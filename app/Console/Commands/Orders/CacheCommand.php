<?php

namespace App\Console\Commands\Orders;

use App\Models\Orders\Order;
use Illuminate\Console\Command;

class CacheCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'order:cache';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Caches Orders';

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
        foreach (Order::all() as $key => $order) {
            $order->calculateProfits()->save();
        }
    }
}
