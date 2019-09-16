<?php

namespace App\Console\Commands\Article;

use App\Models\Articles\Article;
use App\Models\Cards\Card;
use App\User;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Arr;

class AddSetCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'article:addSet';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    protected $user;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $cards = Card::where('expansion_id', 250)->get();
        $this->user = User::first();
        $cardDefaultPrices = \App\Models\Items\Card::where('user_id', $this->user->id)->get()->mapWithKeys(function ($item) {
            return [$item['name'] => $item['unit_cost']];
        });

        // $this->truncateStock();

        $articles = [];
        $cardmarketArticles = [];
        foreach ($cards as $card) {
            $unit_cost = Arr::get($cardDefaultPrices, $card->rarity, 0.02);
            $article = Article::create([
                'user_id' => $this->user->id,
                'card_id' => $card->id,
                'language_id' => 3,
                'condition' => 'NM',
                'unit_price' => $unit_cost + 1,
                'unit_cost' => $unit_cost,
            ]);
            $articles[] = $article;
            $cardmarketArticles[] = $article->toCardmarket();
        }

        $cardmarketArticles = $this->user->cardmarketApi->stock->add($cardmarketArticles);
        foreach ($cardmarketArticles['inserted'] as $key => $cardmarketArticle) {
            if ($cardmarketArticle['success']) {
                $articles[$key]->update([
                    'cardmarket_article_id' => $cardmarketArticle['idArticle']['idArticle'],
                    'cardmarket_last_edited' => new Carbon($cardmarketArticle['idArticle']['lastEdited']),
                ]);
            }
        }

        // $article::toCardmarket();
        // $cardmarketArticles = Article::toCardmarket($articles);
        // Grenze bei 100?
        // $response = $this->user->cardmarketApi->stock->add($cardmarketArticles);
        // dump($response);
    }

    protected function truncateStock()
    {
        Article::where('user_id', $this->user->id)
            ->whereNull('sold_at')
            ->delete();

        $stocks = $this->user->cardmarketApi->stock->get();
        if (count($stocks['article'])) {
            foreach ($stocks['article'] as $key => $stock) {
                $articles[] = [
                    'idArticle' => $stock['idArticle'],
                    'count' => $stock['count'],
                ];
            }
            $data = $this->user->cardmarketApi->stock->delete($articles);
        }
    }
}
