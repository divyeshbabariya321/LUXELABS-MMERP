<?php

namespace App\Console\Commands;

use App\DeveloperTask;
use Illuminate\Console\Command;

class ChangeTesterBasedOnTeamLead extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ChangeTesterBasedOnTeamLead';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     */

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        DeveloperTask::where('team_lead_id', 319)->update(['tester_id' => 414]);
    }
}
