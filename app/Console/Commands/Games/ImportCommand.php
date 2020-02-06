<?php

namespace App\Console\Commands\Games;

use App\Models\Expansions\Expansion;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\App;

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
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $gameId = $this->argument('game');

        if (! in_array($gameId, array_keys(Expansion::GAMES))) {
            $this->error('Game does not exist');
            exit;
        }

        $this->cardmarketApi = App::make('CardmarketApi');

        $cardmarketExpansions = $this->cardmarketApi->expansion->find($gameId);

        $bar = $this->output->createProgressBar(count($cardmarketExpansions['expansion']));

        foreach ($cardmarketExpansions['expansion'] as $key => $cardmarketExpansion) {
            $expansion = Expansion::createOrUpdateFromCardmarket($cardmarketExpansion);

            $singles = $this->cardmarketApi->expansion->singles($expansion->id);
            foreach ($singles['single'] as $key => $single) {
                $card = Card::createOrUpdateFromCardmarket($single, $expansion->id);
            }

            $bar->advance();
            usleep(50);
        }

        $bar->finish();

        $this->info('');
        $this->info('syncing prices');

        $this->call('card:price:sync', [
            '--game' => $gameId,
        ]);

        $this->info('finished');
    }
}
