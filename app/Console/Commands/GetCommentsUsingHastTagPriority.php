<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class GetCommentsUsingHastTagPriority extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'hastag:instagram {hastagId}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Getting hashtag comments from instagram using hashtag id';

    /**
     * Create a new command instance.
     */

    /**
     * Execute the console command.
     */
    public function handle(): void {}
}
