<?php

namespace App\Http\Controllers\Cardmarket;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Artisan;

class CallbackController extends Controller
{
    protected $api;

    public function __construct()
    {
        $this->api = App::make('CardmarketApi');
    }

    public function create()
    {

        return view('cardmarket.create')
            ->with('link', $this->api->access->link());
    }

    public function store(string $request_token)
    {
        try {
            $access = $this->api->access->token($request_token);

            dump($access);

            $user = auth()->user();
            $user->api->setAccessToken($request_token, $access['oauth_token'], $access['oauth_token_secret']);

            Artisan::call('order:sync', ['--user' => $user->id]);
            Artisan::call('order:sync', ['--user' => $user->id, '--state' => 'paid']);

            return redirect('home')->with('status', [
                'type' => 'success',
                'text' => 'Konto verknüpft',
            ]);
        }
        catch (\Exception $exc) {
            dump($exc);
            dump('Anmeldung fehlgeschlagen');
            auth()->user()->api->reset();
        }
    }

    public function update()
    {
        $user = auth()->user();
        $data = $user->cardmarketApi->account->logout();
        if ($data['logout'] == 'successful') {
            $user->api->reset();

            return redirect($this->api->access->link());
        }
    }

    public function destroy()
    {
        $user = auth()->user();
        $data = $user->cardmarketApi->account->logout();
        if ($data['logout'] == 'successful') {
            $user->api->reset();

            return back()->with('status', [
                'type' => 'success',
                'text' => 'Konto erfolgreich getrennt',
            ]);
        }
    }
}
