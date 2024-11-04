<?php

namespace App\Jobs;

use App\BroadcastImage;
use App\ChatMessage;
use App\Customer;
use App\Http\Controllers\WhatsAppController;
use App\MessageQueue;
use App\Product;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendMessageToAll implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 5;

    public $backoff = 5;

    /**
     * Create a new job instance.
     *
     * @param  protectedint  $userId
     * @param  protectedCustomer  $customer
     * @param  protectedarray  $content
     * @param  protectedint  $messageQueueId
     * @param  null|protected  $groupId
     */
    public function __construct(protected int $userId, protected Customer $customer, protected array $content, protected int $messageQueueId, protected $groupId = null) {}

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            // Set default params
            $params = [
                'number' => null,
                'user_id' => $this->userId,
                'customer_id' => $this->customer->id,
                'approved' => 0,
                'status' => 8, // status for Broadcast messages
                'group_id' => $this->groupId,
            ];

            // Check for phone number
            if (is_numeric($this->customer->phone)) {
                // Set number we use to send the message
                $sendNumber = $this->customer->whatsapp_number ?? null;

                // Check for linked images - TODO: FIND OUT WHY WE DO APPROXIMATELY THE SAME THING TWICE
                if (array_key_exists('linked_images', $this->content)) {
                    // Create chatMessage
                    $chatMessage = ChatMessage::create($params);

                    // Attach all linked images
                    foreach ($this->content['linked_images'] as $image) {
                        if (is_array($image)) {
                            $image_key = $image['key'];
                            $mediable_type = 'BroadcastImage';

                            // Find broadcast image
                            $broadcast = BroadcastImage::with('Media')
                                ->whereRaw("broadcast_images.id IN (SELECT mediables.mediable_id FROM mediables WHERE mediables.media_id = $image_key AND mediables.mediable_type LIKE '%$mediable_type%')")
                                ->first();

                            // Get product IDs
                            $productIds = json_decode($broadcast->products, true);
                        } else {
                            $broadcast_image = BroadcastImage::find($image);
                            $productIds = json_decode($broadcast_image->products, true);
                        }

                        // Loop over products
                        foreach ($productIds as $productId) {
                            // Find product
                            $product = Product::find($productId);

                            // Attach product image to message
                            if ($product && $product->hasMedia(config('constants.media_tags'))) {
                                $chatMessage->attachMedia($product->getMedia(config('constants.media_tags'))->first()->getKey(), config('constants.media_tags'));
                            }
                        }
                    }
                }

                // Do we have a message?
                if ($this->content['message']) {
                    // Set params
                    $params['message'] = $this->content['message'];
                    $message = $this->content['message'];

                    // Create chatmessage
                    $chatMessage = ChatMessage::create($params);

                    try {
                        dump('sending message with NEW API');
                        $sendResult = ChatMessage::sendWithChatApi($this->customer->phone, $sendNumber, $message, false, $chatMessage->id);
                        if ($sendResult) {
                            $chatMessage->unique_id = $sendResult['id'] ?? '';
                            $chatMessage->save();
                        }
                    } catch (Exception $e) {
                        return response()->json(['status' => '400', 'message' => 'Opps! Something went wrong, Please try again.']);

                    }
                }

                // Do we have linked images?
                if (array_key_exists('linked_images', $this->content)) {
                    // Create chatMessage
                    $chatMessage = ChatMessage::create($params);

                    // Attach all linked images
                    foreach ($this->content['linked_images'] as $image) {
                        // Check for image array
                        if (is_array($image)) {
                            // Attach image
                            $chatMessage->attachMedia($image['key'], config('constants.media_tags'));

                            try {
                                dump('sending linked images with NEW API');
                                $sendResult = ChatMessage::sendWithChatApi($this->customer->phone, $sendNumber, null, str_replace(' ', '%20', $image['url']), $chatMessage->id);
                                if ($sendResult) {
                                    $chatMessage->unique_id = $sendResult['id'] ?? '';
                                    $chatMessage->save();
                                }
                            } catch (Exception $e) {
                                return response()->json(['status' => '400', 'message' => 'Opps! Something went wrong, Please try again.']);

                            }
                        } else {
                            $broadcast_image = BroadcastImage::find($image);

                            if ($broadcast_image->hasMedia(config('constants.media_tags'))) {
                                foreach ($broadcast_image->getMedia(config('constants.media_tags')) as $brod_image) {
                                    $chatMessage->attachMedia($brod_image, config('constants.media_tags'));

                                    try {
                                        dump('sending images with NEW API');
                                        $sendResult = ChatMessage::sendWithChatApi($this->customer->phone, $sendNumber, null, str_replace(' ', '%20', getMediaUrl($brod_image)), $chatMessage->id);
                                        if ($sendResult) {
                                            $chatMessage->unique_id = $sendResult['id'] ?? '';
                                            $chatMessage->save();
                                        }
                                    } catch (Exception $e) {
                                        return response()->json(['status' => '400', 'message' => 'Opps! Something went wrong, Please try again.']);

                                    }
                                }
                            }
                        }
                    }
                }

                if (isset($this->content['image'])) {
                    if (! isset($chatMessage)) {
                        $chatMessage = ChatMessage::create($params);
                    }

                    foreach ($this->content['image'] as $image) {
                        $chatMessage->attachMedia($image['key'], config('constants.media_tags'));

                        try {
                            dump('sending simple images with NEW API');
                            $sendResult = app(WhatsAppController::class)->sendWithThirdApi($this->customer->phone, $sendNumber, null, str_replace(' ', '%20', $image['url']), $chatMessage->id);
                            if ($sendResult) {
                                $chatMessage->unique_id = $sendResult['id'] ?? '';
                                $chatMessage->save();
                            }
                        } catch (Exception $e) {
                            return response()->json(['status' => '400', 'message' => 'Opps! Something went wrong, Please try again.']);

                        }
                    }
                }

                $chatMessage->update([
                    'approved' => 1,
                ]);

                $message_queue = MessageQueue::find($this->messageQueueId);
                $message_queue->chat_message_id = $chatMessage->id;
                $message_queue->sent = 1;
                $message_queue->save();
            } else {
                MessageQueue::where('customer_id', $this->customer->id)->delete();
            }
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    public function tags()
    {
        return ['SendMessageToAll', $this->userId];
    }
}
