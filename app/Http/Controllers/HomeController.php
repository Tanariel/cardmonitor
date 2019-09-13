<?php

namespace App\Http\Controllers;

use App\Models\Orders\Evaluation;
use App\Models\Orders\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $evaluations = Evaluation::join('orders', 'orders.id', '=', 'evaluations.order_id')
            ->with('order.buyer')
            ->where('orders.user_id', auth()->user()->id)
            ->orderBy('orders.received_at', 'DESC')
            ->limit(5)
            ->get();

        $orders = DB::table('orders')->select('state', DB::raw('COUNT(*) AS count'))->groupBy('state')->get();
        $ordersByState = [];
        foreach ($orders as $key => $order) {
            $ordersByState[$order->state] = $order->count;
        }

        return view('home')
            ->with('evaluations', $evaluations)
            ->with('ordersByState', $ordersByState);
    }
}
