<?php

namespace App\Console\Commands\User\Balance;

use App\Support\Users\FinTs;
use Illuminate\Console\Command;

class ImportCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'user:balance:import';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Imports new bank transactions';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $fints = new FinTs();
        $fints->import(now()->sub(1, 'days'), now());
    }
}
