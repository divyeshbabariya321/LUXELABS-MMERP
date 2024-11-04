<?php

namespace App\Console\Commands;

use App\ChatMessagePhrase;
use App\ChatMessageWord;
use App\CronJob;
use App\CronJobReport;
use App\Helpers\MessageHelper;
use Carbon\Carbon;
use Exception;
use Illuminate\Console\Command;

class MostUsedWordsInChat extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'message:most-used-words';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '';

    /**
     * Create a new command instance.
     */

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        try {
            $report = CronJobReport::create([
                'signature' => $this->signature,
                'start_time' => Carbon::now(),
            ]);
            // start to get the most used words from chat messages
            $mostUsedWords = MessageHelper::getMostUsedWords();
            ChatMessagePhrase::truncate();
            ChatMessageWord::truncate();

            if (! empty($mostUsedWords['words'])) {
                ChatMessageWord::insert($mostUsedWords['words']);
            }

            // start to phrases
            $allwords = ChatMessageWord::all();

            foreach ($allwords as $words) {
                $phrases = isset($mostUsedWords['phrases'][$words->word]) ? $mostUsedWords['phrases'][$words->word]['phrases'] : [];
                if (! empty($phrases)) {
                    foreach ($phrases as $phrase) {
                        if (isset($phrase['txt'])) {
                            // Split message into phrases
                            $split = preg_split('/(\.|\!|\?)/', $phrase['txt'], 10, PREG_SPLIT_DELIM_CAPTURE);

                            // Loop over split
                            foreach ($split as $sentence) {
                                ChatMessagePhrase::insert([
                                    'word_id' => $words->id,
                                    'phrase' => $sentence,
                                    'chat_id' => $phrase['id'],
                                ]);
                            }
                        }
                    }
                }
            }
            $report->update(['end_time' => Carbon::now()]);
        } catch (Exception $e) {
            CronJob::insertLastError($this->signature, $e->getMessage());
        }
    }
}
