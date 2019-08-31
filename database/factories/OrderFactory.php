<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\Orders\Order;
use App\User;
use Faker\Generator as Faker;

$factory->define(Order::class, function (Faker $faker) {
    return [
        'user_id' => factory(User::class),
        'shipping_method_id' => 1,
        'cardmarket_order_id' => $faker->randomNumber(),
        'cardmarket_buyer_id' => $faker->randomNumber(),
        'state' => 'paid',
        'shippingmethod' => 'Standardbrief',
        'cards_count' => $faker->numberBetween(1, 123),
    ];
});
