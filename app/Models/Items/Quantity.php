<?php

namespace App\Models\Items;

use App\Models\Items\Card;
use App\Models\Items\Transactions\Sale;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;

class Quantity extends Model
{
    const DECIMALS = 6;

    protected $appends = [
        'effective_from_formatted',
        'end_formatted',
        'quantity_formatted',
        'start_formatted',
    ];

    protected $casts = [
        'quantity' => 'decimal:' . self::DECIMALS,
        'start' => 'integer',
        'end' => 'integer',
    ];

    protected $dates = [
        'effective_from',
    ];

    protected $fillable = [
        'effective_from',
        'effective_from_formatted',
        'end',
        'end_formatted',
        'item_id',
        'quantity',
        'quantity_formatted',
        'start',
        'start_formatted',
        'user_id',
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

            return true;
        });
    }

    public function isDeletable()
    {
        return true;
    }

    public function getEffectiveFromFormattedAttribute()
    {
        return $this->effective_from->format('d.m.Y H:i');
    }

    public function getQuantityFormattedAttribute()
    {
        return number_format($this->quantity, 2, ',', '');
    }

    public function getStartFormattedAttribute()
    {
        return number_format($this->start, 0, ',', '');
    }

    public function getEndFormattedAttribute()
    {
        return is_null($this->end) ? '-' : number_format($this->end, 0, ',', '');
    }

    public function setEffectiveFromFormattedAttribute($value)
    {
        $this->attributes['effective_from'] = Carbon::createFromFormat('d.m.Y H:i', $value);
        Arr::forget($this->attributes, 'effectiv_from_formatted');
    }

    public function setQuantityFormattedAttribute($value)
    {
        $this->attributes['quantity'] = number_format(str_replace(',', '.', $value), self::DECIMALS, '.', '');
        Arr::forget($this->attributes, 'quantity_formatted');
    }

    public function setStartFormattedAttribute($value)
    {
        $this->attributes['start'] = number_format(str_replace(',', '.', $value), self::DECIMALS, '.', '');
        Arr::forget($this->attributes, 'start_formatted');
    }

    public function setEndFormattedAttribute($value)
    {
        $this->attributes['end'] = is_null($value) ? null : number_format(str_replace(',', '.', $value), self::DECIMALS, '.', '');
        Arr::forget($this->attributes, 'end_formatted');
    }
}
