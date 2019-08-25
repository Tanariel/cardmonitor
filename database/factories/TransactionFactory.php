<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\Items\Item;
use App\Models\Items\Transactions\Purchase;
use App\Models\Items\Transactions\Transaction;
use App\User;
use Faker\Generator as Faker;
use Illuminate\Support\Arr;

$factory->define(Transaction::class, function (Faker $faker, array $attributes) {
    $item = factory(Item::class)->create([
        'user_id' => Arr::get($attributes, 'user_id', factory(User::class)->create()),
    ]);

    return [
        'type' => Purchase::class,
        'user_id' => $item->user_id,
        'item_id' => $item->id,
        'quantity' => $faker->randomNumber,
        'unit_cost' => $faker->randomFloat(6),
        'at' => $faker->dateTime
    ];
});
