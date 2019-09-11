<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\Localizations\Language;
use Faker\Generator as Faker;

$factory->define(Language::class, function (Faker $faker) {
    return [
        'id' => $faker->numberBetween(2, 12),
        'name' => $faker->word,
        'code' => $faker->languageCode,
    ];
});
