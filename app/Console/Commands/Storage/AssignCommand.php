<?php

namespace App\Console\Commands\Storage;

use App\Models\Storages\Content;
use App\Models\Storages\Storage;
use App\User;
use Illuminate\Console\Command;

class AssignCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'storage:assign {user}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Assigns storages to all articles';

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
        $userId = $this->argument('user');

        Storage::reset($userId);

        $contents = Content::where('user_id', $userId)
            ->get();

        foreach ($contents as $content) {
            $content->assign();
        }
    }
}
