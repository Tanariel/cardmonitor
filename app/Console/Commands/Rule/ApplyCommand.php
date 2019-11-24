<?php

namespace App\Console\Commands\Rule;

use App\Models\Rules\Rule;
use App\User;
use Illuminate\Console\Command;

class ApplyCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'rule:apply {user}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Applies all active rules';

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
        $user = User::findOrFail($this->argument('user'));

        if ($user->is_applying_rules) {
            return;
        }

        $user->update([
            'is_applying_rules' => true,
        ]);

        $rules = Rule::where('user_id', $user->id)
            ->where('active', true)
            ->orderBy('order_column', 'ASC')
            ->get();

        Rule::reset($user->id);

        foreach ($rules as $rule) {
            $rule->apply();
        }

        $user->update([
            'is_applying_rules' => false,
        ]);
    }
}
