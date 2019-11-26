<?php

namespace App\Http\Controllers\Storages;

use App\Http\Controllers\Controller;
use App\Models\Storages\Storage;
use Illuminate\Http\Request;

class ParentController extends Controller
{
    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Storages\Storage  $storage
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Storage $storage)
    {
        $attributes = $request->validate([
            'parent_id' => 'nullable|exists:storages,id',
        ]);

        if (is_null($attributes['parent_id'])) {
            $storage->makeRoot()
                ->save();
        }
        else {
            $storage->appendToNode(Storage::find($attributes['parent_id']))
                ->save();
        }

        if ($request->wantsJson()) {
            return $storage;
        }

        return redirect($storage->path);
    }
}
