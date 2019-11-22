<?php

namespace App\Console\Commands\Card\Price;

use App\Models\Cards\Card;
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
    protected $signature = 'card:price:sync';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Get latest prices from cardmarket';

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
        $CardmarketApi = App::make('CardmarketApi');

        $filename = 'priceguide.csv';
        $zippedFilename = $filename . '.gz';

        $data = $CardmarketApi->priceguide->csv();
        $created = Storage::disk('local')->put($zippedFilename, base64_decode($data['priceguidefile']));

        if ($created === false) {
            return;
        }

        shell_exec('gunzip ' . storage_path('app/' . $filename));

        $row_count = 0;
        $articlesFile = fopen(storage_path('app/' . $filename), "r");
        while (($data = fgetcsv($articlesFile, 2000, ",")) !== FALSE) {
            if ($row_count == 0) {
                $row_count++;
                continue;
            }
            Card::updatePricesFromCardmarket($data);
            $row_count++;
        }

        Storage::disk('local')->delete($filename);
    }
}
