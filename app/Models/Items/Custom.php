<?php

namespace App\Models\Items;

use App\Models\Items\Item;
use Illuminate\Database\Eloquent\Model;
use Tightenco\Parental\HasParent;

class Custom extends Item
{
    use HasParent;

    public function isDeletable() : bool
    {
        return true;
    }
}
