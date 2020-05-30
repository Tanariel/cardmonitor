<?php

namespace Tests\Unit\Models\Games;

use App\APIs\Cardmarket\Stock\Csv\MtG;
use App\Models\Games\Game;
use Illuminate\Support\Arr;
use Tests\TestCase;

class GameTest extends TestCase
{
    /**
     * @test
     */
    public function it_gets_importable_games()
    {
        $model = factory(Game::class)->create([
            'is_importable' => false,
        ]);
        $this->assertEmpty(Game::importables());
        $model->update([
            'name' => 'Game',
            'is_importable' => true,
        ]);
        $this->assertCount(1, Game::importables());
    }

    /**
     * @test
     */
    public function it_gets_a_list_of_classnames()
    {
        $game = factory(Game::class)->create([
            'id' => 1,
            'name' => 'Magic the Gathering',
            'abbreviation' => 'MtG',
            'is_importable' => true,
        ]);

        $classnames = Game::classnames('App\APIs\Cardmarket\Stock\Csv');
        $this->assertEquals($classnames->get(1), MtG::class);
    }
}
