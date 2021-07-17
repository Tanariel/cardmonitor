<?php

namespace App\Models\Cards;

use App\Models\Expansions\Expansion;
use App\Models\Localizations\Language;
use App\Traits\HasLocalizations;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Storage;

class Card extends Model
{
    use HasLocalizations;

    const RARITIES = [
        'Masterpiece',
        'Mythic',
        'Rare',
        'Special',
        'Time Shifted',
        'Uncommon',
        'Common',
        'Land',
        'Token',
        'Tip Card',
    ];

    protected $appends = [
        'imagePath',
        'has_latest_prices',
    ];

    protected $casts = [
        'price_sell' => 'decimal:2',
        'price_low' => 'decimal:2',
        'price_trend' => 'decimal:2',
        'price_german_pro' => 'decimal:2',
        'price_suggested' => 'decimal:2',
        'price_foil_sell' => 'decimal:2',
        'price_foil_low' => 'decimal:2',
        'price_foil_trend' => 'decimal:2',
        'price_low_ex' => 'decimal:2',
        'price_avg_1' => 'decimal:2',
        'price_avg_7' => 'decimal:2',
        'price_avg_30' => 'decimal:2',
        'price_foil_avg_1' => 'decimal:2',
        'price_foil_avg_7' => 'decimal:2',
        'price_foil_avg_30' => 'decimal:2',
    ];

    protected $dates = [
        'prices_updated_at',
    ];

    protected $guarded = [
        'imagePath',
        'has_latest_prices',
    ];

    public $incrementing = false;

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
            $model->id = $model->cardmarket_product_id;

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
            'game_id' => $cardmarketCard['idGame'],
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

    public static function createOrUpdateFromCardmarket(array $cardmarketCard, $expansionId = null) : self
    {
        $values = [
            'cardmarket_product_id' => $cardmarketCard['idProduct'],
            'expansion_id' => $expansionId,
            'game_id' => $cardmarketCard['idGame'],
            'image' => $cardmarketCard['image'],
            'name' => $cardmarketCard['enName'],
            'number' => $cardmarketCard['number'] ?? null,
            'rarity' => $cardmarketCard['rarity'] ?? null,
            'reprints_count' => $cardmarketCard['countReprints'],
            'website' => $cardmarketCard['website'],
        ];

        $attributes = [
            'cardmarket_product_id' => $cardmarketCard['idProduct'],
        ];

        $model = self::updateOrCreate($attributes, $values);

        if ($model->wasRecentlyCreated) {
            foreach ($cardmarketCard['localization'] as $key => $localization) {
                if ($localization['idLanguage'] == 1) {
                    continue;
                }

                $model->localizations()->create([
                    'language_id' => $localization['idLanguage'],
                    'name' => $localization['name'],
                ]);
            }

            $model->download();
        }

        return $model;
    }

    public static function firstOrImport(int $cardmarketProductId) : self
    {
        $model = self::where('cardmarket_product_id', $cardmarketProductId)->first();

        if (isset($model)) {
            return $model;
        }

        return self::import($cardmarketProductId);
    }

    public static function import(int $cardmarketProductId) : self
    {
        $cardmarketApi = App::make('CardmarketApi');
        $data = $cardmarketApi->product->get($cardmarketProductId);
        $cardmarketProduct = $data['product'];
        $cardmarketExpansionId = $cardmarketProduct['expansion']['idExpansion'] ?? null;

        if (isset($cardmarketExpansionId)) {
            $expansion = Expansion::firstOrImport($cardmarketExpansionId);
        }

        return self::createOrUpdateFromCardmarket($cardmarketProduct, $cardmarketExpansionId);
    }

    public static function updatePricesFromCardmarket(array $data)
    {
        return self::where('id', $data[0])->update([
            'price_sell' => $data[1] ?: 0,
            'price_low' => $data[2] ?: 0,
            'price_trend' => $data[3] ?: 0,
            'price_german_pro' => $data[4] ?: 0,
            'price_suggested' => $data[5] ?: 0,
            'price_foil_sell' => $data[6] ?: 0,
            'price_foil_low' => $data[7] ?: 0,
            'price_foil_trend' => $data[8] ?: 0,
            'price_low_ex' => $data[9] ?: 0,
            'price_avg_1' => $data[10] ?: 0,
            'price_avg_7' => $data[11] ?: 0,
            'price_avg_30' => $data[12] ?: 0,
            'price_foil_avg_1' => $data[13] ?: 0,
            'price_foil_avg_7' => $data[14] ?: 0,
            'price_foil_avg_30' => $data[15] ?: 0,
            'prices_updated_at' => now(),
        ]);
    }

    public static function hasLatestPrices() : bool
    {
        return ((new Carbon(self::whereNotNull('prices_updated_at')->max('prices_updated_at')))->diffInHours() < 2);
    }

    public function download()
    {
        if (is_null($this->image)) {
            return;
        }

        $CardmarketApi = App::make('CardmarketApi');

        $filename = storage_path('app/public/items/' . $this->game_id . '/' . $this->expansion_id . '/' . $this->id . '.jpg');

        if (Storage::exists('public/items/' . $this->game_id . '/' . $this->expansion_id . '/' . $this->id . '.jpg')) {
            return;
        }

        if (! Storage::exists('public/items/' . $this->game_id . '/' . $this->expansion_id)) {
            Storage::makeDirectory('public/items/' . $this->game_id . '/' . $this->expansion_id, 0755, true);
        }

        try {
            $CardmarketApi->product->download($this->image, $filename);
        }
        catch(\Exception $e) {
            echo " Warning {$this->image} Couldn't be downloaded !";
            return;
        }
    }

    public function getHasLatestPricesAttribute() : bool
    {
        if (is_null($this->prices_updated_at)) {
            return false;
        }

        return ($this->prices_updated_at->diffInHours() < 2);
    }

    public function setPricesFromCardmarket(array $cardMarketPriceGuide) : self
    {
        $this->attributes['price_sell'] = $cardMarketPriceGuide['SELL'];
        $this->attributes['price_low'] = $cardMarketPriceGuide['LOW'];
        $this->attributes['price_low_ex'] = $cardMarketPriceGuide['LOWEX'];
        $this->attributes['price_foil_low'] = $cardMarketPriceGuide['LOWFOIL'];
        $this->attributes['price_avg_1'] = $cardMarketPriceGuide['AVG'];
        $this->attributes['price_trend'] = $cardMarketPriceGuide['TREND'];
        if (Arr::has($cardMarketPriceGuide, 'TRENDFOIL')) {
            $this->attributes['price_foil_trend'] = $cardMarketPriceGuide['TRENDFOIL'];
        }
        $this->prices_updated_at = now();

        return $this;
    }

    public function getImagePathAttribute()
    {
        return Storage::url('public/items/' . $this->game_id . '/' . $this->expansion_id . '/' . $this->id . '.jpg');
    }

    public function expansion() : BelongsTo
    {
        return $this->belongsTo(Expansion::class, 'expansion_id');
    }

    public function scopeRarity(Builder $query, $value) : Builder
    {
        if (! $value) {
            return $query;
        }

        return $query->where('cards.rarity', $value);
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
