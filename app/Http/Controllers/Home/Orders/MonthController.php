<?php

namespace App\Http\Controllers\Home\Orders;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MonthController extends Controller
{
    public function index(string $year, string $month)
    {
        Carbon::setLocale('de');

        $userId = auth()->user()->id;
        $start = new Carbon($year . '-' . $month . '-1');
        $end = (new Carbon($year . '-' . $month . '-1'))->endOfMonth();
        $periods = new CarbonPeriod($start, '1 days', $end);

        $categories = [];

        $article_counts = [];
        $revenues = [];
        $costs = [];
        $profits = [];

        $orders_count = 0;
        $cards_count = 0;
        $revenue_sum = 0;
        $cost_sum = 0;
        $profit_sum = 0;

        foreach ($periods as $date) {
            $key = $date->format('Y-m-d');
            $categories[$key] = $date->format('d.m.Y');
            $article_counts[$key] = 0;
            $revenues[$key] = 0;
            $costs[$key] = 0;
            $profits[$key] = 0;
        }

        $sql = "SELECT
                    DATE(orders.received_at) AS received_at,
                    DATE_FORMAT(orders.received_at, '%d.%m.%Y') AS received_at_formatted,
                    SUM(orders.revenue) AS revenue,
                    SUM(orders.cost) AS cost,
                    SUM(orders.profit) AS profit,
                    SUM(orders.articles_count) AS articles_count
                FROM
                    orders
                WHERE
                    orders.user_id = :user_id AND
                    orders.received_at IS NOT NULL AND
                    orders.received_at BETWEEN :start AND :end
                GROUP BY
                    received_at";
        $params = [
            'user_id' => $userId,
            'start' => $start,
            'end' => $end,
        ];
        $orders = DB::select($sql, $params);
        foreach ($orders as $key => $order) {
            $key = $order->received_at;
            $article_counts[$key] = (float) $order->articles_count;
            $revenues[$key] = (float) $order->revenue;
            $costs[$key] = (float) $order->cost;
            $profits[$key] = (float) $order->profit;

            $orders_count++;
            $cards_count += $order->articles_count;
            $revenue_sum += $order->revenue;
            $cost_sum += $order->cost;
            $profit_sum += $order->profit;
        }

        return [
            'categories' => array_values($categories),
            'series' => [
                [
                    'name' => 'Gewinn',
                    'data' => array_values($profits),
                    'color' => '#28a745',
                    'type' => 'column',
                ],
                [
                    'name' => 'Kosten',
                    'data' => array_values($costs),
                    'color' => '#dc3545',
                    'type' => 'column',
                ],
                [
                    'name' => 'Karten',
                    'data' => array_values($article_counts),
                    'type' => 'spline',
                    'tooltip' => [
                        'headerFormat' => '<b>{point.key}</b><br/>',
                        'pointFormat' => '{point.y:0f} Karten'
                    ],
                ],
            ],
            'title' => [
                'text' => 'Bestellungen im ' . $start->monthName
            ],
            'month_name' => $start->monthName,
            'statistics' => [
                'cards' => $cards_count,
                'cost' => $cost_sum,
                'orders' => $orders_count,
                'profit' => $profit_sum,
                'revenue' => $revenue_sum,
            ],
        ];

    }
}