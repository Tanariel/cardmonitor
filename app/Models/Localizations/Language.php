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

    public static function setup()
    {
        $languages = [
            1 => 'English',
            2 => 'French',
            3 => 'German',
            4 => 'Spanish',
            5 => 'Italian',
        ];
        foreach ($languages as $id => $name) {
            $language = self::create([
                'id' => $id,
                'name' => $name,
            ]);
        }
    }

}
