<?php

namespace App\Console\Commands\Setup;

use App\Models\Apis\Api;
use App\Models\Cards\Card;
use App\Models\Expansions\Expansion;
use App\Models\Localizations\Language;
use App\Models\Storages\Storage;
use App\User;
use Carbon\Carbon;
use Cardmonitor\Cardmarket\Api as CardmarketApi;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Hash;

class DevCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'setup:dev';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sets up the development enviroment';

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
        $this->call('migrate:fresh', ['--force' => true]);

        Language::setup();
        $this->createUsers();
        $this->createCards();
        Artisan::call('card:price:sync');
        Artisan::call('storage:setup');
    }

    protected function createUsers()
    {
        $this->user = User::create([
            'name' => 'Daniel',
            'email' => 'admin@cardmonitor.de',
            'email_verified_at' => now(),
            'password' => Hash::make('admin'),
        ]);

        $user = User::create([
            'name' => 'Cardmarket',
            'email' => 'cardmarket@cardmonitor.de',
            'email_verified_at' => now(),
            'password' => Hash::make('cardmarket'),
        ]);

    }

    protected function createCards()
    {
        $row = 0;
        $expansionsFile = fopen("database/data/expansions.csv", "r");
        while (($data = fgetcsv($expansionsFile, 1000, ";")) !== FALSE) {
            if ($row == 0 || $data[0] == '') {
                $row++;
                continue;
            }
            $expansion = Expansion::createFromCsv($data);
            $row++;
        }
        fclose($expansionsFile);

        $expansions = Expansion::all()->mapWithKeys(function ($item) {
            return [$item['cardmarket_expansion_id'] => $item['id']];
        });

        $row = 0;
        $cardsFile = fopen("database/data/cards.csv", "r");
        while (($data = fgetcsv($cardsFile, 1100, ";")) !== FALSE) {
            if ($row == 0 || $data[0] == '') {
                $row++;
                continue;
            }

            $card = Card::createFromCsv($data, $expansions[$data[0]]);
            $card->download();

            $row++;
        }
        fclose($cardsFile);
    }
}
