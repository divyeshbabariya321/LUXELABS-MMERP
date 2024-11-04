<?php

namespace App\Library\Watson;
use App\ChatbotQuestion;
use App\ChatbotMessageLogResponse;

use Illuminate\Support\Facades\Log;

/**
 * Watson reply condition match
 */
class ResponsePurify
{
    const EXCLUDED_REPLY = [
        "Can you reword your statement? I'm not understanding.",
        "I didn't understand. You can try rephrasing.",
        "I didn't get your meaning.",
    ];

    public $response;

    public $images;

    public $entities = [];

    public $intents = [];

    public $context;

    public function __construct($response, public $customer = null, public $logId = null)
    {
        $this->response = isset($response->output) ? $response->output : null;
        $this->context  = isset($response->context) ? $response->context : null;

        if ($this->isValid()) {
            $this->settleResponse();
        }
    }

    private function settleResponse()
    {
        $result = $this->response;

        // settle intetnts
        if (isset($result->intents)) {
            foreach ($result->intents as $intents) {
                $this->intents[$intents->intent] = $intents;
            }
        }

        // assign entities
        if (isset($result->entities)) {
            foreach ($result->entities as $entities) {
                $this->entities[$entities->entity] = $entities;
            }
        }
    }

    public function checkAutoApprove()
    {
        if (isset($this->intents)) {
            foreach ($this->intents as $intents) {
                $question = ChatbotQuestion::where('value', $intents->intent)->first();
                if ($question && $question->auto_approve) {
                    return true;
                }
            }
        }
        if (isset($this->entities)) {
            foreach ($this->entities as $entities) {
                $question = ChatbotQuestion::where('value', $entities->entity)->first();
                if ($question && $question->auto_approve) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * Match action and assign to the method so it can be go ahead
     */
    public function assignAction()
    {
        $medias = $this->isNeedToSendProductImages();
        $text   = $this->getReplyText();
        // if match action then assign
        if (! empty($medias['match']) && $medias['match'] == true) {
            return ['action' => 'send_product_images', 'reply_text' => $text, 'response' => $this->response, 'medias' => $medias['medias']];
        } else {
            if (isset($this->logId)) {
                ChatbotMessageLogResponse::StoreLogResponse([
                    'chatbot_message_log_id' => $this->logId,
                    'request'                => '',
                    'response'               => 'Watson assistant function send message customer function media not match.',
                    'status'                 => 'success',
                ]);
            }
        }

        // send the order status from here
        $orderStatus = $this->isNeedToSendOrderStatus($text);
        if (isset($this->logId)) {
            ChatbotMessageLogResponse::StoreLogResponse([
                'chatbot_message_log_id' => $this->logId,
                'request'                => '',
                'response'               => 'Watson assistant function send message customer function order status => ' . json_encode($orderStatus),
                'status'                 => 'success',
            ]);
        }

        if (! empty($orderStatus)) {
            return ['action' => 'send_text_only', 'reply_text' => $orderStatus['text']];
        }

        // is need to refund status
        $refundStatus = $this->isNeedToRefundStatus($text);
        if (isset($this->logId)) {
            ChatbotMessageLogResponse::StoreLogResponse([
                'chatbot_message_log_id' => $this->logId,
                'request'                => '',
                'response'               => 'Watson assistant function send message customer function refund status => ' . json_encode($refundStatus),
                'status'                 => 'success',
            ]);
        }
        if (! empty($refundStatus)) {
            return ['action' => 'send_text_only', 'reply_text' => $refundStatus['text']];
        }

        if (isset($this->logId)) {
            ChatbotMessageLogResponse::StoreLogResponse([
                'chatbot_message_log_id' => $this->logId,
                'request'                => '',
                'response'               => 'Watson assistant function send message customer function text => ' . $text,
                'status'                 => 'success',
            ]);
        }

        if (! empty($text)) {
            return ['action' => 'send_text_only', 'reply_text' => $text];
        }

        return null;
    }

    /**
     * Check response is valid or not
     *
     * @return false
     */
    public function isValid(): bool
    {
        return (isset($this->response) && isset($this->response->generic)) ? true : false;
    }

    public function getReplyText()
    {
        $textMessage = reset($this->response->generic);
        if (isset($textMessage->text)) {
            if (! in_array($textMessage->text, self::EXCLUDED_REPLY)) {
                return $textMessage->text;
            }
        }
    }

    private function isNeedToSendProductImages()
    {
        $entity      = 'product';
        $intentsList = ['Customer_Care_Products_Offered', 'Customer_Brand_Enquiry'];
        $gender      = null;
        if (isset($this->customer) && isset($this->customer->gender)) {
            $gender = ($this->customer->gender == 'male') ? 3 : 2;
        }

        $return = [
            'match'  => false,
            'medias' => [],
        ];

        // first match with scenerios
        foreach ($intentsList as $intents) {
            if (
                in_array($entity, array_keys($this->entities))
                && in_array($intents, array_keys($this->intents))
            ) {
                $attributes = isset($this->entities[$entity]) ? $this->entities[$entity] : null;
                $sendImages = new Action\SendProductImages($attributes, $params = [
                    'gender' => $gender,
                ]);

                $return['match'] = true;
                if ($sendImages->isOptionMatched()) {
                    $return['medias'] = $sendImages->getResults();
                }
            }
        }

        // now check with context scenrio of this again
        $context = $this->context;
        if (! empty($context)) {
            if (! empty($context->skills)) {
                $attributes = '';
                foreach ($context->skills as $skills) {
                    if (! empty($skills->user_defined->brand_name)) {
                        $attributes .= $skills->user_defined->brand_name;
                        if (! empty($skills->user_defined->category_name)) {
                            $attributes .= $skills->user_defined->category_name;
                            // if brand and category both setup then
                            $obect             = new \stdClass;
                            $obect->attributes = $attributes;
                            $sendImages        = new Action\SendProductImages($attributes, $params = [
                                'gender' => $gender,
                            ]);
                            $return['match'] = true;
                            if ($sendImages->isOptionMatched()) {
                                $return['medias'] = $sendImages->getResults();
                            }
                        }
                    }
                }
            }
        }

        return $return;
    }

    private function isNeedToSendOrderStatus($text = '')
    {
        // is order status need to be send?
        $intentsList = ['Order_status_find'];
        Log::info('Steps for check 1');
        foreach ($intentsList as $intents) {
            if (in_array($intents, array_keys($this->intents))) {
                Log::info('Steps for check 2');
                // check the last order of customer and send the message status
                $customer = $this->customer;
                if (empty($lastOrder)) {
                    $lastOrder = $customer->latestOrder();
                }
                if (! empty($lastOrder)) {
                    Log::info('Steps for check 3');
                    if ($lastOrder->status) {
                        Log::info('Steps for check 4');

                        if (! empty($lastOrder->status->message_text_tpl)) {
                            $text = $lastOrder->status->message_text_tpl;
                        }

                        $paramsToReplace = [
                            '#{order_id}'      => $lastOrder->order_id,
                            '#{order_status}'  => $lastOrder->status->status,
                            '#{website}'       => $lastOrder->getWebsiteTitle(),
                            '#{estimate_date}' => $lastOrder->estimated_delivery_date,
                            '#{delivery_date}' => $lastOrder->date_of_delivery,
                            '#{awb_number}'    => $lastOrder->totalWayBills(),
                        ];

                        $replyText = ! empty($text) ? $text : 'Greetings from Solo Luxury Ref: order number #{order_id} we have updated your order with status : #{order_status} Thanks for your trust.';

                        return ['text' => str_replace(array_keys($paramsToReplace), array_values($paramsToReplace), $replyText)];
                    }
                }
            }
        }
        Log::info('Steps for check 5');

        return false;
    }

    private function isNeedToRefundStatus($text = '')
    {
        // is order status need to be send?
        $intentsList = ['Refund_status_find'];
        foreach ($intentsList as $intents) {
            if (in_array($intents, array_keys($this->intents))) {
                // check the last order of customer and send the message status
                $customer     = $this->customer;
                $latestRefund = $customer->latestRefund();
                if (! empty($latestRefund)) {
                    if ($latestRefund->returnExchangeStatus) {
                        return ['text' => str_replace(['#{id}', '#{status}'], [$latestRefund->id, $latestRefund->returnExchangeStatus->status_name], $latestRefund->returnExchangeStatus->message)];
                    }
                }
            }
        }

        return false;
    }

    public function getEntities()
    {
        return false;
    }
}
