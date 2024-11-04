<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class ClearAll extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'clear:all';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clear all cached data';

    /**
     * Create a new command instance.
     *
     * @return void
     */

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->call('config:clear');
        $this->call('route:clear');
        // $this->call('route:cache');
        $this->call('view:clear');
        $this->call('cache:clear');
        $this->call('config:cache');
    }
}
