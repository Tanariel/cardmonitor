<?php

namespace App\Console\Commands\Card\Price;

use App\Models\Cards\Card;
use App\Models\Expansions\Expansion;
use App\Models\Games\Game;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Storage;

class SyncCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'card:price:sync {--game=0}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Get latest prices from cardmarket';

    protected $filename = 'priceguide.csv';

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
        if ($this->option('game')) {
            $this->sync($this->option('game'));
        }
        else {
            foreach (Game::keyValue() as $gameId => $name) {
                $this->sync($gameId);
            }
        }
    }

    protected function sync(int $gameId)
    {
        $this->filename = 'priceguide-' . $gameId . '.csv';
        $this->download($gameId);

        $row_count = 0;
        $articlesFile = fopen(storage_path('app/' . $this->filename), "r");
        while (($data = fgetcsv($articlesFile, 2000, ",")) !== FALSE) {
            if ($row_count == 0) {
                $row_count++;
                continue;
            }
            Card::updatePricesFromCardmarket($data);
            $row_count++;
        }

        if (App::environment() == 'production') {
            Storage::disk('local')->delete($this->filename);
        }
    }

    protected function download(int $gameId)
    {
        if (App::environment() != 'production') {
            return;
        }

        $CardmarketApi = App::make('CardmarketApi');

        $zippedFilename = $this->filename . '.gz';

        $data = $CardmarketApi->priceguide->csv($gameId);
        $created = Storage::disk('local')->put($zippedFilename, base64_decode($data['priceguidefile']));

        if ($created === false) {
            return;
        }

        shell_exec('gunzip ' . storage_path('app/' . $this->filename));
    }
}
