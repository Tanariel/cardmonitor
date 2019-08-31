<?php

namespace App\Console\Commands\Order;

use App\Models\Orders\Order;
use Illuminate\Console\Command;

// require_once('../cardmarket-api/vendor/autoload.php');

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
    protected $description = 'Add/Update orders from cardmarket API';

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
        $access = [
            'app_token' => '8Ts9QDnOCD7gukTV',
            'app_secret' => 'Zy7x2e1gkVcCQat50qd8XtsyMA9qatRN',
            'access_token' => 'LMDxSPkFfCBIYTULl3yHdswrwbYCZEzf',
            'access_token_secret' => 'PgHYR3j8o0Itktu47AbkRRE1foccd91r',
        ];
        $api = new \Cardmonitor\Cardmarket\Api($access, \Cardmonitor\Cardmarket\Api::URL_SANDBOX);
        dd($api->account->get());
        // dd($api, \Cardmonitor\Cardmarket\Api::URL_SANDBOX);
        $orders = $api->order->find(\Cardmonitor\Cardmarket\Order::ACTOR_SELLER, \Cardmonitor\Cardmarket\ORDER::STATE_PAID);

        foreach ($orders['order'] as $cardmarketOrder) {
            dump($cardmarketOrder);
            $values = [
                'shipping_method_id' => $cardmarketOrder['shippingMethod']['idShippingMethod'],
                'cardmarket_buyer_id' => $cardmarketOrder['buyer']['idUser'],
                'state' => $cardmarketOrder['state']['state'],
                'shippingmethod' => $cardmarketOrder['shippingMethod']['name'],
                'shipment_revenue' => $cardmarketOrder['shippingMethod']['price'],
                'cards_count' => $cardmarketOrder['articleCount'],
                'cards_revenue' => $cardmarketOrder['articleValue'],
                'revenue' => $cardmarketOrder['totalValue'],
            ];
            $order = Order::firstOrNew(['cardmarket_order_id' => $cardmarketOrder['idOrder']], $values);
            $order->load(['articles']);
            // dump($order);
            foreach ($cardmarketOrder['article'] as $cardmarketArticle) {
                dump($cardmarketArticle);
                // $order articles contains $cardmarketArticle?
                // erstellen, wenn nicht
                // wird nicht geändert
            }
        }
    }
}
