<?php

namespace App\Support;

use Illuminate\Support\Collection;

class Locale
{
    public static function list() : Collection
    {
        return collect(config('app.available_locales'))->map( function ($lang) {
            return [
                'lang' => $lang,
                'name' => self::name($lang),
                'name_orig' => self::name($lang, $lang),
            ];
        });
    }

    public static function name($lang, $locale = null) : string
    {
        return __('user.locale.' . $lang, [], $locale);
    }

    public static function iso3166(string $code) : string
    {
        if ($code == 'D') {
            return 'DE';
        }

        return $code;
    }
}