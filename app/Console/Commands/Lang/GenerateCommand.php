<?php

namespace App\Console\Commands\Lang;

use Illuminate\Console\Command;

class GenerateCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'lang:generate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate i18n json assets';

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
        $dirs = new \DirectoryIterator(resource_path('lang').'/');

        foreach ($dirs as $dir) {
            if (! $dir->isDir()) {
                continue;
            }

            $lang = $dir->getFilename();
            if ($lang == '.' || $lang == '..') {
                continue;
            }

            $this->call('lang:js', [
                '--json' => true,
                '--source' => $dir->getPathname(),
                'target' => 'public/js/langs/'.$lang.'.json',
            ]);
        }
    }
}
