<?php

namespace App\Http\Controllers\Storages;

use App\Http\Controllers\Controller;
use App\Models\Expansions\Expansion;
use App\Models\Storages\Content;
use App\Models\Storages\Storage;
use Illuminate\Http\Request;

class ContentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request, Storage $storage)
    {
        return $storage->contents()
            ->with(['storagable'])
            ->get();
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
    public function store(Request $request, Storage $storage)
    {
        $attributes = $request->validate([
            'expansion_id' => 'required|integer|exists:expansions,id',
        ]);

        $attributes = [
            'user_id' => auth()->user()->id,
            'storagable_type' => Expansion::class,
            'storagable_id' => $attributes['expansion_id'],
        ];

        $content = $storage->contents()->create($attributes);

        return $content->load(['storagable']);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Storages\Content  $content
     * @return \Illuminate\Http\Response
     */
    public function show(Content $content)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Storages\Content  $content
     * @return \Illuminate\Http\Response
     */
    public function edit(Content $content)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Storages\Content  $content
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Content $content)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Storages\Content  $content
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, Content $content)
    {
        if ($isDeletable = $content->isDeletable()) {
            $content->delete();
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
