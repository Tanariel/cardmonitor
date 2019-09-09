<?php

namespace App\Models\Cards;

use App\Models\Expansions\Expansion;
use App\Traits\HasLocalizations;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Arr;

class Card extends Model
{
    use HasLocalizations;

    protected $appends = [
        'imagePath',
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

    public function getImagePathAttribute()
    {
        return 'https://static.cardmarket.com/' . substr($this->attributes['image'], 2);
    }

    public function expansion() : BelongsTo
    {
        return $this->belongsTo(Expansion::class, 'expansion_id');
    }
}
