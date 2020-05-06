<?php

namespace App\APIs\Skryfall;

use Illuminate\Support\Collection;

class CardCollection extends Collection
{
    public function firstByNumber(string $number)
    {
        return $this->first( function ($item) use ($number) {
            return $item->collector_number == $number;
        });
    }
}