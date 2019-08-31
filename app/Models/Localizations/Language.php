<?php

namespace App\Models\Localizations;

use Illuminate\Database\Eloquent\Model;

class Language extends Model
{
    const DEFAULT_ID = 1;
    const DEFAULT_NAME = 'English';

    public $incrementing = false;

    protected $fillable = [
        'id',
        'name',
    ];

}
