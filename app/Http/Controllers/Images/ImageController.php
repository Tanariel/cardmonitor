<?php

namespace App\Http\Controllers\Images;

use App\Http\Controllers\Controller;
use App\Models\Images\Image;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ImageController extends Controller
{
    protected $baseViewPath = 'image';

    public function __construct()
    {
        $this->authorizeResource(Image::class, 'image');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if ($request->wantsJson()) {
            return auth()->user()
                ->images()
                ->paginate();
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
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Images\Image  $image
     * @return \Illuminate\Http\Response
     */
    public function show(Image $image)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Images\Image  $image
     * @return \Illuminate\Http\Response
     */
    public function edit(Image $image)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Images\Image  $image
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Image $image)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Images\Image  $image
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, Image $image)
    {
        $isDeletable = $image->isDeletable();

        if ($isDeletable && Storage::disk('public')->delete('images/' . $image->filename))
        {
            $image->delete();
        }

        if ($request->wantsJson())
        {
            return [
                'deleted' => $isDeletable,
            ];
        }

        return back()
            ->with('status', [
                'type' => 'success',
                'text' => 'Datei gelöscht!'
            ]);
    }
}
