<?php

namespace App\Models\Expansions;

use App\Models\Cards\Card;
use App\Traits\HasLocalizations;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Cache;

class Expansion extends Model
{
    use HasLocalizations;

    protected $dates = [
        'released_at',
    ];

    protected $guarded = [];

    public $incrementing = false;

    public const GAMES = "expansions";

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
            $model->id = $model->cardmarket_expansion_id;

            if (! $model->game_id) {
                $model->game_id = 1;
            }

            return true;
        });
    }

    public static function createFromCsv(array $row) : self
    {
        $model = self::create([
            'cardmarket_expansion_id' => $row[0],
            'name' => $row[1],
            'abbreviation' => $row[6],
            'icon' => $row[7],
            'is_released' => $row[9] ?: 0,
            'released_at' => ($row[9] == 1 ? new Carbon($row[8]) : null)
        ]);
        for ($i = 2; $i <= 5; $i++) {
            $model->localizations()->create([
                'language_id' => $i,
                'name' => $row[$i],
            ]);
        }

        return $model;
    }

    public static function createFromCardmarket(array $cardmarketExpansion) : self
    {
        $model = self::create([
            'cardmarket_expansion_id' => $cardmarketExpansion['idExpansion'],
            'name' => $cardmarketExpansion['enName'],
            'abbreviation' => $cardmarketExpansion['abbreviation'],
            'icon' => $cardmarketExpansion['icon'],
            'is_released' => $cardmarketExpansion['isReleased'],
            'released_at' => ($cardmarketExpansion['isReleased'] == 'true' ? new Carbon($cardmarketExpansion['releaseDate']) : null)
        ]);
        foreach ($cardmarketExpansion['localization'] as $key => $localization) {
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

    public static function createOrUpdateFromCardmarket(array $cardmarketExpansion) : self
    {
        $values = [
            'abbreviation' => $cardmarketExpansion['abbreviation'],
            'cardmarket_expansion_id' => $cardmarketExpansion['idExpansion'],
            'game_id' => $cardmarketExpansion['idGame'],
            'icon' => $cardmarketExpansion['icon'],
            'is_released' => $cardmarketExpansion['isReleased'],
            'name' => $cardmarketExpansion['enName'],
            'released_at' => (is_null($cardmarketExpansion['releaseDate']) ? null : new Carbon($cardmarketExpansion['releaseDate']))
        ];

        $attributes = [
            'cardmarket_expansion_id' => $cardmarketExpansion['idExpansion'],
        ];

        $model = self::updateOrCreate($attributes, $values);

        if ($model->wasRecentlyCreated) {
            foreach ($cardmarketExpansion['localization'] as $key => $localization) {
                if ($localization['idLanguage'] == 1) {
                    continue;
                }

                $model->localizations()->create([
                    'language_id' => $localization['idLanguage'],
                    'name' => $localization['name'],
                ]);
            }
        }

        return $model;
    }

    public static function firstOrImport(int $cardmarketExpansionId) : self
    {
        $model = self::where('cardmarket_expansion_id', $cardmarketExpansionId)->first();

        if (! is_null($model)) {
            return $model;
        }

        return self::import($cardmarketExpansionId);
    }

    public static function getByAbbreviation(string $abbreviation) : self
    {
        $abbreviation = strtoupper($abbreviation);

        return Cache::rememberForever('expansion.' . $abbreviation, function () use ($abbreviation) {
            return self::firstWhere('abbreviation', $abbreviation);
        });
    }

    public static function import(int $cardmarketExpansionId) : self
    {
        $cardmarketApi = App::make('CardmarketApi');
        $data = $cardmarketApi->expansion->singles($cardmarketExpansionId);

        return self::createOrUpdateFromCardmarket($data['expansion']);
    }

    public function isPresale() : bool
    {
        if (is_null($this->released_at)) {
            return true;
        }

        return ($this->released_at > now()->addDay());
    }

    public function setAbbreviationFromCardImagePathAttribute($value) : void
    {
        $parts = explode('/', $value);
        $this->attributes['abbreviation'] = strtolower($parts[4]);
    }

    public function getIconPositionAttribute() : array
    {
        return [
            'x' => (($this->icon % 10) * 21 * -1),
            'y' => ((floor($this->icon / 10) * 21) * -1),
        ];
    }

    public function getIconPositionStringAttribute() : string
    {
        return $this->icon_position['x'] . 'px ' . $this->icon_position['y'] . 'px';
    }

    public function cards() : HasMany
    {
        return $this->hasMany(Card::class, 'expansion_id');
    }
}
