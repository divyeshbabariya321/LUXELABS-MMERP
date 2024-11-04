<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class AutoRejectProductIfAttributesAreMissing extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'product:reject-if-attribute-is-missing';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

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
        return true;
    }
}
