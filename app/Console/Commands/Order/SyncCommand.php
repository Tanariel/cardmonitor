<?php

namespace App\Console\Commands\Order;

use App\Models\Apis\Api;
use App\Models\Articles\Article;
use App\Models\Cards\Card;
use App\Models\Orders\Order;
use App\User;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\App;

class SyncCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'order:sync {user} {--actor=seller}  {--state=received}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Add/Update received orders from cardmarket API';

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
        $this->user = User::find($this->argument('user'));

        $this->processing();
        $this->user->cardmarketApi->syncOrders($this->option('actor'), $this->option('state'));
        $this->processed();
    }

    public function processing()
    {
        $this->user->update([
            'is_syncing_orders' => true,
        ]);
    }

    public function processed()
    {
        $this->user->update([
            'is_syncing_orders' => false,
        ]);
    }
}
