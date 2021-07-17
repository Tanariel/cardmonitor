<?php

namespace App\Console\Commands\Card;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\App;

class SyncCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'card:sync';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Gets new products from cardmarket';

    protected $cardmarketApi;

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
        $this->cardmarketApi = App::make('CardmarketApi');

        $filename = $this->download();
        $row_count = 0;
        try {
            $file = fopen(storage_path('app/' . $filename), "r");
        } catch (\Exception $e) {
            $this->error("I couldn't find and open the file : {$filename} in the storage, perhaps you should create it ? ");
            die();
        }

        while (($data = fgetcsv($file, 2000, ";")) !== FALSE) {
            if ($row_count == 0) {
                $row_count++;
                continue;
            }
            dump($data);
            $row_count++;
        }
    }

    protected function download()
    {
        $filename = 'productsfile.csv';
        $zippedFilename = $filename . '.gz';

        if (App::environment() == 'local' && is_file(storage_path('app/' . $filename))) {
            return $filename;
        }

        try {
            $data = $this->cardmarketApi->product->csv();
        } catch (\Exception $e) {
            $this->error("Could not get answer from cardmarket api, here is the error i got : {$e->getMessage()}");
            die();
        }

        $created = Storage::disk('local')->put($zippedFilename, base64_decode($data['productsfile']));

        shell_exec('gunzip ' . storage_path('app/' . $filename));

        return $filename;
    }

    protected function expansions()
    {

    }

    protected function cards()
    {

    }
}
