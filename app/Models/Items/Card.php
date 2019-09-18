<?php

namespace App\Models\Items;

use App\Models\Items\Item;
use App\User;
use Illuminate\Database\Eloquent\Model;
use Tightenco\Parental\HasParent;

class Card extends Item
{
    use HasParent;

    public static function defaultCosts(User $user) : object {
        return self::where('user_id', $user->id)->get()->mapWithKeys(function ($item) {
            return [$item['name'] => $item['unit_cost']];
        });
    }
}