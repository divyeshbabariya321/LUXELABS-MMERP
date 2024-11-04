<?php

namespace App\Jobs;
use App\ReturnExchangeStatus;
use App\ReturnExchange;
use App\Http\Controllers\WhatsAppController;

use Illuminate\Http\Request;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Exception;

class UpdateReturnStatusMessageTpl implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 1;

    /**
     * Create a new job instance.
     *
     * @param private      $returnId
     * @param null|private $message
     *
     * @return void
     */
    public function __construct(private $returnId, private $message = null)
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
            $return = ReturnExchange::where('id', $this->returnId)->first();
            if ($return) {
                $statusModal = ReturnExchangeStatus::where('id', $return->status)->first();
                if (! $this->message || $this->message == '') {
                    $defaultMessageTpl = ReturnExchangeStatus::STATUS_TEMPLATE;
                    if ($statusModal && ! empty($statusModal->message)) {
                        $defaultMessageTpl = $statusModal->message;
                    }
                    $msg = str_replace(['#{id}', '#{status}'], [$return->id, $statusModal->status_name], $defaultMessageTpl);
                } else {
                    $msg = $this->message;
                }
                // start update the order status
                $requestData = new Request();
                $requestData->setMethod('POST');
                $requestData->request->add([
                    'customer_id' => $return->customer_id,
                    'message'     => $msg,
                    'status'      => 0,
                ]);

                app(WhatsAppController::class)->sendMessage($requestData, 'customer');
            }
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    public function tags()
    {
        return ['customer_message', $this->returnId];
    }
}
