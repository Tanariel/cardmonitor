<?php

namespace App\Http\Controllers\Users;

use App\Http\Controllers\Controller;
use App\Support\Locale;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    protected $baseViewPath = 'user';

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
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
     * @param  \App\User  $user
     * @return \Illuminate\Http\Response
     */
    public function show()
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\User  $user
     * @return \Illuminate\Http\Response
     */
    public function edit()
    {
        $model = auth()->user();
        $model->load([
            'providers',
        ]);

        return view($this->baseViewPath . '.edit')
            ->with('model', $model)
            ->with('locales', Locale::list());
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\User  $user
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        $model = auth()->user();

        $attributes = $request->validate([
            'locale' => 'sometimes|required|string',
            'password' => 'sometimes|required|confirmed|min:8',
            'prepared_message' => 'sometimes|required|string',
        ]);

        if (Arr::has($attributes, 'password')) {
            $attributes['password'] = Hash::make($attributes['password']);
        }

        $model->update($attributes);

        return back()->with('status', [
            'type' => 'success',
            'text' => 'Änderungen wurden gespeichert.',
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\User  $user
     * @return \Illuminate\Http\Response
     */
    public function destroy()
    {
        //
    }
}
