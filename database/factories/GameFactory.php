<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\Games\Game;
use Faker\Generator as Faker;

$factory->define(Game::class, function (Faker $faker) {
    return [
        'id' => $faker->unique()->randomNumber,
        'name' => $faker->word,
        'abbreviation' => $faker->word,
        'is_importable' => false,
    ];
});
