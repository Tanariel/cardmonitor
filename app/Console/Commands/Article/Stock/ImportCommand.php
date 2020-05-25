<?php

namespace App\Console\Commands\Article\Stock;

use App\Models\Articles\Article;
use App\Transformers\Articles\Csvs\Transformer;
use App\User;
use Illuminate\Console\Command;
use Illuminate\Http\File;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Storage;

class ImportCommand extends Command
{
    protected $cardmarketArticleIds = [];

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'article:stock:import {user} {game=1}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Imports the stock from a csv file';

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
        $user = User::find($this->argument('user'));
        $gameId = $this->argument('game');

        $importStock = $this->importStock($user->id, $gameId);
        $cardmarketStock = $this->cardmarketStock($user, $gameId);


        foreach ($importStock as $card_id => $articles) {

            $cardmarketCard = Arr::get($cardmarketStock, $card_id, []);

            foreach ($articles as $key => $article) {
                $cardmarketArticle = $this->findCardmarketArticle($cardmarketCard, $article);

                if (empty($cardmarketArticle)) {
                    $this->addArticle($user, $article);
                    continue;
                }

                Arr::forget($this->cardmarketArticleIds, $cardmarketArticle['cardmarket_article_id']);
                $this->setAmount($user, $cardmarketArticle['cardmarket_article_id'], ($article['amount'] - $cardmarketArticle['amount']));

            }
        }

        foreach ($this->cardmarketArticleIds as $cardmarket_article_id => $cardmarketArticle) {
            $user->cardmarketApi->stock->delete($cardmarketArticle);
        }

        $this->call('article:sync', [
            'user' => $user->id,
        ]);

    }

    protected function addArticle(User $user, array $article) : void
    {
        $unit_price = Arr::get($article, 'unit_price');
        if ($unit_price == 0) {
            return;
        }

        $response = $user->cardmarketApi->stock->add([
            'idProduct' => $article['card_id'],
            'idLanguage' => $article['language_id'],
            'comments' => '',
            'count' => $article['amount'],
            'price' => number_format($unit_price, 2),
            'condition' => $article['condition'],
            'isFoil' => Arr::get($article, 'is_foil', false) ? 'true' : 'false',
            'isSigned' => Arr::get($article, 'is_signed', false) ? 'true' : 'false',
            'isPlayset' => Arr::get($article, 'is_playset', false) ? 'true' : 'false',
        ]);
    }

    protected function setAmount(User $user, int $cardmarket_article_id, int $difference) : void
    {
        if ($difference == 0) {
            return;
        }

        $quantity = [
            'idArticle' => $cardmarket_article_id,
            'amount' => abs($difference)
        ];

        if ($difference > 0) {
            $response = $user->cardmarketApi->stock->increase($quantity);
        }

        if ($difference < 0) {
            $response = $user->cardmarketApi->stock->decrease($quantity);
        }
    }

    protected function findCardmarketArticle(array $cardmarketCard, array $article) : array
    {
        $cardmarketArticle = array_filter($cardmarketCard, function ($card) use ($article) {
            if ($card['language_id'] != $article['language_id']) {
                return false;
            }

            if ($article['unit_price'] && $card['unit_price'] != $article['unit_price']) {
                return false;
            }

            if ($card['condition'] != $article['condition']) {
                return false;
            }

            if ($card['is_foil'] != $article['is_foil']) {
                return false;
            }

            if ($card['is_altered'] != $article['is_altered']) {
                return false;
            }

            return true;
        });

        return Arr::get($cardmarketArticle, 0, []);
    }

    protected function importStock(int $userId, int $gameId) : array
    {
        $importStock = [];

        $path = Storage::path($userId . '-stockimport-' . $gameId . '.csv');
        $file = new \SplFileObject($path);
        $file->setCsvControl(';');
        $file->setFlags(\SplFileObject::SKIP_EMPTY | \SplFileObject::DROP_NEW_LINE);
        $rows_count = 0;
        while ($file->valid()) {

            $row = $file->fgetcsv();

            if ($file->key() == 0 || is_null($row)) {
                continue;
            }

            $attributes = Article::localCardIdToAttributes(Arr::get($row, 0));
            $attributes['condition'] = Arr::get($row, 1) ?: 'NM';
            $attributes['amount'] = Arr::get($row, 2);
            $attributes['unit_price'] = str_replace(',', '.', Arr::get($row, 3, 0));
            $importStock[$attributes['card_id']][] = $attributes;

            $rows_count++;
        }

        return $importStock;
    }

    protected function cardmarketStock(User $user, int $gameId) : array
    {
        $cardmarketStock = [];

        $filename = $user->cardmarketApi->downloadStockFile($user->id, $gameId);

        if (empty($filename)) {
            return [];
        }

        $cardmarketStock = [];

        $path = Storage::path($filename);
        $file = new \SplFileObject($path);
        $file->setCsvControl(';');
        $file->setFlags(\SplFileObject::SKIP_EMPTY | \SplFileObject::DROP_NEW_LINE);
        while (! $file->eof()) {
            $row = $file->fgetcsv();
            if (is_null($row)) {
                continue;
            }
            $attributes = Transformer::transform($gameId, $row);
            $cardmarketStock[$attributes['card_id']][] = $attributes;
            $this->cardmarketArticleIds[$attributes['cardmarket_article_id']] = [
                'idArticle' => $attributes['cardmarket_article_id'],
                'count' => $attributes['amount'],
            ];
        }

        Storage::disk('local')->delete($filename);

        return $cardmarketStock;
    }
}
