<?php

namespace App\Console\Commands;

use App\ChatMessage;
use App\CronJob;
use App\CronJobReport;
use Carbon\Carbon;
use Exception;
use Illuminate\Console\Command;

class GetMostUsedWordsInCustomerMessages extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'bulk-customer-message:get-most-used-keywords';

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
        try {

            CronJobReport::create([
                'signature' => $this->signature,
                'start_time' => Carbon::now(),
            ]);

            $messages = ChatMessage::where('is_processed_for_keyword', 0)->whereNotNull('number')->where('customer_id', '>', '0')->limit(1000)->get();
            if ($messages != null) {
                foreach ($messages as $message) {
                    // Set text
                    $text = $message->message;

                    // Set to processed
                    $message->is_processed_for_keyword = 1;
                    $message->save();

                    // Explode the words
                    $words = explode(' ', $text);

                    foreach ($words as $word) {
                        $word = preg_replace('/[^\w]/', '', $word);
                        var_dump($word);

                        if (strlen(trim($word)) <= 3) {
                            continue;
                        }
                    }
                }
            }
        } catch (Exception $e) {
            CronJob::insertLastError($this->signature, $e->getMessage());
        }
    }
}
