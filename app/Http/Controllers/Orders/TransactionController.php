<?php

namespace App\Http\Controllers\Orders;

use App\Http\Controllers\Controller;
use App\Models\Items\Item;
use App\Models\Items\Transactions\Sale;
use App\Models\Items\Transactions\Transaction;
use App\Models\Orders\Order;
use Illuminate\Http\Request;

class TransactionController extends Controller
{
    protected $baseViewPath = 'order.transaction';

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request, Order $order)
    {
        if ($request->wantsJson()) {
            return $order->transactions()->paginate();
        }

        return view($this->baseViewPath . '.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, Order $order)
    {
        $transaction = Transaction::make($request->validate([
            'item_id' => 'required|integer|exists:items,id'
        ]) + [
            'order_id' => $order->id,
            'user_id' => $order->user_id,
            'type' => Sale::class
        ]);

        $item = Item::firstOrNew([
            'id' => $transaction->item_id
        ], []);

        $transaction->unit_cost = $item->unit_cost;
        $transaction->quantity = $item->quantity($order);

        $transaction->save();

        $transaction->item = $item;

        $order->calculateProfits()
            ->save();

        return $transaction;
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Items\Transactions\Transaction  $transaction
     * @return \Illuminate\Http\Response
     */
    public function show(Transaction $transaction)
    {
        return view($this->baseViewPath . '.show')
            ->with('model', $transaction);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Items\Transactions\Transaction  $transaction
     * @return \Illuminate\Http\Response
     */
    public function edit(Transaction $transaction)
    {
        return view($this->baseViewPath . '.edit')
            ->with('model', $transaction);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Items\Transactions\Transaction  $transaction
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Order $order, Transaction $transaction)
    {
        $transaction->update($request->validate([
            'quantity_formatted' => 'required|formated_number',
            'unit_cost_formatted' => 'required|formated_number',
        ]));

        $order->calculateProfits()
            ->save();

        if ($request->wantsJson()) {
            return $transaction->load('item');
        }

        return $transaction;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Items\Transactions\Transaction  $transaction
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, Order $order, Transaction $transaction)
    {
        if ($isDeletable = $transaction->isDeletable()) {
            $transaction->delete();
            $order->calculateProfits()
                ->save();
        }

        if ($request->wantsJson())
        {
            return [
                'deleted' => $isDeletable,
            ];
        }

        return back();
    }
}
