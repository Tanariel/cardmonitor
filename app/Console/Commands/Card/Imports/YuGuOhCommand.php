<?php

namespace App\Console\Commands\Card\Imports;

use App\Models\Cards\Card;
use App\Models\Expansions\Expansion;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\App;

class YuGuOhCommand extends Command
{
    const GAME_ID = 3;

    protected $cardmarketApi;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cards:import:yugioh';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Imports Yu-Gi-Oh cards';

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
        $this->info('importing cards');

        $this->cardmarketApi = App::make('CardmarketApi');

        $cardmarketExpansions = $this->cardmarketApi->expansion->find(self::GAME_ID);

        $bar = $this->output->createProgressBar(count($cardmarketExpansions['expansion']));

        foreach ($cardmarketExpansions['expansion'] as $key => $cardmarketExpansion) {
            $expansion = Expansion::createOrUpdateFromCardmarket($cardmarketExpansion);

            if ($expansion->wasRecentlyCreated == false) {
                $bar->advance();
                continue;
            }

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

        $this->call('card:price:sync');

        $this->info('finished');
    }
}
