<?php

namespace App\Models\Articles;

use App\Models\Cards\Card;
use App\Models\Localizations\Language;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Arr;

class Article extends Model
{
    const DECIMALS = 6;

    const DEFAULT_CONDITION = 'EX';
    const DEFAULT_LANGUAGE = 1;

    const PROVISION = 0.05;
    const MIN_UNIT_PRICE = 0.02;

    protected $appends = [
        'localName',
        'provision_formatted',
        'state_icon',
        'state_key',
        'unit_cost_formatted',
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

    public function isDeletable() : bool
    {
        return true;
    }

    protected function calculateProvision() : float
    {
        $this->attributes['provision'] = max(0.01, self::PROVISION * $this->unit_price);

        return $this->attributes['provision'];
    }

    public function setBoughtAtFormattedAttribute($value)
    {
        $this->attributes['bought_at'] = Carbon::createFromFormat('d.m.Y H:i', $value);
        Arr::forget($this->attributes, 'bought_at_formatted');
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

    public function getStateKeyAttribute()
    {
        return $this->state ?? -1;
    }

    public function getUnitCostFormattedAttribute()
    {
        return number_format($this->unit_cost, 2, ',', '');
    }

    public function card() : BelongsTo
    {
        return $this->belongsTo(Card::class);
    }

    public function language() : BelongsTo
    {
        return $this->belongsTo(Language::class);
    }
}
