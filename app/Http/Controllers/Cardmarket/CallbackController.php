<?php

namespace App\Http\Controllers\Cardmarket;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;

class CallbackController extends Controller
{
    public function create()
    {
        $api = App::make('CardmarketApi');

        return view('cardmarket.create')
            ->with('link', $api->access->link());
    }

    public function update(string $request_token)
    {
        dump($request_token);
        dump(auth()->user());

        $api = App::make('CardmarketApi');
        try {
            $access = $api->access->token($request_token);
            auth()->user()->api->setAccessToken($request_token, $access['oauth_token'], $access['oauth_token_secret']);
        }
        catch (\Exception $exc) {
            dump($exc);
            dump('Anmeldung fehlgeschlagen');
            auth()->user()->apis()->first()->reset();
        }

        // return redirect('home')->with('status', [
        //     'type' => 'success',
        //     'text' => 'Konto verknüpft',
        // ]);
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
