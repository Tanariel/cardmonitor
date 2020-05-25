<?php

namespace Tests\Unit\Transformers\Articles\Csvs;

use App\Models\Games\Game;
use App\Transformers\Articles\Csvs\Magic;
use App\Transformers\Articles\Csvs\Transformer;
use App\Transformers\Articles\Csvs\Yugioh;
use PHPUnit\Framework\TestCase;

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
        $this->assertEquals(Magic::class, Transformer::transformer(Game::ID_MAGIC));
        $this->assertEquals(Yugioh::class, Transformer::transformer(Game::ID_YUGIOH));
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
