<?php

namespace App\Transformers\Articles\Csvs;

use App\Models\Expansions\Expansion;
use App\Models\Games\Game;
use App\Transformers\Articles\Csvs\Magic;
use App\Transformers\Articles\Csvs\Pokemon;
use App\Transformers\Articles\Csvs\Yugioh;
use Illuminate\Support\Arr;

class Transformer
{
    const TRANSFORMERS = [
        Game::ID_MAGIC => Magic::class,
        Game::ID_YUGIOH => Yugioh::class,
        Game::ID_POKEMON => Pokemon::class,
    ];

    public static function transform(int $gameId, array $data) : array
    {
        $transformer = self::transformer($gameId);
        return $transformer::transform($data);
    }

    public static function transformer(int $gameId) : string
    {
        if (! Arr::has(self::TRANSFORMERS, $gameId)) {
            throw new \InvalidArgumentException('Game ID "' . $gameId . '" is not available. Create Transformer!');
        }

        return Arr::get(self::TRANSFORMERS, $gameId);
    }
}