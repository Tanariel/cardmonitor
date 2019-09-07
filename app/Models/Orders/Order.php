<?php

namespace App\Models\Orders;

use App\Models\Articles\Article;
use App\Models\Cards\Card;
use App\Models\Items\Item;
use App\Models\Items\Transactions\Sale;
use App\Models\Items\Transactions\Transaction;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Arr;

class Order extends Model
{
    const SHIPPING_PROFITS = [
        'Standardbrief' => 0.3,
        'Standardbrief International' => 0.3,
        'Kompaktbrief' => 0.3,
        'Kompaktbrief International' => 0.3,
        'Grossbrief' => 0.5,
        'Grossbrief International' => 0.5,
    ];

    protected $dates = [
        'bought_at',
        'canceled_at',
        'paid_at',
        'received_at',
        'sent_at',
    ];

    protected $cardDefaultPrices;

    protected $guarded = [
        'id',
    ];

    /**
     * The booting method of the model.
     *
     * @return void
     */
    public static function boot()
    {
        parent::boot();

        static::creating(function($model)
        {
            return true;
        });
    }

    public static function updateOrCreateFromCardmarket(int $userId, array $cardmarketOrder) : self
    {
        $values = [
            'shipping_method_id' => $cardmarketOrder['shippingMethod']['idShippingMethod'],
            'cardmarket_buyer_id' => $cardmarketOrder['buyer']['idUser'],
            'state' => $cardmarketOrder['state']['state'],
            'shippingmethod' => $cardmarketOrder['shippingMethod']['name'],
            'shipment_revenue' => $cardmarketOrder['shippingMethod']['price'],
            'articles_count' => $cardmarketOrder['articleCount'],
            'articles_revenue' => $cardmarketOrder['articleValue'],
            'revenue' => $cardmarketOrder['totalValue'],
            'user_id' => $userId,
            'bought_at' => (Arr::has($cardmarketOrder['state'], 'dateBought') ? new Carbon($cardmarketOrder['state']['dateBought']) : null),
            'canceled_at' => (Arr::has($cardmarketOrder['state'], 'dateCanceled') ? new Carbon($cardmarketOrder['state']['dateCanceled']) : null),
            'paid_at' => (Arr::has($cardmarketOrder['state'], 'datePaid') ? new Carbon($cardmarketOrder['state']['datePaid']) : null),
            'received_at' => (Arr::has($cardmarketOrder['state'], 'dateReceived') ? new Carbon($cardmarketOrder['state']['dateReceived']) : null),
            'sent_at' => (Arr::has($cardmarketOrder['state'], 'dateSent') ? new Carbon($cardmarketOrder['state']['dateSent']) : null),
        ];
        if (Arr::has($cardmarketOrder, 'evaluation')) {
            $values['evaluation_grade'] = $cardmarketOrder['evaluation']['evaluationGrade'];
            $values['evaluation_item_description'] = $cardmarketOrder['evaluation']['itemDescription'];
            $values['evaluation_packaging'] = $cardmarketOrder['evaluation']['packaging'];
            $values['evaluation_comment'] = $cardmarketOrder['evaluation']['comment'];
        }

        $order = self::updateOrCreate(['cardmarket_order_id' => $cardmarketOrder['idOrder']], $values);
        if ($order->wasRecentlyCreated) {
            $order->findItems();
            $order->addArticlesFromCardmarket($cardmarketOrder);
        }
        $order->calculateProfits()
            ->save();

        return $order;
    }

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

    protected function getCardDefaultPrices() : Collection
    {
        if (! is_null($this->cardDefaultPrices)) {
            return $this->cardDefaultPrices;
        }

        $this->cardDefaultPrices = \App\Models\Items\Card::where('user_id', $this->user_id)->get()->mapWithKeys(function ($item) {
            return [$item['name'] => $item['unit_cost']];
        });

        return $this->cardDefaultPrices;
    }

    public function addArticlesFromCardmarket(array $cardmarketOrder)
    {
        $this->getCardDefaultPrices();

        foreach ($cardmarketOrder['article'] as $cardmarketArticle) {
            // dump($cardmarketArticle);
            $this->addArticleFromCardmarket($cardmarketArticle);
        }
    }

    protected function addArticleFromCardmarket(array $cardmarketArticle)
    {
        // Card vorhanden?
        // Article finden, wenn nicht vorhanden
        $articles = Article::where('order_id', $this->id)
            ->where('cardmarket_article_id', $cardmarketArticle['idArticle'])
            ->where('user_id', $this->user_id)
            ->limit($cardmarketArticle['count'])
            ->get();
        $articles_count = count($articles);
        $articles_left_count = ($cardmarketArticle['count'] - $articles_count);
        if ($articles_left_count == 0) {
            return;
        }

        // Article mit cardmarket_article_id
        $articles_count = Article::where('articles.user_id', $this->user_id)
            ->whereNull('order_id')
            ->where('articles.cardmarket_article_id', $cardmarketArticle['idArticle'])
            ->where('articles.language_id', $cardmarketArticle['language']['idLanguage'])
            ->where('articles.condition', $cardmarketArticle['condition'])
            ->where('articles.unit_price', $cardmarketArticle['price'])
            ->limit($articles_left_count)
            ->update([
                'articles.order_id' => $this->id,
                'articles.sold_at' => $this->paid_at,
            ]);
        $articles_left_count -= $articles_count;
        if ($articles_left_count == 0) {
            return;
        }

        $articles_count = Article::join('cards', 'cards.id', '=', 'articles.card_id')
            ->where('articles.user_id', $this->user_id)
            ->whereNull('order_id')
            ->where('cards.cardmarket_product_id', $cardmarketArticle['idProduct'])
            ->where('articles.language_id', $cardmarketArticle['language']['idLanguage'])
            ->where('articles.condition', $cardmarketArticle['condition'])
            ->where('articles.unit_price', $cardmarketArticle['price'])
            ->limit($articles_left_count)
            ->update([
                'articles.order_id' => $this->id,
                'articles.cardmarket_article_id' => $cardmarketArticle['idArticle'],
                'articles.sold_at' => $this->paid_at,
            ]);
        $articles_left_count -= $articles_count;
        if ($articles_left_count == 0) {
            return;
        }

        $attributes = [
            'user_id' => $this->user_id,
            'card_id' => Card::where('cardmarket_product_id', $cardmarketArticle['idProduct'])->first()->id,
            'language_id' => $cardmarketArticle['language']['idLanguage'],
            'cardmarket_article_id' => $cardmarketArticle['idArticle'],
            'condition' => $cardmarketArticle['condition'],
            'unit_price' => $cardmarketArticle['price'],
            'unit_cost' => $this->cardDefaultPrices[$cardmarketArticle['product']['rarity']], // Berechnen
            'sold_at' => $this->paid_at // "2019-08-30T10:59:53+0200"
        ];
        foreach (range($articles_count, ($articles_left_count - 1)) as $value) {
            $this->articles()->create($attributes);
        }
    }

    public function calculateProfits() : self
    {
        $provision = $this->calculateProvision();
        $itemsCost = $this->calculateItemsCost();
        $articlesProfit = $this->calculateArticlesProfit();
        $shipmentProfit = $this->calculateShipmentProfit();

        $this->attributes['cost'] = $provision + $itemsCost + $this->cards_cost + $this->shipment_cost;
        $this->attributes['profit'] = $articlesProfit + $shipmentProfit;

        return $this;
    }

    protected function calculateProvision() : float
    {
        $this->attributes['provision'] = $this->articles->sum('provision');

        return $this->attributes['provision'];
    }

    protected function calculateItemsCost() : float
    {
        $this->attributes['items_cost'] = $this->sales->sum( function ($sale) {
            return ($sale->quantity * $sale->unit_cost);
        });

        return $this->attributes['items_cost'];
    }

    protected function calculateArticlesProfit() : float
    {
        $this->attributes['articles_cost'] = $this->articles->sum('unit_cost');
        $this->attributes['articles_profit'] = ($this->attributes['articles_revenue'] - $this->attributes['articles_cost']);

        return $this->attributes['articles_profit'];
    }

    protected function calculateShipmentProfit() : float
    {
        $this->attributes['shipment_profit'] = Arr::get(self::SHIPPING_PROFITS, $this->attributes['shippingmethod'], 0.3);
        $this->attributes['shipment_cost'] = $this->attributes['shipment_revenue'] - $this->attributes['shipment_profit'];

        return $this->attributes['shipment_profit'];
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
