<?php

namespace App\Models\Cards;

use App\Models\Expansions\Expansion;
use App\Traits\HasLocalizations;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Arr;

class Card extends Model
{
    use HasLocalizations;

    const RARITIES = [
        'Mythic',
        'Rare',
        'Special',
        'Uncommon',
        'Common',
        'Land',
        'Token',
    ];

    protected $appends = [
        'imagePath',
    ];

    protected $casts = [
        'price_sell' => 'decimal:2',
        'price_low' => 'decimal:2',
        'price_trend' => 'decimal:2',
        'price_avg' => 'decimal:2',
        'price_german_pro' => 'decimal:2',
        'price_foil_sell' => 'decimal:2',
        'price_foil_low' => 'decimal:2',
        'price_foil_trend' => 'decimal:2',
        'price_low_ex' => 'decimal:2',
    ];

    protected $dates = [
        'prices_updated_at',
    ];

    protected $guarded = [
        'id',
        'imagePath',
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
            if (! $model->game_id) {
                $model->game_id = 1;
            }

            return true;
        });
    }

    public static function createFromCsv(array $row, int $expansionId = 0) : self
    {
        $model = self::create([
            'cardmarket_product_id' => $row[1],
            'expansion_id' => $expansionId,
            'image' => $row[10],
            'name' => $row[4],
            'number' => $row[14],
            'rarity' => $row[15],
            'reprints_count' => $row[3],
            'website' => $row[9],
            'articles_count' => Arr::get($row, 18, 0),
            'articles_foil_count' => Arr::get($row, 19, 0),
        ]);
        for ($i = 5; $i <= 8; $i++) {
            $model->localizations()->create([
                'language_id' => ($i - 3),
                'name' => $row[$i],
            ]);
        }

        return $model;
    }

    public static function createFromCardmarket(array $cardmarketCard, int $expansionId = 0) : self
    {
        $model = self::create([
            'cardmarket_product_id' => $cardmarketCard['idProduct'],
            'expansion_id' => $expansionId,
            'image' => $cardmarketCard['image'],
            'name' => $cardmarketCard['enName'],
            'number' => $cardmarketCard['number'],
            'rarity' => $cardmarketCard['rarity'],
            'reprints_count' => $cardmarketCard['countReprints'],
            'website' => $cardmarketCard['website'],
        ]);
        foreach ($cardmarketCard['localization'] as $key => $localization) {
            if ($localization['idLanguage'] == 1) {
                continue;
            }

            $model->localizations()->create([
                'language_id' => $localization['idLanguage'],
                'name' => $localization['name'],
            ]);
        }

        return $model;
    }

    public function setPricesFromCardmarket(array $cardMarketPriceGuide) : self
    {
        $this->attributes['price_sell'] = $cardMarketPriceGuide['SELL'];
        $this->attributes['price_low'] = $cardMarketPriceGuide['LOW'];
        $this->attributes['price_low_ex'] = $cardMarketPriceGuide['LOWEX'];
        $this->attributes['price_foil_low'] = $cardMarketPriceGuide['LOWFOIL'];
        $this->attributes['price_avg'] = $cardMarketPriceGuide['AVG'];
        $this->attributes['price_trend'] = $cardMarketPriceGuide['TREND'];
        $this->prices_updated_at = now();

        return $this;
    }

    public function getImagePathAttribute()
    {
        return 'https://static.cardmarket.com/' . substr($this->attributes['image'], 2);
    }

    public function expansion() : BelongsTo
    {
        return $this->belongsTo(Expansion::class, 'expansion_id');
    }

    public function scopeExpansion(Builder $query, $value) : Builder
    {
        if (! $value) {
            return $query;
        }

        return $query->where('cards.expansion_id', $value);
    }

    public function scopeSearch(Builder $query, $searchtext, $languageId) : Builder
    {
        return $query->join('localizations', function ($join) use ($languageId) {
            $join->on('localizations.localizationable_id', '=', 'cards.id')
                ->where('localizations.localizationable_type', '=', Card::class)
                ->where('localizations.language_id', '=', $languageId);
        })
            ->where('localizations.name', 'like', '%' . $searchtext . '%');
    }
}
