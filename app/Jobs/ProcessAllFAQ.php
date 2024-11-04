<?php

namespace App\Jobs;

use App\Reply;
use App\Reply;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ProcessAllFAQ implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @param  private  $replyInfo
     * @param  private  $user_id
     * @return void
     */
    public function __construct(private $replyInfo, private $user_id) {}

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $replyInfo = $this->replyInfo;
        $user_id = $this->user_id;

        try {
            //Add the data for queue
            foreach ($replyInfo as $key => $value) {
                if (! empty($value->is_translate)) {   //if FAQ translate is  available then send for FAQ
                    $insertArray = [];
                    $insertArray[] = $value->id;

                    ProceesPushFaq::dispatch($insertArray)->onQueue('faq');
                } else {   //If FAQ transation is not available then first set for translation
                    $insertArray = [];
                    $insertArray[] = $value->id;

                    $replyInformation = Reply::find($value->id);

                    ProcessTranslateReply::dispatch($replyInformation, $user_id)->onQueue('replytranslation');   //set for translation

                    ProceesPushFaq::dispatch($insertArray)->onQueue('faq');
                }
            }
        } catch (Exception $e) {
            return response()->json(['code' => 400, 'message' => 'something went wrong'.$e->getMessage()]);
        }
    }
}
