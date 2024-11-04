<?php

namespace App\Jobs;
use App\Mails\Manual\InitializeRefundRequest;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Mail;
use Exception;

class ExchangeBuybackEmailSending implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 5;

    public $backoff = 5;

    /**
     * Create a new job instance.
     *
     * @param protected $to
     * @param protected $success
     * @param protected $emailObject
     *
     * @return void
     */
    public function __construct(protected $to, protected $success, protected $emailObject)
    {
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(): void
    {
        $emailObject = $this->emailObject;

        try {
            Mail::to($this->to)->send(new InitializeRefundRequest($this->success));
            $emailObject->is_draft = 0;
        } catch (\Throwable $th) {
            $emailObject->error_message = $th->getMessage();
            throw new Exception($th->getMessage());
        }

        $emailObject->save();
    }

    public function tags()
    {
        return [$this->success, $this->to];
    }
}
