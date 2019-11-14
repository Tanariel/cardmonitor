<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\Expansions\Expansion;
use App\Models\Storages\Content;
use App\Models\Storages\Storage;
use App\User;
use Faker\Generator as Faker;

$factory->define(Content::class, function (Faker $faker) {
    return [
        'user_id' => factory(User::class),
        'storage_id' => factory(Storage::class),
        'storagable_type' => Expansion::class,
        'storagable_id' => factory(Expansion::class),
    ];
});
