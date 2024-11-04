<?php

namespace App\Console\Commands;

use App\Job;
use Illuminate\Console\Command;

class DeleteMagentoJobs extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'magento-jobs:delete';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Delete magento jobs';

    /**
     * Create a new command instance.
     */

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $job = Job::where('queue', 'magento')->get();
        foreach ($job as $j) {
            $j->delete();
        }
    }
}
