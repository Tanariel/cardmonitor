<?php

namespace App\Transformers\Articles\Csvs;

use App\Models\Expansions\Expansion;
use App\Transformers\Articles\Csvs\Magic;
use App\Transformers\Articles\Csvs\Yugioh;
use Illuminate\Support\Arr;

class Transformer
{
    const TRANSFORMERS = [
        Expansion::GAME_ID_MAGIC => Magic::class,
        Expansion::GAME_ID_YUGIOH => Yugioh::class,
    ];

    public static function transform(int $gameId, array $data) : array
    {
        $transformer = self::transformer($gameId);
        return $transformer::transform($data);
    }

    public static function transformer(int $gameId) : string
    {
        if (! Arr::has(self::TRANSFORMERS, $gameId)) {

        }

        return Arr::get(self::TRANSFORMERS, $gameId);
    }
}