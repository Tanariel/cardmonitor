<?php

namespace App\Http\Controllers\Articles\Stock;

use App\Http\Controllers\Controller;
use App\Models\Articles\Article;
use App\Models\Cards\Card;
use App\Models\Expansions\Expansion;
use App\Models\Games\Game;
use App\Models\Localizations\Language;
use Illuminate\Http\Request;

class StockController extends Controller
{
    protected $baseViewPath = 'article.stock';

    public function index(Request $request)
    {
        $user = auth()->user();

        if ($request->wantsJson()) {
            return $user->articles()
                ->stock()
                ->join('cards', 'cards.id', 'articles.card_id')
                ->expansion($request->input('expansion_id'))
                ->game($request->input('game_id'))
                ->isFoil($request->input('is_foil'))
                ->language($request->input('language_id'))
                ->rarity($request->input('rarity'))
                ->search($request->input('searchtext'))
                ->sold(0)
                ->with([
                    'card.expansion',
                    'language',
                ])
                ->orderBy('cards.name', 'ASC')
                ->paginate();
        }

        $languages = Language::all()->mapWithKeys(function ($item) {
            return [$item['id'] => $item['name']];
        });

        return view($this->baseViewPath . '.index')
            ->with('conditions', Article::CONDITIONS)
            ->with('expansions', Expansion::all())
            ->with('games', Game::keyValue())
            ->with('languages', $languages)
            ->with('rarities', Card::RARITIES)
            ->with('is_applying_rules', $user->is_applying_rules)
            ->with('is_syncing_articles', $user->is_syncing_articles);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Articles\Article  $article
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Article $article)
    {
        $request->validate([
            'amount' => 'required|integer|min:1',
            'sync' => 'required|boolean',
        ]);

        $article->setAmount($request->input('amount'), $request->input('sync'));

        $article = Article::where('user_id', $article->user_id)
            ->where('cardmarket_article_id', $article->cardmarket_article_id)
            ->with([
                'card.expansion',
                'card.localizations',
                'language',
            ])->first();

        $article->append('amount');

        return $article;
    }
}
