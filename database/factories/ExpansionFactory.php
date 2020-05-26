<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\Expansions\Expansion;
use Faker\Generator as Faker;

$factory->define(Expansion::class, function (Faker $faker) {
    return [
        'cardmarket_expansion_id' => $faker->randomNumber,
        'name' => $faker->word,
        'abbreviation' => strtoupper($faker->word),
        'icon' => $faker->randomNumber,
        'released_at' => $faker->date,
        'is_released' => $faker->boolean,
    ];
});
