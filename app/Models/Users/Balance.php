<?php

namespace App\Models\Users;

use App\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Balance extends Model
{
    protected $appends = [
        'amount_in_euro_formatted',
        'received_at_formatted'
    ];

    protected $dates = [
        'received_at',
    ];

    protected $guarded = [];

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
            $model->incrementUserBalance();

            return true;
        });
    }

    public static function createFromTransaction($SEPATransaction) : self
    {
        $model = self::firstOrNew([
            'amount_in_cents' => ($SEPATransaction->getAmount() * 100),
            'multiplier' => ($SEPATransaction->getCreditDebit() == \Fhp\Model\StatementOfAccount\Transaction::CD_CREDIT ? 1 : -1),
            'type' => $SEPATransaction->getCreditDebit(),
            'name' => $SEPATransaction->getName(),
            'bic' => $SEPATransaction->getBankCode(),
            'iban' => $SEPATransaction->getAccountnumber(),
            'booking_text' => $SEPATransaction->getBookingText(),
            'description' => $SEPATransaction->getMainDescription(),
            'eref' => $SEPATransaction->getEndToEndID(),
            'received_at' => today(),
        ]);

        $model->guessUser()
            ->save();

        return $model;
    }

    public function guessUser() : self
    {
        $userId = (int) str_replace('Cardmonitor ', '', $this->attributes['description']);

        $this->user_id = User::firstOrNew([
            'id' => $userId
        ])->id;

        return $this;
    }

    public function incrementUserBalance()
    {
        if (is_null($this->user_id)) {
            return;
        }

        $this->user->increment('balance_in_cents', ($this->amount_in_cents * $this->multiplier));
    }

    public function getReceivedAtFormattedAttribute()
    {
        return $this->received_at->format('d.m.Y');
    }

    public function getAmountInEuroFormattedAttribute()
    {
        return number_format(($this->amount_in_cents / 100 * $this->multiplier), 2, ',', '.');
    }

    public function user() : BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
