<?php

namespace App\Models\Rules;

use App\Models\Articles\Article;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;

class Rule extends Model
{
    const DECIMALS = 6;

    protected $appends = [
        'base_price_formatted',
        'editPath',
        'isDeletable',
        'min_price_common_formatted',
        'min_price_land_formatted',
        'min_price_masterpiece_formatted',
        'min_price_mythic_formatted',
        'min_price_rare_formatted',
        'min_price_special_formatted',
        'min_price_time_shifted_formatted',
        'min_price_tip_card_formatted',
        'min_price_token_formatted',
        'min_price_uncommon_formatted',
        'multiplier_formatted',
        'path',
        'price_above_formatted',
        'price_below_formatted',
    ];

    protected $guarded = [];

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

            if (! $model->base_price) {
                $model->base_price = 'price_trend';
            }

            $model->order_column = self::nextOrderColumn($model->user_id);

            return true;
        });

        static::deleting(function($model)
        {
            $model->articles()->update([
                'rule_id' => null,
                'rule_price' => null,
            ]);
        });
    }

    public static function nextOrderColumn(int $userId)
    {
        return self::where('user_id', $userId)->max('order_column') + 1;
    }

    public function apply(bool $sync = false)
    {
        // TODO: Update Article with rule price
        $query = Article::join('cards', 'cards.id', '=', 'articles.card_id')
            ->whereNull('articles.rule_id')
            ->whereNull('order_id')
            ->where('articles.unit_price', '>=', $this->price_above)
            ->where('articles.unit_price', '<=', $this->price_below);

        if ($this->expansion_id) {
            $query->where('cards.expansion_id', $this->expansion_id);
        }

        if ($this->rarity) {
            $query->where('cards.rarity', $this->rarity);
        }

        $attributes = [
            'articles.rule_id' => $this->id,
            'articles.rule_applied_at' => now(),
            'articles.rule_price' => DB::raw('(cards.' . $this->base_price . ' * ' . $this->multiplier . ')'),
        ];

        if ($sync) {
            $attributes['unit_price'] = DB::raw('(cards.' . $this->base_price . ' * ' . $this->multiplier . ')');
        }

        $query->update($attributes);
    }

    public static function reset(int $userId)
    {
        Article::where('user_id', $userId)
            ->update([
                'rule_id' => null,
                'rule_price' => null,
                'rule_applied_at' => null,
            ]);
    }

    public function activate() : self {
        $this->active = true;

        return $this;
    }

    public function deactivate() : self {
        $this->active = false;

        return $this;
    }

    public function isActivated() : bool
    {
        return $this->active;
    }

    public function isDeletable() : bool
    {
        return true;
    }

    public function getBasePriceFormattedAttribute()
    {
        return Arr::get(Article::BASE_PRICES, $this->base_price, 'Preis');
    }

    public function setMultiplierFormattedAttribute($value)
    {
        $this->multiplier = number_format(str_replace(',', '.', $value), self::DECIMALS, '.', '');
        Arr::forget($this->attributes, 'multiplier_formatted');
    }

    public function getMultiplierFormattedAttribute()
    {
        return number_format($this->multiplier, 2, ',', '');
    }

    public function setPriceAboveFormattedAttribute($value)
    {
        $this->price_above = number_format(str_replace(',', '.', $value), self::DECIMALS, '.', '');
        Arr::forget($this->attributes, 'price_above_formatted');
    }

    public function getPriceAboveFormattedAttribute()
    {
        return number_format($this->price_above, 2, ',', '');
    }

    public function setPriceBelowFormattedAttribute($value)
    {
        $this->price_below = number_format(str_replace(',', '.', $value), self::DECIMALS, '.', '');
        Arr::forget($this->attributes, 'price_below_formatted');
    }

    public function getPriceBelowFormattedAttribute()
    {
        return number_format($this->price_below, 2, ',', '');
    }

    public function setMinPriceMasterpieceFormattedAttribute($value)
    {
        $this->min_price_masterpiece = number_format(str_replace(',', '.', $value), self::DECIMALS, '.', '');
        Arr::forget($this->attributes, 'min_price_masterpiece_formatted');
    }

    public function getMinPriceMasterpieceFormattedAttribute()
    {
        return number_format($this->min_price_masterpiece, 2, ',', '');
    }

    public function setMinPriceMythicFormattedAttribute($value)
    {
        $this->min_price_mythic = number_format(str_replace(',', '.', $value), self::DECIMALS, '.', '');
        Arr::forget($this->attributes, 'min_price_mythic_formatted');
    }

    public function getMinPriceMythicFormattedAttribute()
    {
        return number_format($this->min_price_mythic, 2, ',', '');
    }

    public function setMinPriceRareFormattedAttribute($value)
    {
        $this->min_price_rare = number_format(str_replace(',', '.', $value), self::DECIMALS, '.', '');
        Arr::forget($this->attributes, 'min_price_rare_formatted');
    }

    public function getMinPriceRareFormattedAttribute()
    {
        return number_format($this->min_price_rare, 2, ',', '');
    }

    public function setMinPriceSpecialFormattedAttribute($value)
    {
        $this->min_price_special = number_format(str_replace(',', '.', $value), self::DECIMALS, '.', '');
        Arr::forget($this->attributes, 'min_price_special_formatted');
    }

    public function getMinPriceSpecialFormattedAttribute()
    {
        return number_format($this->min_price_special, 2, ',', '');
    }

    public function setMinPriceTimeShiftedFormattedAttribute($value)
    {
        $this->min_price_time_shifted = number_format(str_replace(',', '.', $value), self::DECIMALS, '.', '');
        Arr::forget($this->attributes, 'min_price_time_shifted_formatted');
    }

    public function getMinPriceTimeShiftedFormattedAttribute()
    {
        return number_format($this->min_price_time_shifted, 2, ',', '');
    }

    public function setMinPriceUncommonFormattedAttribute($value)
    {
        $this->min_price_uncommon = number_format(str_replace(',', '.', $value), self::DECIMALS, '.', '');
        Arr::forget($this->attributes, 'min_price_uncommon_formatted');
    }

    public function getMinPriceUncommonFormattedAttribute()
    {
        return number_format($this->min_price_uncommon, 2, ',', '');
    }

    public function setMinPriceCommonFormattedAttribute($value)
    {
        $this->min_price_common = number_format(str_replace(',', '.', $value), self::DECIMALS, '.', '');
        Arr::forget($this->attributes, 'min_price_common_formatted');
    }

    public function getMinPriceCommonFormattedAttribute()
    {
        return number_format($this->min_price_common, 2, ',', '');
    }

    public function setMinPriceLandFormattedAttribute($value)
    {
        $this->min_price_land = number_format(str_replace(',', '.', $value), self::DECIMALS, '.', '');
        Arr::forget($this->attributes, 'min_price_land_formatted');
    }

    public function getMinPriceLandFormattedAttribute()
    {
        return number_format($this->min_price_land, 2, ',', '');
    }

    public function setMinPriceTokenFormattedAttribute($value)
    {
        $this->min_price_token = number_format(str_replace(',', '.', $value), self::DECIMALS, '.', '');
        Arr::forget($this->attributes, 'min_price_token_formatted');
    }

    public function getMinPriceTokenFormattedAttribute()
    {
        return number_format($this->min_price_token, 2, ',', '');
    }

    public function setMinPriceTipCardFormattedAttribute($value)
    {
        $this->min_price_tip_card = number_format(str_replace(',', '.', $value), self::DECIMALS, '.', '');
        Arr::forget($this->attributes, 'min_price_tip_card_formatted');
    }

    public function getMinPriceTipCardFormattedAttribute()
    {
        return number_format($this->min_price_tip_card, 2, ',', '');
    }

    public function getPathAttribute()
    {
        return $this->path('show');
    }

    public function getEditPathAttribute()
    {
        return $this->path('edit');
    }

    protected function path(string $action = '') : string
    {
        return route($this->baseRoute() . '.' . $action, ['rule' => $this->id]);
    }

    protected function baseRoute() : string
    {
        return 'rule';
    }


    public function getIsDeletableAttribute()
    {
        return $this->isDeletable();
    }

    public function articles() : HasMany
    {
        return $this->hasMany(Article::class);
    }
}
