<?php

namespace App\Models\Apis;

use Illuminate\Database\Eloquent\Model;

class Api extends Model
{
    protected $casts = [
        'accessdata' => 'array',
    ];

    protected $guarded = [
        'id',
    ];

    public function isConnected() : bool
    {
        return (! empty($this->accessdata));
    }

    public function isDeletable() : bool
    {
        return true;
    }

    public function reset() : self
    {
        $this->update([
            'accessdata' => [],
        ]);

        return $this;
    }

    public function setAccessToken(string $request_token, string $access_token, string $access_token_secret)
    {
        $this->update([
            'accessdata' => [
                'request_token' => $request_token,
                'access_token' => $access_token,
                'access_token_secret' => $access_token_secret,
            ],
        ]);
    }
}
