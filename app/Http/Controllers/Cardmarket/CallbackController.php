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
        $access = $this->api->access->token($request_token);

        $user = auth()->user();
        $user->api->setAccessToken($request_token, $access['oauth_token'], $access['oauth_token_secret']);

        $user->cardmarketApi->syncAllSellerOrders();

        return redirect('home')->with('status', [
            'type' => 'success',
            'text' => 'Konto verknüpft',
        ]);
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
