<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\Localizations\Language;
use App\Models\Localizations\Localization;
use Faker\Generator as Faker;

$factory->define(Localization::class, function (Faker $faker) {
    return [
        'language_id' => factory(Language::class),
        'name' => $faker->word,
    ];
});
