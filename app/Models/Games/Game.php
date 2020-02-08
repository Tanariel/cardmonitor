<?php

namespace App\Models\Games;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

class Game extends Model
{
    public $incrementing = false;

    protected $guarded = [];

    public static function importables() : Collection
    {
        return self::where('is_importable', true)->get();
    }
}
