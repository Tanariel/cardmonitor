<?php

namespace App\Http\Controllers\Items;

use App\Http\Controllers\Controller;
use App\Models\Items\Item;
use App\Models\Items\Quantity;
use Illuminate\Http\Request;

class QuantityController extends Controller
{
    protected $baseViewPath = 'item.quantity';

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request, Item $item)
    {
        if ($request->wantsJson()) {
            return $item->quantities()->paginate();
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
        return $item->quantities()->create($request->validate([
            'effective_from_formatted' => 'required|date_format:"d.m.Y H:i"',
            'end_formatted' => 'nullable|formated_number',
            'quantity_formatted' => 'required|formated_number',
            'start_formatted' => 'required|formated_number',
        ]));
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Items\Quantity  $quantity
     * @return \Illuminate\Http\Response
     */
    public function show(Quantity $quantity)
    {
        return view($this->baseViewPath . '.show')
            ->with('model', $quantity);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Items\Quantity  $quantity
     * @return \Illuminate\Http\Response
     */
    public function edit(Quantity $quantity)
    {
        return view($this->baseViewPath . '.edit')
            ->with('model', $quantity);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Items\Quantity  $quantity
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Quantity $quantity)
    {
        $quantity->update($request->validate([
            'effective_from_formatted' => 'required|date_format:"d.m.Y H:i"',
            'end_formatted' => 'nullable|formated_number',
            'quantity_formatted' => 'required|formated_number',
            'start_formatted' => 'required|formated_number',
        ]));

        return $quantity;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Items\Quantity  $quantity
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, Quantity $quantity)
    {
        if ($isDeletable = $quantity->isDeletable()) {
            $quantity->delete();
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
