<?php

namespace App\Http\Controllers\Items\Transactions;

use App\Http\Controllers\Controller;
use App\Models\Items\Item;
use App\Models\Items\Transactions\Transaction;
use Illuminate\Http\Request;

class TransactionController extends Controller
{
    protected $baseViewPath = 'item.transaction';

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request, Item $item)
    {
        if ($request->wantsJson()) {
            return $item->transactions()->paginate();
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
    public function store(Request $request, Item $item)
    {
        return $item->transactions()->create($request->validate([
            'at_formatted' => 'required|date_format:"d.m.Y H:i"',
            'type' => 'required|string',
            'quantity_formatted' => ' required|formated_number',
            'unit_cost_formatted' => ' required|formated_number',
        ]));
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
    public function update(Request $request, Transaction $transaction)
    {
        $transaction->update($request->validate([
            'at_formatted' => 'required|date_format:"d.m.Y H:i"',
            'quantity_formatted' => ' required|formated_number',
            'unit_cost_formatted' => ' required|formated_number',
        ]));

        return $transaction;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Items\Transactions\Transaction  $transaction
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, Transaction $transaction)
    {
        if ($isDeletable = $transaction->isDeletable()) {
            $transaction->delete();
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
