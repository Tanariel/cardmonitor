<?php

use App\Models\Expansions\Expansion;
use App\Models\Games\Game;
use Illuminate\Database\Seeder;
use Illuminate\Support\Arr;

class GameSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->cardmarketApi = App::make('CardmarketApi');

        $cardmarketGames = $this->cardmarketApi->games->get();
        foreach ($cardmarketGames['game'] as $key => $cardmarketGame) {
            $links = $cardmarketGame['links'][0] ?? [];
            $game = Game::updateOrCreate(['id' => $cardmarketGame['idGame']], [
                'name' => $cardmarketGame['name'],
                'abbreviation' => $cardmarketGame['abbreviation'],
                'is_importable' => Arr::has($links, Expansion::GAMES),
            ]);
        }
    }
}
