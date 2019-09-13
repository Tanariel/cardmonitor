<?php

namespace App\Http\Controllers\Home\Orders;

use App\Http\Controllers\Controller;
use App\Models\Orders\Order;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class YearController extends Controller
{
    public function index(string $year)
    {
        return Order::revenuePerMonth(auth()->user()->id, $year);
    }
}