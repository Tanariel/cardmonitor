<?php

namespace App\Console\Commands\Games;

use App\Models\Cards\Card;
use App\Models\Expansions\Expansion;
use App\Models\Games\Game;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\App;
use Symfony\Component\Console\Output\OutputInterface;

class ImportCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'games:import {game}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Imports expansions and cards from a game';

    /**
     * The importable Games keyed by its Id.
     *
     * @var array
     */
    protected $importableGames = [];

    /**
     * The importable Game IDs.
     *
     * @var array
     */
    protected $importableGameIds = [];

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();

        $this->cardmarketApi = App::make('CardmarketApi');
        $this->importableGames = Game::importables()->keyBy('id');
        $this->importableGameIds = array_keys($this->importableGames->toArray());
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $gameId = $this->argument('game');

        if ($gameId == 0) {
            foreach ($this->importableGameIds as $gameId) {
                $this->import($gameId);
            }
        }
        else {
            $this->import($gameId);
        }
    }

    protected function import(int $gameId)
    {
        $this->info('Importing ' . $this->importableGames[$gameId]->name);

        if (! $this->isImportable($gameId)) {
            $this->error('Game does not exist');
            return;
        }

        $cardmarketExpansions = $this->cardmarketApi->expansion->find($gameId);

        $bar = $this->output->createProgressBar(count($cardmarketExpansions['expansion']));

        foreach ($cardmarketExpansions['expansion'] as $key => $cardmarketExpansion) {
            $expansion = Expansion::createOrUpdateFromCardmarket($cardmarketExpansion);

            try {
                $singles = $this->cardmarketApi->expansion->singles($expansion->id);
            }
            catch (\Exception $e) {
                // $this->error('Expansion ' . $cardmarketExpansion['idExpansion'] . ' not available');
                continue;
            }

            foreach ($singles['single'] as $key => $single) {
                Card::createOrUpdateFromCardmarket($single, $expansion->id);
            }

            $bar->advance();
            usleep(50);
        }

        $bar->finish();

        $this->updatePrices($gameId);

        $this->info('Finished');
    }

    protected function updatePrices(int $gameId)
    {
        $this->info('');
        $this->info('Syncing prices');

        $this->call('card:price:sync', [
            '--game' => $gameId,
        ]);
    }

    protected function isImportable(int $gameId) : bool
    {
        return in_array($gameId, $this->importableGameIds);
    }
}
