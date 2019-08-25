<?php

namespace App\Models\Items;

use App\Models\Items\Item;
use Illuminate\Database\Eloquent\Model;
use Tightenco\Parental\HasParent;

class Mailing extends Item
{
    use HasParent;
}
