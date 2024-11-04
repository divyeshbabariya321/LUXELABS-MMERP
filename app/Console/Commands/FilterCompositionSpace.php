<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class FilterCompositionSpace extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'composition:filter';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Filter composition with space';

    /**
     * Create a new command instance.
     */

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        Log::info('Non breaking space issue started =>'.date('Y-m-d H:i:s'));
        Log::info('Non breaking space issue has been done =>'.date('Y-m-d H:i:s'));
    }
}
