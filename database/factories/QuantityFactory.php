<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\Items\Item;
use App\Models\Items\Quantity;
use App\User;
use Faker\Generator as Faker;
use Illuminate\Support\Arr;

$factory->define(Quantity::class, function (Faker $faker, array $attributes) {
    $item = factory(Item::class)->create([
        'user_id' => Arr::get($attributes, 'user_id', factory(User::class)->create()),
    ]);

    return [
        'user_id' => $item->user_id,
        'item_id' => $item->id,
        'start' => 0,
        'end' => null,
        'quantity' => $faker->randomFloat(6),
        'effective_from' => $faker->dateTime
    ];
});
