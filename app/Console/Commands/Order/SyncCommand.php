<?php

namespace App\Console\Commands\Order;

use App\Models\Apis\Api;
use App\Models\Articles\Article;
use App\Models\Cards\Card;
use App\Models\Orders\Order;
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
    protected $signature = 'order:sync';

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
        dump('getting orders..');
        $apis = Api::all();
        foreach ($apis as $api) {
            $this->syncApiOrders($api);
        }
    }

    protected function syncApiOrders(Api $api)
    {

        $userId = $api->user_id;
        $CardmarketApi = App::make('CardmarketApi', [
            'api' => $api,
        ]);

        $cardmarketOrders = $CardmarketApi->order->find(\Cardmonitor\Cardmarket\Order::ACTOR_SELLER, \Cardmonitor\Cardmarket\ORDER::STATE_RECEIVED);
        foreach ($cardmarketOrders['order'] as $cardmarketOrder) {
            dd($cardmarketOrder);
            // TODO: nur aktuelle aktualisieren ($cardmarketOrder['state']['dateReceived'] ?)
            $order = Order::updateOrCreateFromCardmarket($userId, $cardmarketOrder);

        }
    }
}
