<?php

namespace App\Support\Users;

use App\Models\Apis\Api;
use App\Models\Articles\Article;
use App\Models\Expansions\Expansion;
use App\Models\Orders\Order;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Storage;

class CardmarketApi
{
    protected $api;
    protected $cardmarketApi;
    protected $section;

    public function __construct(Api $api)
    {
        $this->setCardmarketApi($api);
    }

    public function setCardmarketApi(Api $api)
    {
        $this->api = $api;
        $this->cardmarketApi = App::make('CardmarketApi', [
            'api' => $api,
        ]);
    }

    public function __get(string $name) : self
    {
        $this->section = $name;

        return $this;
    }

    public function __call($function, $args)
    {
        $section = $this->section;
        // dump($section, $function, $args);
        try {
            return call_user_func_array([$this->cardmarketApi->$section, $function], $args);
        }
        catch (\Exception $exc) {
            // $this->refresh();
        }
    }

    public function syncAllArticles()
    {
        $userId = $this->api->user_id;
        $filename = $userId . '-stock.csv';
        $zippedFilename = $filename . '.gz';

        $data = $this->cardmarketApi->stock->csv();
        $created = Storage::disk('local')->put($zippedFilename, base64_decode($data['stock']));

        if ($created === false) {
            return;
        }

        shell_exec('gunzip ' . storage_path('app/' . $filename));

        $expansions = Expansion::all()->keyBy('abbreviation');

        $row_count = 0;
        $articlesFile = fopen(storage_path('app/' . $filename), "r");
        while (($data = fgetcsv($articlesFile, 2000, ";")) !== FALSE) {
            if ($row_count == 0) {
                $row_count++;
                continue;
            }
            $data['expansion_id'] = $expansions[$data[4]]->id;
            $amount = $data[Article::CSV_AMOUNT];
            $cardmarket_article_id = $data[Article::CSV_CARDMARKET_ARTICLE_ID];
            for ($i = 1; $i <= $amount; $i++) {
                Article::reindex($cardmarket_article_id);
                Article::createOrUpdateFromCsv($userId, $data, $i);
                Article::where('cardmarket_article_id', $cardmarket_article_id)
                    ->whereNull('sold_at')
                    ->where('index', '>', $amount)
                    ->delete();
            }
            $row_count++;
        }

        // Storage::disk('local')->delete($filename);

    }

    public function syncAllSellerOrders()
    {
        $states = [
            // 'bought',
            'paid',
            'sent',
            'received',
            'lost',
            'cancelled',
        ];

        foreach ($states as $state) {
            $this->syncOrders('seller', $state);
            usleep(100);
        }
    }

    public function syncOrders(string $actor, string $state)
    {
        $userId = $this->api->user_id;
        $cardmarketOrders_count = 0;
        $start = 1;
        do {
            $data = $this->cardmarketApi->order->find($actor, $state, $start);
            if (is_array($data)) {
                $data_count = count($data['order']);
                $cardmarketOrders_count += $data_count;
                foreach ($data['order'] as $cardmarketOrder) {
                    Order::updateOrCreateFromCardmarket($userId, $cardmarketOrder);
                }
                $start += 100;
                if ($data_count < 100) {
                    $data = null;
                }
                usleep(50);
            }
        }
        while (! is_null($data));
    }

    public function refresh()
    {
        try {
            $access = $this->cardmarketApi->access->token($this->api->accessdata['request_token']);
            $this->api->setAccessToken($request_token, $access['oauth_token'], $access['oauth_token_secret']);
            $this->setCardmarketApi($this->api);
        }
        catch (\Exception $exc) {
            $this->api->reset();
        }

    }
}