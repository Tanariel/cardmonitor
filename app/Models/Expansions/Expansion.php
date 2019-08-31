<?php

namespace App\Models\Expansions;

use App\Traits\HasLocalizations;
use Illuminate\Database\Eloquent\Model;

class Expansion extends Model
{
    use HasLocalizations;

    protected $guarded = [
        'id',
    ];


}
