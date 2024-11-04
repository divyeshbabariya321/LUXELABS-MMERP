<?php

namespace App\Jobs;
use App\Jobs;

use Illuminate\Support\Facades\Log;
use Illuminate\Bus\Queueable;
use App\InstagramCommentQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Exception;

class InstagramComment implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $_message;

    protected $_postId;

    protected $_account_id;

    public $tries = 5;

    public $backoff = 5;

    /**
     * Create a new job instance.
     *
     * @param mixed $data
     *
     * @return void
     */
    public function __construct($data)
    {
        $this->_message    = $data['message'];
        $this->_postId     = $data['id'];
        $this->_account_id = $data['account_id'];
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            $comment             = new InstagramCommentQueue();
            $comment->message    = $this->_message;
            $comment->post_id    = $this->_postId;
            $comment->account_id = $this->_account_id;
            $comment->save();
        } catch (Exception $e) {
            Log::info('Issue fom InstagramComment' . ' ' . $e->getMessage());
            throw new Exception($e->getMessage());
        }
    }

    public function tags()
    {
        return ['InstagramComment', $this->_postId];
    }
}
