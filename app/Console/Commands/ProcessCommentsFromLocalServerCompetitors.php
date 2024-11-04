<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class ProcessCommentsFromLocalServerCompetitors extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'competitors:process-local-users {hastagId}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Its For Local Part where we run this on local and send the data to server';

    /**
     * Create a new command instance.
     */
    /**
     * Execute the console command.
     */
    public function handle(): void {}
}
