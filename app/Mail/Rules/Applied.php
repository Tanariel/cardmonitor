<?php

namespace App\Mail\Rules;

use App\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class Applied extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $runtime_in_sec;
    public $synced_count;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(User $user, float $runtime_in_sec, int $synced_count)
    {
        $this->user = $user;
        $this->runtime_in_sec = $runtime_in_sec;
        $this->synced_count = $synced_count;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->from('info@cardmonitor.de', config('app.name'))
            ->subject('Regel angewendet ' . $this->user->name)
            ->markdown('emails.rules.applied');
    }
}
