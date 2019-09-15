<?php

namespace App\Http\Controllers\Cardmarket\Orders;

use App\Http\Controllers\Controller;
use App\Models\Orders\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;

class OrderController extends Controller
{
    protected $CardmarketApi;

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  Order  $order
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Order $order = null)
    {
        $this->CardmarketApi = auth()->user()->cardmarketApi;

        if (is_null($order)) {
            $this->syncAllOrders();
        }
        else {
            $this->syncOrder($order);
        }

        return back()->with('status', [
            'type' => 'success',
            'text' => 'Bestellung aktualisiert.',
        ]);
    }

    protected function syncOrder(Order $order) : Order
    {
        $cardmarketOrder = $this->CardmarketApi->order->get($order->cardmarket_order_id);

        return Order::updateOrCreateFromCardmarket($order->user_id, $cardmarketOrder['order']);
    }

    protected function syncAllOrders()
    {

    }

}
