<?php

namespace App\Jobs;

use Illuminate\Support\Facades\Log;
use App\Language;
use App\Translations;
use App\GoogleTranslate;
use App\Models\ReplyLog;
use App\TranslateReplies;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\Services\CommonGoogleTranslateService;
use Exception;

class ProcessTranslateReply implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @param private $record
     * @param private $user_id
     *
     * @return void
     */
    public function __construct(private $record, private $user_id)
    {
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(): void
    {
        try {
            $record  = $this->record;
            $id      = $this->record->id;
            $user_id = $this->user_id;

            $replies = $record->reply;

            if ($replies != '') {
                $LanguageModel = Language::all();

                for ($i = 0; $i < count($LanguageModel); $i++) {
                    $language = $LanguageModel[$i]->locale;

                    // Check translation SEPARATE LINE exists or not
                    $checkTranslationTable = Translations::select('text')
                        ->where('from', 'en')
                        ->where('to', $language)
                        ->where('text_original', $replies)
                        ->first();

                    if ($checkTranslationTable) {
                        $data = htmlspecialchars_decode($checkTranslationTable->text, ENT_QUOTES);
                    } else {
                        $data              = '';
                        $googleTranslate   = new CommonGoogleTranslateService();
                        $translationString = $googleTranslate->translate($language, $replies);
                        if ($translationString != '') {
                            Translations::addTranslation($replies, $translationString, 'en', $language);
                            $data = htmlspecialchars_decode($translationString, ENT_QUOTES);
                        }
                    }

                    if ($data != '') {
                        $translateReplies = TranslateReplies::where('translate_from', 'en')
                            ->where('translate_to', $language)
                            ->where('replies_id', $id)
                            ->first();

                        if (count((array) $translateReplies) == 0) {
                            $translateReplies             = new TranslateReplies();
                            $translateReplies->created_by = $user_id;
                            $translateReplies->created_at = date('Y-m-d H:i:s');
                        } else {
                            $translateReplies->updated_by = $user_id;
                            $translateReplies->updated_at = date('Y-m-d H:i:s');
                        }

                        $translateReplies->replies_id     = $id;
                        $translateReplies->translate_from = 'en';
                        $translateReplies->translate_to   = $language;
                        $translateReplies->translate_text = $data;
                        $translateReplies->save();

                        $record->is_flagged   = 1;
                        $record->is_translate = 1;
                        $record->save();

                        (new ReplyLog)->addToLog($id, 'System has translate the reply to language (' . $language . ')', 'Translate');
                    } else {
                        (new ReplyLog)->addToLog($id, 'System unable to translate the FAQ', 'Translate');

                        $record->is_flagged   = 0;
                        $record->is_translate = 0;
                        $record->save();
                    }
                }
            }
        } catch (Exception $e) {
            if (! empty($id)) {
                (new ReplyLog)->addToLog($id, $e->getMessage(), 'Translate');
            }

            Log::info('Translation error');
            Log::info($e->getMessage());
        }
    }
}
