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
    public static function transform(int $gameId, array $data) : array
    {
        $transformer = self::transformer($gameId);
        return $transformer::transform($data);
    }

    public static function transformer(int $gameId)
    {
        $games = Game::classnames('App\Transformers\Articles\Csvs');
        if (! Arr::has($games, $gameId)) {
            throw new \InvalidArgumentException('Game ID "' . $gameId . '" is not available. Create Transformer!');
        }

        $classname = Arr::get($games, $gameId);

        return new $classname();
    }
}