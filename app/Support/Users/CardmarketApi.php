<?php

namespace App\Support\Users;

use App\Models\Apis\Api;
use App\Models\Orders\Order;
use Illuminate\Support\Facades\App;

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

    public function syncAllSellerOrders()
    {
        $states = [
            'bought',
            'paid',
            'sent',
            'received',
            'lost',
            'cancelled',
        ];

        foreach ($states as $state) {
            $this->syncOrders('seller', $state);
        }
    }

    public function syncOrders(string $actor, string $state)
    {
        $userId = $this->api->user_id;
        $cardmarketOrders = [];
        $start = 1;
        do {
            $data = $this->cardmarketApi->order->find($actor, $state, $start);
            if (is_array($data)) {
                $data_count = count($data['order']);
                foreach ($data['order'] as $cardmarketOrder) {
                    $order = Order::updateOrCreateFromCardmarket($userId, $cardmarketOrder);
                }
                $start += 100;
                if ($data_count == 0) {
                    $data = null;
                }
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