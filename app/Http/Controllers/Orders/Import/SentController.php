<?php

namespace App\Http\Controllers\Orders\Import;

use App\Http\Controllers\Controller;
use App\Models\Orders\Order;
use Illuminate\Http\Request;

class SentController extends Controller
{
    public function store(Request $request)
    {
        $attributes = $request->validate([
            'import_sent_file' => 'required|file',
        ]);

        $CardmarketApi = auth()->user()->cardmarketApi;
        $userId = auth()->user()->id;

        $content = $attributes['import_sent_file']->get();
        $rows = explode("\n", $content);
        $row_count = 0;
        $sent_count = 0;
        foreach ($rows as $row) {
            if ($row_count == 0) {
                $row_count++;
                continue;
            }
            $columns = explode(';', $row);
            $cardmarket_order_id = $columns[0];
            $order = Order::where('user_id', $userId)->where('cardmarket_order_id', $columns[0])->first();

            $row_count++;
            if (is_null($order)) {
                continue;
            }

            try {
                $cardmarketOrder = $CardmarketApi->order->send($order->cardmarket_order_id);
                $order->updateOrCreateFromCardmarket($userId, $cardmarketOrder['order']);
            }
            catch (\Exception $exc) {
                continue;
            }

            $sent_count++;
        }

        return back()->with('status', [
            'type' => 'success',
            'text' => $sent_count . '/' . ($row_count - 1) . ' Bestellung als versendet markiert.',
        ]);
    }
}
