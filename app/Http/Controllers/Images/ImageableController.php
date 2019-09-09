<?php

namespace App\Http\Controllers\Images;

use App\Http\Controllers\Controller;
use App\Models\Images\Image;
use App\Models\Orders\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ImageableController extends Controller
{
    protected $baseViewPath = 'image.imageable';

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request, Order $order)
    {
        if ($request->wantsJson()) {
            return $order->images;
        }

        $order->load([
            'images',
        ]);

        return view($this->baseViewPath . '.index')
            ->with('model', $order);
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
    public function store(Request $request, Order $order)
    {
        $attributes = $request->validate([
            'images' => 'required|array',
            'images.*' => 'required|file|max:51200|mimes:' . join(',', Image::MIME_TYPES),
        ]);

        $images = [];
        foreach ($attributes['images'] as $key => $file) {
            $images[] = Image::createFromUploadedFile($file, $order);
        }

        if ($request->wantsJson()) {
            return $images;
        }

        return back()
            ->with('status', [
                'type' => 'success',
                'text' => 'Datei hochgeladen!'
            ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
