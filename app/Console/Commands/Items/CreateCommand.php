<?php

namespace App\Console\Commands\Items;

use App\Models\Items\Card;
use App\User;
use Illuminate\Console\Command;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;

class CreateCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'items:create {user=0}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Creates default items that do not exist';

    protected $rarities = [];

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
        $this->setRarities();

        if ($this->argument('user')) {
            $this->create($this->argument('user'));
        }
        else {
            foreach (User::all() as $user) {
                $this->create($user->id);
            }
        }
    }

    protected function setRarities()
    {
        $this->rarities = collect(DB::select('SELECT rarity FROM cards WHERE rarity IS NOT NULL GROUP BY rarity'))->pluck('rarity');
    }

    protected function getCardNames(int $userId) : array
    {
        return Card::where('user_id', $userId)->get()->pluck('name')->toArray();
    }

    protected function create(int $userId)
    {
        $cardNames = $this->getCardNames($userId);

        foreach ($this->rarities as $key => $rarity) {
            if (in_array($rarity, $cardNames)) {
                continue;
            }

            Card::create([
                'name' => $rarity,
                'unit_cost' => Card::DEFAULT_PRICE,
                'user_id' => $userId,
            ]);
        }
    }
}
