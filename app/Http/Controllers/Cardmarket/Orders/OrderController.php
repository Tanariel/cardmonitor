<?php

namespace App\Http\Controllers\Cardmarket\Orders;

use App\Http\Controllers\Controller;
use App\Models\Orders\Order;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Artisan;

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
        $user = auth()->user();
        $this->CardmarketApi = $user->cardmarketApi;

        if (is_null($order)) {
            $this->syncAllOrders($user);
        }
        else {
            $this->syncOrder($order);
        }

        if ($request->wantsJson()) {
            return;
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

    protected function syncAllOrders(User $user)
    {
        Artisan::call('order:sync', ['--user' => $user->id]);
        Artisan::call('order:sync', ['--user' => $user->id, '--state' => 'paid']);
    }

}
