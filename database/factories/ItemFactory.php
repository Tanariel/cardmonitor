<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\Items\Custom;
use App\Models\Items\Item;
use App\User;
use Faker\Generator as Faker;

$factory->define(Item::class, function (Faker $faker) {
    return [
        'type' => Custom::class,
        'user_id' => factory(User::class),
        'unit_id' => 1,
        'name' => $faker->word,
    ];
});
