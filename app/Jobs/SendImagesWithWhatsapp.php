<?php

namespace App\Jobs;
use App\Http\Controllers\WhatsAppController;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Exception;

class SendImagesWithWhatsapp implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public $tries = 5;

    public $backoff = 5;

    public function __construct(protected $phone, protected $whatsapp_number, protected $image_url, protected $message_id)
    {
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            app(WhatsAppController::class)->sendWithNewApi($this->phone, $this->whatsapp_number, null, $this->image_url, $this->message_id);
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    public function tags()
    {
        return ['SendImagesWithWhatsapp', $this->whatsapp_number];
    }
}
