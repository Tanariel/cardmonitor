<?php

namespace App\Http\Controllers\Apis;

use App\Http\Controllers\Controller;
use App\Models\Apis\Api;
use Illuminate\Http\Request;

class ApiController extends Controller
{
    protected $baseViewPath = 'api';

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if ($request->wantsJson()) {
            return auth()->user()->apis;
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
    public function store(Request $request)
    {
        $attributes = $request->validate([
            'app_token' => 'required|string',
            'app_secret' => 'required|string',
            'access_token' => 'required|string',
            'access_token_secret' => 'required|string',
        ]);

        return Api::create([
            'user_id' => auth()->user()->id,
            'accessdata' => $attributes,
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Apis\Api  $api
     * @return \Illuminate\Http\Response
     */
    public function show(Api $api)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Apis\Api  $api
     * @return \Illuminate\Http\Response
     */
    public function edit(Api $api)
    {
        return view($this->baseViewPath . '.edit')
            ->with('model', $api);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Apis\Api  $api
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Api $api)
    {
        $api->accessdata = $request->validate([
            'app_token' => 'required|string',
            'app_secret' => 'required|string',
            'access_token' => 'required|string',
            'access_token_secret' => 'required|string',
        ]);

        $api->save();

        return $api;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Apis\Api  $api
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, Api $api)
    {
        if ($isDeletable = $api->isDeletable()) {
            $api->delete();
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
