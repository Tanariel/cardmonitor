<?php

namespace Tests\Unit\Models\Games;

use App\Models\Games\Game;
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
}
