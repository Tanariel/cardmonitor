<?php

namespace App\Models\Items;

use App\Models\Items\Item;
use App\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Parental\HasParent;

class Card extends Item
{
    use HasParent;

    const DEFAULT_PRICE = 0.02;

    protected static $defaultPrices;

    public static function defaultCosts(User $user) : object {
        return self::where('user_id', $user->id)->get()->mapWithKeys(function ($item) {
            return [$item['name'] => $item['unit_cost']];
        });
    }

    public static function defaultPrices(int $userId) : Collection
    {
        if (! is_null(self::$defaultPrices)) {
            return self::$defaultPrices;
        }

        self::$defaultPrices = self::where('user_id', $userId)->get()->mapWithKeys(function ($item) {
            return [$item['name'] => $item['unit_cost']];
        });

        return self::$defaultPrices;
    }

    public static function defaultPrice(int $userId, string $rarity) : float
    {
        self::defaultPrices($userId);

        return Arr::get(self::$defaultPrices, $rarity, self::DEFAULT_PRICE);
    }
}