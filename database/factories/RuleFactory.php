<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\Rules\Rule;
use App\User;
use Faker\Generator as Faker;

$factory->define(Rule::class, function (Faker $faker) {
    return [
        'user_id' => factory(User::class),
        'name' => $faker->word,
        'formular' => 'price_trend',
    ];
});
