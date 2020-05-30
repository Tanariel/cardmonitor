<?php

namespace Tests\Unit\Transformers\Articles\Csvs;

use App\Models\Games\Game;
use App\Transformers\Articles\Csvs\Mtg;
use App\Transformers\Articles\Csvs\PCG;
use App\Transformers\Articles\Csvs\Transformer;
use App\Transformers\Articles\Csvs\YGO;
use Illuminate\Support\Arr;
use Tests\TestCase;

class TransformerTest extends TestCase
{
    const DATA_MAGIC = [
        1,2,3,4,5,6,7,8,9,10,11,12,13,14,15
    ];

    /**
     * @test
     */
    public function it_gets_the_right_transformer()
    {
        $cardmarketGames = json_decode(file_get_contents('tests/snapshots/cardmarket/games/get.json'), true);;
        foreach ($cardmarketGames['game'] as $key => $cardmarketGame) {
            $game = Game::updateOrCreate(['id' => $cardmarketGame['idGame']], [
                'name' => $cardmarketGame['name'],
                'abbreviation' => $cardmarketGame['abbreviation'],
                'is_importable' => in_array($cardmarketGame['idGame'], [1,3,6]),
            ]);
        }

        $this->assertInstanceOf(MtG::class, Transformer::transformer(Game::ID_MAGIC));
        $this->assertInstanceOf(YGO::class, Transformer::transformer(Game::ID_YUGIOH));
        $this->assertInstanceOf(PCG::class, Transformer::transformer(Game::ID_POKEMON));
    }

    /**
     * @test
     */
    public function it_transforms_the_given_data()
    {
        $this->markTestIncomplete('This test has not been implemented yet.');

        $data = Transformer::transform(Game::ID_MAGIC, self::DATA_MAGIC);
        // dump($data);
    }

    /**
     * @test
     */
    public function it_throws_an_error_if_the_game_is_not_importable()
    {
        $this->expectException(\InvalidArgumentException::class);

        Transformer::transformer(-1);
    }
}
