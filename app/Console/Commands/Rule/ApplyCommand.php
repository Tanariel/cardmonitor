<?php

namespace App\Console\Commands\Rule;

use App\Mail\Rules\AppliedUser;
use App\Models\Articles\Article;
use App\Models\Cards\Card;
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
        $start = microtime(true);
        $this->user = User::findOrFail($this->argument('user'));

        $this->updatePrices();

        $rules = Rule::where('user_id', $this->user->id)
            ->where('active', true)
            ->orderBy('order_column', 'ASC')
            ->get();

        Rule::reset($this->user->id);

        $results = [];
        foreach ($rules as $rule) {
            $results[] = [
                'name' => $rule->name,
                'affected_rows' => $rule->apply($this->option('sync')),
            ];

        }

        $runtime_in_sec = round((microtime(true) - $start), 2);
        if ($this->option('sync')) {
            $this->sync();

            Mail::to($this->user->email)
                ->queue(new \App\Mail\Rules\AppliedUser($this->user, $runtime_in_sec, $results));

            Mail::to(config('app.mail'))
                ->queue(new \App\Mail\Rules\Applied($this->user, $runtime_in_sec));
        }
    }

    protected function updatePrices()
    {
        if (Card::hasLatestPrices()) {
            return;
        }

        $this->call('card:price:sync', [
            '--game' => 0,
        ]);
    }

    protected function sync()
    {
        $cardmarketApi = $this->user->cardmarketApi;

        $this->user->articles()->whereNotNull('rule_id')
            // ->where('price_rule', '>=', 0.02)
            ->whereNull('sold_at')
            ->chunkById(1000, function ($articles) use ($cardmarketApi) {
                foreach ($articles as $article) {
                    $article->syncUpdate();
                }
                // usleep(50);
        });
    }
}
