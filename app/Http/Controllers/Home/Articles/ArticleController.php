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

        $articles = DB::table('articles')
            ->select(DB::raw('COUNT(id) AS count'), DB::raw('SUM(unit_price) AS unit_price_sum'), DB::raw('SUM(rule_price) AS rule_price_sum'))
            ->where('user_id', $user->id)
            ->whereNull('order_id')
            ->get();

        return json_encode($articles->first());
    }
}
