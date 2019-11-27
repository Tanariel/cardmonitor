<?php

namespace App\Http\Controllers\Storages;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;

class AssignController extends Controller
{
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $user = auth()->user();

        Artisan::call('storage:assign', [
            'user' => $user->id,
        ]);

        if ($request->wantsJson()) {
            return;
        }

        return redirect($rule->path)
            ->with('status', [
                'type' => 'success',
                'text' => 'Lagerplätze wurden zugewiesen.',
            ]);
    }
}
