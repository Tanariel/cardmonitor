<?php

namespace App\Models\Cards;

use App\Traits\HasLocalizations;
use Illuminate\Database\Eloquent\Model;

class Card extends Model
{
    use HasLocalizations;

    protected $guarded = [
        'id',
    ];
}
