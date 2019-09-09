<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\Images\Image;
use App\User;
use Faker\Generator as Faker;

$factory->define(Image::class, function (Faker $faker) {

    $fileExtension = $faker->fileExtension;

    return [
        'user_id' => factory(User::class),
        'name' => $faker->word,
        'extension' => $fileExtension,
        'original_name' => $faker->uuid . $fileExtension,
        'filename' => $faker->uuid . $fileExtension,
        'mime' => $faker->mimeType,
        'size' => rand(1000, 10000),
    ];
});
