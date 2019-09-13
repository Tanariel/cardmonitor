<?php

namespace App\Http\Controllers\Home\Orders;

use App\Http\Controllers\Controller;
use App\Models\Orders\Order;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MonthController extends Controller
{
    public function index(string $year, string $month)
    {
        return Order::revenuePerDay(auth()->user()->id, new Carbon($year . '-' . $month . '-1 00:00:00'), (new Carbon($year . '-' . $month . '-1 23:59:59'))->endOfMonth());
    }
}