<?php

namespace App\Console\Commands;

use App\Elasticsearch\Reindex\Reindex;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class ReindexMessages extends Command
{
    const LIMIT = 50000;

    const MESSAGES_INDEX = 'messages';

    const REINDEX_IS_RUNNING = 'reindex-messages-is-running';

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'reindex:messages {param?}';

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
        set_time_limit(0);

        try {
            $reindex = new Reindex;
            $reindex->execute();
        } catch (Exception $e) {
            Log::error('Reindex error: '.$e->getMessage().' trace: '.json_encode($e->getTrace()));
        }

        return 0;
    }
}
