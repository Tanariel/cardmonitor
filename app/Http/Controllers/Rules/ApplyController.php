<?php

namespace App\Http\Controllers\Rules;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;

class ApplyController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if ($request->wantsJson()) {
            return auth()->user();
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $user = auth()->user();

        Artisan::queue('rule:apply', [
            'user' => $user->id,
        ]);

        if ($request->wantsJson()) {
            return [];
        }

        return redirect($rule->path)
            ->with('status', [
                'type' => 'success',
                'text' => 'Regeln werden im Hintergrund ausgeführt.',
            ]);
    }
}
