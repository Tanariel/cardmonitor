<?php

namespace App\Http\Controllers\Articles\Stock;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Storage;

class ImportController extends Controller
{
    public function store(Request $request)
    {
        $attributes = $request->validate([
            'file' => 'required|file',
            'game_id' => 'required|integer',
        ]);

        $userId = auth()->user()->id;

        $filename = $attributes['file']->storeAs('', $userId . '-stockimport-' . $attributes['game_id'] . '.csv');

        if (! $filename) {
            return back()->with('status', [
                'type' => 'danger',
                'text' => 'Datei konnte nicht importiert werden.',
            ]);
        }

        Artisan::call('article:stock:import', [
            'user' => $userId,
            'game' => $attributes['game_id'],
        ]);

        return back()->with('status', [
            'type' => 'success',
            'text' => 'Bestand importiert.',
        ]);
    }
}
