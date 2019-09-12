<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\Orders\Evaluation;
use App\Models\Orders\Order;
use Faker\Generator as Faker;

$factory->define(Evaluation::class, function (Faker $faker) {
    return [
        'order_id' => factory(Order::class),
        'grade' => 1,
        'item_description' => 1,
        'packaging' => 1,
        'comment' => '',
        'complaint' => [],
    ];
});
