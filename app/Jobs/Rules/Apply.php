<?php

namespace App\Jobs\Rules;

use App\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Artisan;

class Apply implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $user;
    protected $sync;

    /**
     * The number of seconds the job can run before timing out.
     *
     * @var int
     */
    public $timeout = 3600;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(User $user, bool $sync)
    {
        $this->user = $user;
        $this->sync = $sync;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {

        try {
            $this->processing();
            Artisan::call('rule:apply', [
                'user' => $this->user->id,
                '--sync' => $this->sync,
            ]);
            $this->processed();
        }
        catch (\Exception $e) {
            $this->processed();

            throw $e;
        }
    }

    public function processing()
    {
        $this->user->update([
            'is_applying_rules' => true,
        ]);
    }

    public function processed()
    {
        $this->user->update([
            'is_applying_rules' => false,
        ]);
    }
}
