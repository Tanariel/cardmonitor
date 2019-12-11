<?php

namespace App\Models\Items\Transactions;

use App\Models\Items\Item;
use App\Models\Orders\Order;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Arr;
use Parental\HasChildren;

class Transaction extends Model
{
    use HasChildren;

    const DECIMALS = 6;

    protected $appends = [
        'quantity_formatted',
        'unit_cost_formatted',
        'path',
    ];

    protected $casts = [
        'unit_cost' => 'decimal:' . self::DECIMALS,
    ];

    protected $dates = [
        'at',
    ];

    protected $guarded = [
        'id',
    ];

    /**
     * The booting method of the model.
     *
     * @return void
     */
    public static function boot()
    {
        parent::boot();

        static::creating(function($model)
        {
            if (! $model->user_id) {
                $model->user_id = auth()->user()->id;
            }

            if (! $model->at) {
                $model->at = now();
            }

            return true;
        });
    }

    public function isDeletable() : bool
    {
        return true;
    }

    public function setAtFormattedAttribute($value)
    {
        $this->attributes['at'] = Carbon::createFromFormat('d.m.Y H:i', $value);
        Arr::forget($this->attributes, 'at_formatted');
    }

    public function setQuantityFormattedAttribute($value)
    {
        $this->attributes['quantity'] = number_format(str_replace(',', '.', $value), self::DECIMALS, '.', '');
        Arr::forget($this->attributes, 'quantity_formatted');
    }

    public function setUnitCostFormattedAttribute($value)
    {
        $this->attributes['unit_cost'] = number_format(str_replace(',', '.', $value), self::DECIMALS, '.', '');
        Arr::forget($this->attributes, 'unit_cost_formatted');
    }

    public function getPathAttribute()
    {
        return '/transaction/' . $this->id;
    }

    public function getQuantityFormattedAttribute($value)
    {
        return number_format($this->attributes['quantity'], 2, ',', '');
    }

    public function getUnitCostFormattedAttribute($value)
    {
        return number_format($this->attributes['unit_cost'], 2, ',', '');
    }

    public function item() : BelongsTo
    {
        return $this->belongsTo(Item::class, 'item_id');
    }

    public function order() : BelongsTo
    {
        return $this->belongsTo(Order::class, 'order_id');
    }
}
