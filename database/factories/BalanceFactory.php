<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\Users\Balance;
use App\User;
use Faker\Generator as Faker;

$factory->define(Balance::class, function (Faker $faker) {
    return [
        'user_id' => factory(User::class),
        'amount_in_cents' => $faker->randomNumber,
        'multiplier' => 1,
        'type' => 'credit',
        'name' => $faker->name,
        'iban' => $faker->iban,
        'bic' => $faker->swiftBicNumber,
        'booking_text' => $faker->word,
        'description' => $faker->sentence,
        'eref' => $faker->md5,
        'received_at' => today(),
    ];
});
