<?php

namespace App\Http\Controllers\Users\Balances;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class BalanceController extends Controller
{
    protected $baseViewPath = 'user.balance';

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if ($request->wantsJson()) {
            return auth()->user()->balances()
                ->latest()
                ->paginate();
        }

        return view($this->baseViewPath . '.index')
            ->with('model', auth()->user());
    }
}
