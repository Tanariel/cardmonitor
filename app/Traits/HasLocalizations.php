<?php

namespace App\Traits;

use App\Models\Localizations\Language;
use App\Models\Localizations\Localization;
use Illuminate\Database\Eloquent\Relations\MorphMany;

trait HasLocalizations
{
    public static function bootHasLocalizations()
    {
        static::created(function ($model)
        {
            $model->localizations()->create([
                'language_id' => Language::DEFAULT_ID,
                'name' => $model->name,
            ]);
        });
    }

    public function localizations() : MorphMany
    {
        return $this->morphMany(Localization::class, 'localizationable');
    }
}