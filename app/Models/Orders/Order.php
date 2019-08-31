<?php

namespace App\Models\Orders;

use App\Models\Articles\Article;
use App\Models\Items\Item;
use App\Models\Items\Transactions\Sale;
use App\Models\Items\Transactions\Transaction;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    protected $guarded = [
        'id',
    ];

    public function findItems()
    {
        foreach (Item::where('user_id', $this->user_id)->get() as $key => $item) {
            $quantity = $item->quantity($this);
            if ($quantity == 0) {
                continue;
            }

            $this->sales()->create([
                'item_id' => $item->id,
                'quantity' => $quantity,
                'type' => Sale::class,
                'unit_cost' => $item->unit_cost,
                'user_id' => $this->user_id,
                'at' => now(),
            ]);
        }
    }

    protected function calculateProfits() : self
    {
        $provision = $this->calculateProvision();
        $itemsCost = $this->calculateItemsCost();
        $cardsProfit = $this->calculateCardsProfit();
        $shipmentProfit = $this->calculateShipmentProfit();

        $this->attributes['cost'] = $provision + $itemsCost + $this->cards_cost + $this->shipment_cost;
        $this->attributes['profit'] = $cardsProfit + $shipmentProfit;

        return $this;
    }

    protected function calculateProvision() : float
    {
        return 1;
    }

    protected function calculateItemCosts() : float
    {
        return 1;
    }

    protected function calculateCardsProfit() : float
    {
        return 1;
    }

    protected function calculateShipmentProfit() : float
    {
        return 1;
    }

    public function sales() : HasMany
    {
        return $this->hasMany(Sale::class, 'order_id');
    }

    public function articles() : HasMany
    {
        return $this->hasMany(Article::class, 'order_id');
    }
}
