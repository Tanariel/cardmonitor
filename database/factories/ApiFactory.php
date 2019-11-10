<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\Apis\Api;
use App\User;
use Faker\Generator as Faker;
use Illuminate\Support\Str;

$factory->define(Api::class, function (Faker $faker) {
    return [
        'user_id' => factory(User::class),
        'request_token' => Str::random(),
        'access_token' => Str::random(),
        'access_token_secret' => Str::random(),
    ];
});
