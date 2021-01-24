<?php

namespace App\Auth;

use Illuminate\Database\Eloquent\Model;

class Provider extends Model
{
    protected $dates = [
        'expires_at',
    ];

    protected $fillable = [
        'user_id',
        'provider_type',
        'provider_id',
        'token',
        'token_secret',
        'refresh_token',
        'expires_in',
        'expires_at',
    ];

    public function refresh()
    {
        switch ($this->provider_type) {
            case 'dropbox':
                return $this->refreshDropboxToken();
                break;

            default:
                return false;
                break;
        }
    }

    protected function refreshDropboxToken() : bool
    {
        $token = $this->getDropboxRefreshToken($this->refresh_token);
        $this->update([
            'token' => $token['access_token'],
            'expires_in' => $token['expires_in'],
            'expires_at' => now()->addSeconds($token['expires_in']),
        ]);

        return true;
    }

    protected function getDropboxRefreshToken(string $refresh_token) : array
    {
        $curl = curl_init();

        curl_setopt_array($curl, [
            CURLOPT_URL => 'https://api.dropboxapi.com/oauth2/token',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => 'grant_type=refresh_token&refresh_token=' . $refresh_token,
            CURLOPT_USERPWD => config('services.dropbox.client_id') . ":" . config('services.dropbox.client_secret'),
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/x-www-form-urlencoded'
            ],
        ]);

        $response = curl_exec($curl);

        curl_close($curl);

        return json_decode($response, true);
    }
}
