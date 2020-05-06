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

    public function setLanguageAttribute(Language $language)
    {
        $this->local_name = $this->localizations()->where('language_id', $language->id)->first()->name;
        $this->local_id = $this->id . '-' . strtoupper($language->code);
        $this->language = $language;
    }
}