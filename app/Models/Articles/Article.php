<?php

namespace App\Models\Articles;

use App\Collections\ArticleCollection;
use App\Models\Cards\Card;
use App\Models\Expansions\Expansion;
use App\Models\Games\Game;
use App\Models\Localizations\Language;
use App\Models\Orders\Order;
use App\Models\Rules\Rule;
use App\Models\Storages\Content;
use App\Models\Storages\Storage;
use App\Transformers\Articles\Csvs\Transformer;
use App\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;

class Article extends Model
{
    const DECIMALS = 6;

    const DEFAULT_CONDITION = 'EX';
    const DEFAULT_LANGUAGE = 1;

    const PROVISION = 0.05;
    const MIN_UNIT_PRICE = 0.02;

    const CSV_CARDMARKET_ARTICLE_ID = 0;

    const CSV_AMOUNT = [
        Game::ID_MAGIC => 14,
        Game::ID_YUGIOH => 13,
        Game::ID_POKEMON=> 15,
    ];

    const BASE_PRICES = [
        'price_sell' => 'Durchschnittlicher Verkaufspreis',
        'price_low' => 'Niedrigster Preis',
        'price_trend' => 'Trend Preis',
    ];

    const CONDITIONS = [
        'MT' => 'Mint',
        'NM' => 'Near Mint',
        'EX' => 'Excelent',
        'GD' => 'Good',
        'LP' => 'Light Played',
        'PL' => 'Played',
        'PO' => 'Poor',
    ];

    protected $appends = [
        'localName',
        'path',
        'provision_formatted',
        'state_icon',
        'state_key',
        'sync_icon',
        'unit_cost_formatted',
        'unit_price_formatted',
        'price_rule_formatted',
    ];

    protected $casts = [
        'unit_cost' => 'decimal:' . self::DECIMALS,
        'unit_price' => 'decimal:' . self::DECIMALS,
        'provision' => 'decimal:' . self::DECIMALS,
    ];

    protected $guarded = [
        'id',
        'state_icon',
        'state_key',
        'sync_icon',
    ];

    protected $dates = [
        'bought_at',
        'exported_at',
        'sold_at',
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
            if (! $model->user_id) {
                $model->user_id = auth()->user()->id;
            }

            if (! $model->language_id) {
                $model->language_id = self::DEFAULT_LANGUAGE;
            }

            if (! $model->condition) {
                $model->condition = self::DEFAULT_CONDITION;
            }

            return true;
        });
    }

    public static function createOrUpdateFromCsv(int $userId, array $row, int $index, int $gameId) : self
    {
        // TODO: get rarity from card

        $values = Transformer::transform($gameId, $row);

        $values['user_id'] = $userId;
        $values['storage_id'] = Content::defaultStorage($userId, $row['expansion_id']);
        $values['unit_cost'] = \App\Models\Items\Card::defaultPrice($userId, '');
        $values['exported_at'] = now();

        $attributes = [
            'cardmarket_article_id' => $row[0],
            'index' => $index,
        ];

        return self::updateOrCreate($attributes, $values);
    }

    public static function reindex(int $cardmarket_article_id, int $start = 1) : int
    {
        $collection = self::where('cardmarket_article_id', $cardmarket_article_id)
            ->whereNull('sold_at')->orderBy('index', 'ASC')->get();

        $i = 0;
        foreach ($collection as $model) {
            $model->update([
                'index' => $start++,
            ]);
            $i++;
        }

        return $i;
    }

    public static function updateOrCreateFromCardmarket(int $userId, array $cardmarketArticle) : self
    {
        $values = [
            'user_id' => $userId,
            'card_id' => $cardmarketOrder['idProduct'],
            'language_id' => $cardmarketArticle['language']['idLanguage'],
            'cardmarket_article_id' => $cardmarketArticle['idArticle'],
            'condition' => $cardmarketArticle['condition'],
            'unit_price' => $cardmarketArticle['price'],
            'unit_cost' => \App\Models\Items\Card::defaultPrice($userId, $cardmarketArticle['product']['rarity']),
            'sold_at' => null,
            'is_in_shoppingcard' => $cardmarketArticle['inShoppingCart'] ?? false,
            'is_foil' => $cardmarketArticle['isFoil'] ?? false,
            'is_signed' => $cardmarketArticle['isSigned'] ?? false,
            'is_altered' => $cardmarketArticle['isAltered'] ?? false,
            'is_playset' => $cardmarketArticle['isPlayset'] ?? false,
            'cardmarket_comments' => $cardmarketArticle['comments'] ?: null,
            'has_sync_error' => false,
            'sync_error' => null,
        ];

        $article = self::updateOrCreate(['cardmarket_article_id' => $cardmarketOrder['idArticle']], $values);

        return $article;
    }

    /**
     * Create a new Eloquent Collection instance.
     *
     * @param  array  $models
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function newCollection(array $models = [])
    {
        return new ArticleCollection($models);
    }

    public function isDeletable() : bool
    {
        return true;
    }

    public function toCardmarket() : array
    {
        $cardmarketArticle = [
            'idLanguage' => $this->language_id,
            'comments' => $this->cardmarket_comments,
            'count' => 1,
            'price' => number_format($this->unit_price, 2),
            'condition' => $this->condition,
            'isFoil' => $this->is_foil ? 'true' : 'false',
            'isSigned' => $this->is_signed ? 'true' : 'false',
            'isPlayset' => $this->is_playset ? 'true' : 'false',
        ];

        if ($this->cardmarket_article_id) {
            $cardmarketArticle['idArticle'] = $this->cardmarket_article_id;
        }
        else {
            $cardmarketArticle['idProduct'] = $this->card->cardmarket_product_id;
        }

        return $cardmarketArticle;
    }

    public function sync() : self
    {
        if ($this->cardmarket_article_id) {
            $this->syncUpdate();
        }
        else {
            $this->syncAdd();
        }

        return $this;
    }

    public function syncAdd()
    {
        $response = $this->user->cardmarketApi->stock->add([$this->toCardmarket()]);
        if ($response['inserted']['success']) {
            $cardmarketArticle = $response['inserted']['idArticle'];
            $this->update([
                'cardmarket_article_id' => $cardmarketArticle['idArticle'],
                'cardmarket_last_edited' => new Carbon($cardmarketArticle['lastEdited']),
                'exported_at' => now(),
                'synced_at' => now(),
                'has_sync_error' => false,
                'sync_error' => null,
                'should_sync' => false,
            ]);
        }
        else {
            $this->update([
                'has_sync_error' => true,
                'sync_error' => $response['inserted']['error'],
                'should_sync' => true,
            ]);
        }
    }

    public function syncUpdate()
    {
        try {
            $response = $this->user->cardmarketApi->stock->update([$this->toCardmarket()]);
            if (is_array($response['updatedArticles'])) {
                $cardmarketArticle = $response['updatedArticles'];
                $this->update([
                    'cardmarket_article_id' => $cardmarketArticle['idArticle'],
                    'cardmarket_last_edited' => new Carbon($cardmarketArticle['lastEdited']),
                    'synced_at' => now(),
                    'has_sync_error' => false,
                    'sync_error' => null,
                    'should_sync' => false,
                ]);
            }
            elseif (is_array($response['notUpdatedArticles'])) {
                $this->update([
                    'has_sync_error' => true,
                    'sync_error' => $response['notUpdatedArticles']['error'],
                    'should_sync' => true,
                ]);
            }
            else {
                $this->update([
                    'has_sync_error' => true,
                    'sync_error' => 'Artikel nicht vorhanden. Alle Artikel synchronisieren.',
                    'should_sync' => true,
                ]);
            }
        }
        catch (\Exception $e) {

            dump('article', $this->toCardmarket());
            dump('response', $response);

            throw $e;
        }
    }

    public function syncDelete() : bool
    {
        if (is_null($this->cardmarket_article_id)) {
            return true;
        }

        $response = $this->user->cardmarketApi->stock->delete([
            'idArticle' => $this->cardmarket_article_id,
            'count' => 1,
        ]);

        if (Arr::has($response['deleted'], 'message')) {
            $this->update([
                'cardmarket_article_id' => null
            ]);

            return true;
        }

        return $response['deleted']['success'];
    }

    public function calculateProvision() : float
    {
        $this->attributes['provision'] = ceil(self::PROVISION * ($this->unit_price * 100)) / 100;

        return $this->attributes['provision'];
    }

    public function setBoughtAtFormattedAttribute($value)
    {
        $this->attributes['bought_at'] = Carbon::createFromFormat('d.m.Y H:i', $value);
        Arr::forget($this->attributes, 'bought_at_formatted');
    }

    public function setConditionAttribute($value)
    {
        $this->attributes['condition'] = $value;
        $this->attributes['condition_sort'] = (int) array_search($value, array_keys(array_reverse(self::CONDITIONS)));
    }

    public function setSoldAtFormattedAttribute($value)
    {
        $this->attributes['sold_at'] = Carbon::createFromFormat('d.m.Y H:i', $value);
        Arr::forget($this->attributes, 'sold_at_formatted');
    }

    public function setUnitCostFormattedAttribute($value)
    {
        $this->unit_cost = number_format(str_replace(',', '.', $value), self::DECIMALS, '.', '');
        Arr::forget($this->attributes, 'unit_cost_formatted');
    }

    public function setUnitPriceFormattedAttribute($value)
    {
        $this->unit_price = number_format(str_replace(',', '.', $value), self::DECIMALS, '.', '');
        Arr::forget($this->attributes, 'unit_price_formatted');
    }

    public function setPriceRuleFormattedAttribute($value)
    {
        $this->price_rule = number_format(str_replace(',', '.', $value), self::DECIMALS, '.', '');
        Arr::forget($this->attributes, 'price_rule_formatted');
    }

    public function setUnitPriceAttribute($value)
    {
        $this->attributes['unit_price'] = $value;

        $this->calculateProvision();
    }

    public function setProvisionFormattedAttribute($value)
    {
        $this->attributes['provision'] = number_format(str_replace(',', '.', $value), self::DECIMALS, '.', '');
        Arr::forget($this->attributes, 'provision_formatted');
    }

    public function getLocalNameAttribute() : string
    {
        return $this->card->localizations()->where('language_id', $this->language_id)->first()->name;
    }

    public function getPathAttribute()
    {
        return '/article/' . $this->id;
    }

    public function getProvisionFormattedAttribute()
    {
        return number_format($this->provision, 2, ',', '');
    }

    public function getStateIconAttribute()
    {
        if (is_null($this->state)) {
            return 'fa-question text-info';
        }

        switch ($this->state) {
            case 0:
                return 'fa-check text-success';
                break;
            case 1:
                return 'fa-exclamation text-danger';
                break;
        }
    }

    public function getSyncIconAttribute()
    {
        if ($this->has_sync_error) {
            return 'fa-exclamation text-danger';
        }

        if (is_null($this->exported_at)) {
            return 'fa-sqare text-danger';
        }

        return 'fa-check text-success';
    }

    public function getStateKeyAttribute()
    {
        return $this->state ?? -1;
    }

    public function getUnitCostFormattedAttribute()
    {
        return number_format($this->unit_cost, 2, ',', '');
    }

    public function getUnitPriceFormattedAttribute()
    {
        return number_format($this->unit_price, 2, ',', '');
    }

    public function getPriceRuleFormattedAttribute()
    {
        return number_format($this->price_rule, 2, ',', '');
    }

    public function card() : BelongsTo
    {
        return $this->belongsTo(Card::class);
    }

    public function language() : BelongsTo
    {
        return $this->belongsTo(Language::class);
    }

    public function orders() : BelongsToMany
    {
        return $this->belongsToMany(Order::class);
    }

    public function rule() : BelongsTo
    {
        return $this->belongsTo(Rule::class, 'rule_id');
    }

    public function storage() : BelongsTo
    {
        return $this->belongsTo(Storage::class);
    }

    public function user() : BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function scopeIsFoil(Builder $query, $value) : Builder
    {
        if (is_null($value)) {
            return $query;
        }

        return $query->where('articles.is_foil', $value);
    }

    public function scopeCondition(Builder $query, $value, $operator = '=') : Builder
    {
        if (is_null($value) || $value == -1) {
            return $query;
        }

        return $query->where('articles.condition_sort', $operator, $value);
    }

    public function scopeExpansion(Builder $query, $value) : Builder
    {
        if (! $value) {
            return $query;
        }

        return $query->where('cards.expansion_id', $value);
    }

    public function scopeGame(Builder $query, $value) : Builder
    {
        if (! $value) {
            return $query;
        }

        return $query->where('cards.game_id', $value);
    }

    public function scopeLanguage(Builder $query, $value) : Builder
    {
        if (! $value) {
            return $query;
        }

        return $query->where('articles.language_id', $value);
    }

    public function scopeRarity(Builder $query, $value) : Builder
    {
        if (! $value) {
            return $query;
        }

        return $query->where('cards.rarity', $value);
    }

    public function scopeRule(Builder $query, $value) : Builder
    {
        if (is_null($value)) {
            return $query->whereNull('articles.rule_id');
        }

        if (! $value) {
            return $query;
        }

        return $query->where('articles.rule_id', $value);
    }

    public function scopeSearch(Builder $query, $value) : Builder
    {
        if (! $value) {
            return $query;
        }

        return $query->join('localizations', function ($join) {
            $join->on('localizations.localizationable_id', '=', 'cards.id');
            $join->where('localizations.localizationable_type', '=', Card::class);
        })
            ->where('localizations.name', 'like', '%' . $value . '%')
            ->groupBy('articles.id');
    }

    public function scopeSold(Builder $query, $value) : Builder
    {
        if ($value == 1) {
            return $query->whereNotNull('sold_at');
        }

        if ($value == 0) {
            return $query->whereNull('sold_at');
        }

        return $query;
    }

    public function scopeUnitPrice(Builder $query, $min, $max) : Builder
    {
        if ($min) {
            $query->where('articles.unit_price', '>=', str_replace(',', '.', $min));
        }

        if ($max) {
            $query->where('articles.unit_price', '<=', str_replace(',', '.', $max));
        }

        return $query;
    }

    public function scopeUnitCost(Builder $query, $min, $max) : Builder
    {
        if ($min) {
            $query->where('articles.unit_cost', '>=', str_replace(',', '.', $min));
        }

        if ($max) {
            $query->where('articles.unit_cost', '<=', str_replace(',', '.', $max));
        }

        return $query;
    }

    public function scopeSync(Builder $query, $value) : Builder
    {
        if ($value == -1 || is_null($value)) {
            return $query;
        }

        return $query->where('articles.has_sync_error', $value);
    }
}
