<?php

namespace App\Models\Items;

use App\Models\Items\Item;
use Illuminate\Database\Eloquent\Model;
use Tightenco\Parental\HasParent;

class Custom extends Item
{
    use HasParent;

    /**
     * The booting method of the model.
     *
     * @return void
     */
    public static function boot()
    {
        parent::boot();

        static::created(function($model)
        {
            $model->addQuantities([
                1 => [
                    'start' => 1,
                    'end' => 9999,
                ],
            ]);

            return true;
        });
    }

    public function isDeletable() : bool
    {
        return true;
    }
}
