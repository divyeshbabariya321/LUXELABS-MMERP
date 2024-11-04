<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Exception;

class InstaSchedulePost implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 5;

    public $backoff = 5;

    /**
     * Create a new job instance.
     *
     * @param protected $post
     *
     * @return void
     */
    public function __construct(protected $post)
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
            $media = json_decode($this->post->ig, true);
            $ig    = [
                'media'    => $media['media'],
                'location' => $media['location'],
            ];
            $this->post->ig = $ig;
            new PublishPost($this->post);
        } catch (Exception $e) {
            throw new Exception($e->getMessage());

        }
    }

    public function tags()
    {
        return ['InstaSchedulePost', $this->post->id];
    }
}
