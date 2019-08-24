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

    public function isDeletable() : bool
    {
        return true;
    }
}
