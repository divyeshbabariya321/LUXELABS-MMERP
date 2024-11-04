<?php

namespace App\Console\Commands;

use App\ChatbotQuestion;
use App\ChatbotQuestionExample;
use App\CronJob;
use App\CronJobReport;
use App\Library\Watson\Language\Workspaces\V1\IntentService;
use Carbon\Carbon;
use Exception;
use Illuminate\Console\Command;

class ImportWatsonIntents extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import-watson:intents';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import Watson intents';

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
     */
    public function handle(): int
    {
        try {
            $report = CronJobReport::create([
                'signature' => $this->signature,
                'start_time' => Carbon::now(),
            ]);
            $watson = new IntentService(
                env('WATSON_API_KEY'),
                env('WATSON_API_PASSWORD')
            );

            $workspaceId = '19cf3225-f007-4332-8013-74443d36a3f7';

            $result = $watson->getList($workspaceId, ['export' => 'true']);

            $result = json_decode($result->getContent());

            if (! empty($result->intents)) {
                foreach ($result->intents as $intents) {
                    $question = ChatbotQuestion::where('value', $intents->intent)->first();
                    if (! $question) {
                        $question = ChatbotQuestion::create([
                            'value' => $intents->intent,
                            'workspace_id' => $workspaceId,
                        ]);
                    }

                    if (! empty($intents->examples)) {
                        foreach ($intents->examples as $example) {
                            ChatbotQuestionExample::updateOrCreate(
                                ['chatbot_question_id' => $question->id, 'question' => $example->text],
                                ['text' => $example->text]
                            );
                        }
                    }
                }
            }
            $report->update(['end_time' => Carbon::now()]);
        } catch (Exception $e) {
            CronJob::insertLastError($this->signature, $e->getMessage());
        }

        return true;
    }
}
