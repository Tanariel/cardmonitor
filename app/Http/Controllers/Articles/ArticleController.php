<?php

namespace App\Http\Controllers\Articles;

use App\Http\Controllers\Controller;
use App\Models\Articles\Article;
use App\Models\Cards\Card;
use Illuminate\Http\Request;

class ArticleController extends Controller
{
    protected $baseViewPath = 'article';

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if ($request->wantsJson()) {
            return auth()->user()->articles()->paginate();
        }

        return view($this->baseViewPath . '.index');
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
        return Article::create($request->validate([
            'card_id' => 'required|integer',
            'language_id' => 'sometimes|required|integer',
            'condition' => 'sometimes|required|string',
        ]));
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Articles\Article  $article
     * @return \Illuminate\Http\Response
     */
    public function show(Article $article)
    {
        return view($this->baseViewPath . '.show')
            ->with('model', $article);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Articles\Article  $article
     * @return \Illuminate\Http\Response
     */
    public function edit(Article $article)
    {
        return view($this->baseViewPath . '.edit')
            ->with('model', $article);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Articles\Article  $article
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Article $article)
    {
        $article->update($request->validate([
            // 'language_id' => 'required|integer',
            // 'condition' => 'required|string',
            // 'bought_at_formatted' => 'required|date_format:"d.m.Y H:i"',
            // 'sold_at_formatted' => 'required|date_format:"d.m.Y H:i"',
            // 'unit_price_formatted' => 'required|formated_number',
            'unit_cost_formatted' => 'required|formated_number',
            'provision_formatted' => 'required|formated_number',
        ]));

        return $article->load([
            'card.expansion',
            'card.localizations',
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Articles\Article  $article
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, Article $article)
    {
        if ($isDeletable = $article->isDeletable()) {
            $article->delete();
        }

        if ($request->wantsJson())
        {
            return [
                'deleted' => $isDeletable,
            ];
        }

        return back();
    }
}
