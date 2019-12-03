<?php

namespace App\Mail\Balances;

use App\Models\Users\Balance;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class Deposited extends Mailable
{
    use Queueable, SerializesModels;

    public $balance;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(Balance $balance)
    {
        $this->balance = $balance;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $this->balance->load('user');

        return $this->from('info@cardmonitor.de', config('app.name'))
            ->to($this->balance->user->email)
            ->subject('Wir haben das Guthaben deines Cardmonitor Kontos ' . $this->balance->user->name . ' aufgeladen!')
            ->bcc(config('app.mail'))
            ->markdown('emails.balance.deposited');
    }
}
