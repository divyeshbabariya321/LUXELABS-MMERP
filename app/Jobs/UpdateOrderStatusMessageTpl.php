<?php

namespace App\Jobs;
use App\OrderStatus;
use App\Order;
use App\Http\Controllers\WhatsAppController;

use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Http\Request;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class UpdateOrderStatusMessageTpl implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 1;

    public $backoff = 5;

    /**
     * Create a new job instance.
     *
     * @param  private  $orderId
     * @param  null|private  $message
     * @return void
     */
    public function __construct(private $orderId, private $message = null) {}

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            $order = Order::where('id', $this->orderId)->first();
            if ($order) {
                $statusModal = OrderStatus::where('id', $order->order_status_id)->first();
                if (! $this->message || $this->message == '') {
                    $msg = Order::ORDER_STATUS_TEMPLATE;
                    if ($statusModal && ! empty($statusModal->message_text_tpl)) {
                        $msg = $statusModal->message_text_tpl;
                    }
                    if ($statusModal && ! empty($statusModal->status)) {
                        $msg = str_replace(['#{order_id}', '#{order_status}'], [$order->order_id, $statusModal->status], $msg);
                    }
                } else {
                    $defaultMessageTpl = $this->message;
                    $msg = $this->message;
                }
                // start update the order status
                $requestData = new Request;
                $requestData->setMethod('POST');
                $requestData->request->add([
                    'customer_id' => $order->customer_id,
                    'message' => $msg,
                    'status' => 0,
                    'order_id' => $order->id,
                ]);

                app(WhatsAppController::class)->sendMessage($requestData, 'customer');
            }
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    public function tags()
    {
        return ['customer_message', $this->orderId];
    }
}
