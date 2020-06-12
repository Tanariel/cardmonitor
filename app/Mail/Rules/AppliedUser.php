<?php

namespace App\Mail\Rules;

use App\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class AppliedUser extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $results;
    public $runtime_in_sec;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(User $user, float $runtime_in_sec, array $results)
    {
        $this->user = $user;
        $this->runtime_in_sec = $runtime_in_sec;
        $this->results = $results;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->from('info@cardmonitor.de', config('app.name'))
            ->subject('Regel wurden angewendet')
            ->markdown('emails.rules.applied_user');
    }
}
