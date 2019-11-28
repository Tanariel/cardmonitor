<?php

namespace App\Console\Commands\Rule;

use App\Models\Articles\Article;
use App\Models\Rules\Rule;
use App\User;
use Illuminate\Console\Command;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Mail;

class ApplyCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'rule:apply {user} {--sync}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Applies all active rules';

    protected $user;

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
        try {
            $this->user = User::findOrFail($this->argument('user'));

            if (! $this->user->canPay(Rule::PRICE_APPLY_IN_CENTS)) {
                return false;
            }

            $this->user->update([
                'is_applying_rules' => true,
            ]);

            $rules = Rule::where('user_id', $this->user->id)
                ->where('active', true)
                ->orderBy('order_column', 'ASC')
                ->get();

            Rule::reset($this->user->id);

            foreach ($rules as $rule) {
                $rule->apply($this->option('sync'));
            }

            $runtime_in_sec = round((microtime(true) - LARAVEL_START), 2);
            if ($this->option('sync')) {
                $synced_count = $this->sync();
                $this->user->withdraw(Rule::PRICE_APPLY_IN_CENTS, ApplyCommand::class);

                Mail::to(config('app.mail'))
                    ->queue(new \App\Mail\Rules\Applied($this->user, $runtime_in_sec, $synced_count));
            }

            $this->user->update([
                'is_applying_rules' => false,
            ]);

        }
        catch (\Exception $e) {
            $this->user->update([
                'is_applying_rules' => false,
            ]);

            throw $e;
        }
    }

    protected function sync() : int
    {
        $count = 0;
        $cardmarketApi = $this->user->cardmarketApi;

        $this->user->articles()->whereNotNull('rule_id')
            // ->where('price_rule', '>=', 0.02)
            ->whereNull('order_id')
            ->orderBy('cardmarket_article_id', 'ASC')
            ->chunkById(100, function ($articles) use ($cardmarketApi, &$count) {
                foreach ($articles as $article) {
                    $article->syncUpdate();
                    $count++;
                }
                usleep(50);
        });

        return $count;
    }
}
