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
    protected $signature = 'order:sync {--user} {--actor=seller}  {--state=received}';

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
        $this->syncApiOrders(User::find($this->option('user'))->api);
    }

    protected function syncApiOrders(Api $api)
    {

        $userId = $api->user_id;
        $CardmarketApi = App::make('CardmarketApi', [
            'api' => $api,
        ]);

        $cardmarketOrders = [];
        $start = 1;
        do {
            $data = $CardmarketApi->order->find(Order::ACTOR_SELLER, ORDER::STATE_RECEIVED, $start);
            if (is_array($data)) {
                $cardmarketOrders += $data['order'];
                $start += 100;
            }
        }
        while (! is_null($data));

        foreach ($cardmarketOrders['order'] as $cardmarketOrder) {
            // TODO: nur aktuelle aktualisieren ($cardmarketOrder['state']['dateReceived'] ?)
            $order = Order::updateOrCreateFromCardmarket($userId, $cardmarketOrder);

        }
    }
}
