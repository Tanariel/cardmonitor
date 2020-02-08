<?php

namespace Tests\Unit\Transformers\Articles\Csvs;

use App\Models\Expansions\Expansion;
use App\Transformers\Articles\Csvs\Magic;
use App\Transformers\Articles\Csvs\Transformer;
use App\Transformers\Articles\Csvs\Yugioh;
use PHPUnit\Framework\TestCase;

class TransformerTest extends TestCase
{
    const DATA_MAGIC = [
        1,2,3,4,5,6,7,8,9,10,11,12,13,14,
    ];

    /**
     * @test
     */
    public function it_gets_the_right_transformer()
    {
        $this->assertEquals(Magic::class, Transformer::transformer(Expansion::GAME_ID_MAGIC));
        $this->assertEquals(Yugioh::class, Transformer::transformer(Expansion::GAME_ID_YUGIOH));
    }

    /**
     * @test
     */
    public function it_transforms_the_given_data()
    {
        $data = Transformer::transform(Expansion::GAME_ID_MAGIC, self::DATA_MAGIC);
        dump($data);
    }
}
