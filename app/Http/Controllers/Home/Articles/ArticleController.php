<?php

namespace App\Http\Controllers\Home\Articles;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ArticleController extends Controller
{
    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $user = auth()->user();

        $offers = DB::table('articles')
            ->select(DB::raw('COUNT(id) AS count'), DB::raw('SUM(unit_cost) AS cost'), DB::raw('SUM(unit_price) AS price'))
            ->where('user_id', $user->id)
            ->whereNull('order_id')
            ->get();

        $sold = DB::table('articles')
            ->select(DB::raw('COUNT(id) AS count'), DB::raw('SUM(unit_cost) AS cost'), DB::raw('SUM(unit_price) AS price'))
            ->where('user_id', $user->id)
            ->whereNotNull('order_id')
            ->get();

        $rules = DB::table('articles')
            ->select(DB::raw('COUNT(id) AS count'), DB::raw('SUM(unit_cost) AS cost'), DB::raw('SUM(rule_price) AS price'))
            ->where('user_id', $user->id)
            ->whereNull('order_id')
            ->whereNotNull('rule_id')
            ->get();

        return json_encode([
            'offers' => $offers->first(),
            'sold' => $sold->first(),
            'rules' => $rules->first(),
        ]);
    }
}
