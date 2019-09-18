<?php

namespace App\Http\Controllers\Articles;

use App\Http\Controllers\Controller;
use App\Models\Articles\Article;
use App\Models\Cards\Card;
use App\Models\Expansions\Expansion;
use App\Models\Items\Card as ItemCard;
use App\Models\Localizations\Language;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ArticleController extends Controller
{
    protected $baseViewPath = 'article';

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if ($request->wantsJson()) {
            return auth()->user()->articles()
                ->select('articles.*')
                ->join('cards', 'cards.id', 'articles.card_id')
                ->search($request->input('searchtext'))
                ->with([
                    'card.expansion',
                    'language',
                    'order',
                ])
                ->orderBy('cards.name', 'ASC')
                ->paginate();
        }

        $languages = Language::all()->mapWithKeys(function ($item) {
            return [$item['id'] => $item['name']];
        });

        return view($this->baseViewPath . '.index')
            ->with('conditions', Article::CONDITIONS)
            ->with('languages', $languages);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $user = auth()->user();
        $defaultCardCosts = ItemCard::defaultCosts($user);

        $expansions = Expansion::orderBy('name', 'ASC')->get()->mapWithKeys(function ($item) {
            return [$item['id'] => $item['name']];
        });

        $languages = Language::all()->mapWithKeys(function ($item) {
            return [$item['id'] => $item['name']];
        });

        return view($this->baseViewPath . '.create')
            ->with('conditions', Article::CONDITIONS)
            ->with('defaultCardCosts', $defaultCardCosts)
            ->with('expansions', $expansions)
            ->with('languages', $languages);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $article = Article::create($request->validate([
            'card_id' => 'required|integer',
            'cardmarket_comments' => 'sometimes|nullable|string',
            'language_id' => 'sometimes|required|integer',
            'condition' => 'sometimes|required|string',
            // 'bought_at_formatted' => 'required|date_format:"d.m.Y H:i"',
            // 'sold_at_formatted' => 'required|date_format:"d.m.Y H:i"',
            'is_foil' => 'sometimes|required|boolean',
            'is_signed' => 'sometimes|required|boolean',
            'is_playset' => 'sometimes|required|boolean',
            'unit_price_formatted' => 'sometimes|required|formated_number',
            'unit_cost_formatted' => 'sometimes|required|formated_number',
        ]));

        if ($request->input('sync')) {
            $article->syncAdd();
        }

        return $article;
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Articles\Article  $article
     * @return \Illuminate\Http\Response
     */
    public function show(Article $article)
    {
        return view($this->baseViewPath . '.show')
            ->with('model', $article);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Articles\Article  $article
     * @return \Illuminate\Http\Response
     */
    public function edit(Article $article)
    {
        return view($this->baseViewPath . '.edit')
            ->with('model', $article);
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
        $article->update($request->validate([
            'cardmarket_comments' => 'sometimes|nullable|string',
            'language_id' => 'sometimes|required|integer',
            'condition' => 'sometimes|required|string',
            // 'bought_at_formatted' => 'required|date_format:"d.m.Y H:i"',
            // 'sold_at_formatted' => 'required|date_format:"d.m.Y H:i"',
            'is_foil' => 'sometimes|required|boolean',
            'is_signed' => 'sometimes|required|boolean',
            'is_playset' => 'sometimes|required|boolean',
            'unit_price_formatted' => 'sometimes|required|formated_number',
            'unit_cost_formatted' => 'sometimes|required|formated_number',
            'provision_formatted' => 'sometimes|required|formated_number',
            'state' => 'sometimes|required|integer',
            'state_comments' => 'sometimes|nullable|string',
        ]));

        if ($article->order_id) {
            $article->order->calculateProfits()
                ->save();
        }

        if ($request->input('sync')) {
            $article->sync();
        }

        return $article->load([
            'card.expansion',
            'card.localizations',
            'language',
            'order',
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Articles\Article  $article
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, Article $article)
    {
        if ($isDeletable = $article->isDeletable()) {
            if ($isDeletable = $article->syncDelete()) {
                $article->delete();
            }
        }

        if ($request->wantsJson())
        {
            return [
                'deleted' => $isDeletable,
            ];
        }

        return back();
    }
}
