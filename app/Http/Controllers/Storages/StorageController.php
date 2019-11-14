<?php

namespace App\Http\Controllers\Storages;

use App\Http\Controllers\Controller;
use App\Models\Storages\Storage;
use Illuminate\Http\Request;

class StorageController extends Controller
{
    protected $baseViewPath = 'storage';

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if ($request->wantsJson()) {
            $storages = auth()->user()
                ->storages()
                ->orderBy('full_name', 'ASC')
                ->get();

            return $storages;
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
        return Storage::create($request->validate([
            'name' => 'required|string',
        ]));
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Storages\Storage  $storage
     * @return \Illuminate\Http\Response
     */
    public function show(Storage $storage)
    {
        return view($this->baseViewPath . '.show')
            ->with('model', $storage->load([
                'contents',
            ]));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Storages\Storage  $storage
     * @return \Illuminate\Http\Response
     */
    public function edit(Storage $storage)
    {
        return view($this->baseViewPath . '.edit')
            ->with('model', $storage);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Storages\Storage  $storage
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Storage $storage)
    {
        $storage->update($request->validate([
            'name' => 'required|string',
        ]));

        if ($request->wantsJson()) {
            return $storage;
        }

        return redirect($storage->path);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Storages\Storage  $storage
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, Storage $storage)
    {
        if ($isDeletable = $storage->isDeletable()) {
            $storage->delete();
        }

        if ($request->wantsJson())
        {
            return [
                'deleted' => $isDeletable,
            ];
        }

        if ($isDeletable) {
            $status = [
                'type' => 'success',
                'text' => 'Lagerplatz <b>' . $storage->name . '</b> gelöscht.',
            ];
        }
        else {
            $status = [
                'type' => 'danger',
                'text' => 'Lagerplatz <b>' . $storage->name . '</b> kann nicht gelöscht werden.',
            ];
        }

        return redirect(route($this->baseViewPath . '.index'))
            ->with('status', $status);
    }
}
