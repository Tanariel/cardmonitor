<?php

namespace App\Http\Controllers;

use App\Models\Expansions\Expansion;
use Illuminate\Http\Request;

class ExpansionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if ($request->wantsJson()) {
            return Expansion::orderBy('name', 'ASC')->get();
        }

        return view('expansion.pdf')
            ->with('expansions', Expansion::where('game_id', 1)->orderBy('id', 'ASC')->get());
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
     * @param  \App\Models\Expansions\Expansion  $expansion
     * @return \Illuminate\Http\Response
     */
    public function show(Expansion $expansion)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Expansions\Expansion  $expansion
     * @return \Illuminate\Http\Response
     */
    public function edit(Expansion $expansion)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Expansions\Expansion  $expansion
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Expansion $expansion)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Expansions\Expansion  $expansion
     * @return \Illuminate\Http\Response
     */
    public function destroy(Expansion $expansion)
    {
        //
    }
}
