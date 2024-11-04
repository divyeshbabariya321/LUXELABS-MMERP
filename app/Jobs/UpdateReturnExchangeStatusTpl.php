<?php

namespace App\Jobs;
use App\ReturnExchangeProduct;
use App\ReturnExchange;
use App\Http\Controllers\WhatsAppController;

use Illuminate\Http\Request;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Exception;

class UpdateReturnExchangeStatusTpl implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 1;

    public $backoff = 5;

    /**
     * Create a new job instance.
     *
     * @param private $returnId
     * @param private $message
     *
     * @return void
     */
    public function __construct(private $returnId, private $message)
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
                $product = ReturnExchangeProduct::where('return_exchange_id', $this->returnId)->first();

                $msg = $this->message;

                // start update the order status
                $requestData = new Request();
                $requestData->setMethod('POST');
                $requestData->request->add([
                    'customer_id' => $return->customer_id,
                    'message'     => $msg,
                    'status'      => 0,
                    'order_id'    => $product->order_product_id,
                ]);
                app(WhatsAppController::class)->sendMessage($requestData, 'customer');
            }
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    public function tags()
    {
        return ['UpdateReturnExchangeStatusTpl', $this->returnId];
    }
}
