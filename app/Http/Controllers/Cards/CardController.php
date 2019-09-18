<?php

namespace App\Http\Controllers\Cards;

use App\Http\Controllers\Controller;
use App\Models\Cards\Card;
use Illuminate\Http\Request;

class CardController extends Controller
{
    protected $baseViewPath = 'card';

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if ($request->wantsJson()) {
            return Card::select('cards.*', 'localizations.name AS local_name')
                ->with([
                    'expansion'
                ])
                ->search($request->input('searchtext'), $request->input('language_id'))
                ->expansion($request->input('expansion_id'))
                ->orderBy('local_name', 'ASC')
                ->get();
        }
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
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Cards\Card  $card
     * @return \Illuminate\Http\Response
     */
    public function show(Card $card)
    {
        return view($this->baseViewPath . '.show')
            ->with('model', $card);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Cards\Card  $card
     * @return \Illuminate\Http\Response
     */
    public function edit(Card $card)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Cards\Card  $card
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Card $card)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Cards\Card  $card
     * @return \Illuminate\Http\Response
     */
    public function destroy(Card $card)
    {
        //
    }
}
