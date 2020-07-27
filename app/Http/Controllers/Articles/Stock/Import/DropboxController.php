<?php

namespace App\Http\Controllers\Articles\Stock\Import;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;

class DropboxController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        $userId = $user->id;

        $user->update([
            'is_syncing_articles' => true,
        ]);

        Artisan::queue('article:stock:import:dropbox', [
            'user' => $userId,
        ]);

        return back()->with('status', [
            'type' => 'success',
            'text' => 'Bestand wird im Hintergrund aus Dropbox importiert.',
        ]);
    }
}
