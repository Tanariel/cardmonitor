<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\Storages\Storage;
use App\User;
use Faker\Generator as Faker;

$factory->define(Storage::class, function (Faker $faker) {
    return [
        'user_id' => factory(User::class),
        'storage_id' => null,
        'level' => 0,
        'number' => null,
        'name' => $faker->word,
    ];
});
