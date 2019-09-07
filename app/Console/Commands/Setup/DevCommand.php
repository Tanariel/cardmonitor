<?php

namespace App\Console\Commands\Setup;

use App\Models\Apis\Api;
use App\Models\Cards\Card;
use App\Models\Expansions\Expansion;
use App\Models\Localizations\Language;
use App\User;
use Carbon\Carbon;
use Cardmonitor\Cardmarket\Api as CardmarketApi;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\App;
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

        // Create Languages
        Language::setup();

        // create User
        $this->user = User::create([
            'name' => 'admin',
            'email' => 'admin@cardmonitor.de',
            'email_verified_at' => now(),
            'password' => Hash::make('admin'),
        ]);
        $this->user->setup();

        // create Expansions & Cards
        $api = Api::create([
            'user_id' => $this->user->id,
            'accessdata' => [
                'app_token' => '8Ts9QDnOCD7gukTV',
                'app_secret' => 'Zy7x2e1gkVcCQat50qd8XtsyMA9qatRN',
                'access_token' => 'LMDxSPkFfCBIYTULl3yHdswrwbYCZEzf',
                'access_token_secret' => 'PgHYR3j8o0Itktu47AbkRRE1foccd91r',
                'url' => CardmarketApi::URL_SANDBOX,
            ],
        ]);

        // $CardmarketApi = App::make('CardmarketApi', [
        //     'api' => $api,
        // ]);

        // Create Crads from CSV
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
        while (($data = fgetcsv($cardsFile, 1000, ";")) !== FALSE) {
            if ($row == 0 || $data[0] == '') {
                $row++;
                continue;
            }
            $card = Card::createFromCsv($data, $expansions[$data[0]]);
            $row++;
        }
        fclose($cardsFile);

        // Create Cards from API
        // $cardmarketExpansions = $CardmarketApi->expansion->find(1);
        // foreach ($cardmarketExpansions['expansion'] as $cardmarketExpansion) {
        //     $expansion = Expansion::createFromCardmarket($cardmarketExpansion);
        //     $cardmarketCards = $CardmarketApi->expansion->singles($expansion->cardmarket_expansion_id);
        //     foreach ($cardmarketCards['single'] as $cardmarketCard) {
        //         // dd($cardmarketCard);
        //         $card = Card::createFromCardmarket($cardmarketCard, $expansion->id);
        //     }
        // }
    }
}
