<?php

namespace App\Console\Commands;

use App\ChatbotQuestion;
use App\Library\Watson\Model as WatsonManager;
use Illuminate\Console\Command;

class WastsonPushIntentsManual extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'watson:push-manual-intents';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Watson push manual intents';

    /**
     * Create a new command instance.
     */

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $q = ChatbotQuestion::where('erp_or_watson', 'watson')->whereNull('workspace_id')->where('value', '!=', '')->get();
        if (! $q->isEmpty()) {
            foreach ($q as $k) {
                $result = WatsonManager::pushQuestion($k->id);
                echo $result;
                echo "\r\n";
            }
        }
    }
}
