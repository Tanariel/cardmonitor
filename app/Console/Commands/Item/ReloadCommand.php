<?php

namespace App\Console\Commands\Item;

use App\Models\Items\Transactions\Sale;
use App\Models\Items\Transactions\Transaction;
use App\User;
use Illuminate\Console\Command;

class ReloadCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'item:reload {user}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Reloads the costs for all orders';

    protected $user;

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
        $this->user = User::findOrFail($this->argument('user'));

        Sale::where('user_id', $this->user->id)
            ->delete();

        $this->user->orders()->chunk(100, function ($orders) {
            foreach ($orders as $key => $order) {
                $order->findItems();
            }
        });
    }
}
