<?php

namespace App\Services\Products;

use Illuminate\Http\Request;
use Exception;
use App\Customer;
use App\KeywordAutoGenratedMessageLog;
use App\Http\Controllers\ProductController;
use App\Setting;
use App\Brand;
use App\Category;


class SendImagesOfProduct
{
    public $log = [];

    public $keyword_match = [];

    public function check($chatMessage)
    {
        $temp_log_params = [
            'model'           => Customer::class,
            'model_id'        => $chatMessage->customer_id,
            'message_sent_id' => $chatMessage->id,
            'keyword'         => $chatMessage->message,
        ];

        $addKeyword = KeywordAutoGenratedMessageLog::create($temp_log_params);

        $this->log[] = 'Started to check the auto send message via brand and category';

        if (! empty($chatMessage->message)) {
            $sentence = preg_replace('/\s+/', ' ', $chatMessage->message);
            $sentence = explode(' ', $sentence);

            $brand    = $this->checkWithBrand($sentence);
            $category = $this->checkWithCategory($sentence);

            if ($brand && $category) {
                // find the values from setting to get the how many images we need to send in total;
                try {
                    $setting     = Setting::where('name', 'send_auto_brand_category_image_no')->first();
                    $totalImages = 10;
                    if ($setting) {
                        $totalImages = $setting->val;
                    }

                    $myRequest = new Request();
                    $myRequest->setMethod('POST');
                    $myRequest->request->add(['brand' => [$brand->id]]);
                    $myRequest->request->add(['category' => [$category->id]]);
                    $myRequest->request->add(['submit_type' => 'send-to-approval']);
                    $myRequest->request->add(['limit' => $totalImages]);
                    $myRequest->request->add(['need_to_send_message' => 1]);
                    $myRequest->request->add(['keyword_matched' => $chatMessage->message]);

                    $this->log[] = 'Started function to call the attach image function with : ' . json_encode($myRequest->all());
                    $return      = (new ProductController)->attachImages('customer', $chatMessage->customer_id, null, null, $myRequest);
                    if (! empty($return)) {
                        $this->log[] = 'Total product found for message : ' . $return['total_product'];
                    }
                } catch (Exception $e) {
                    $this->log[] = 'Exception found erro thrown : ' . $e->getMessage() . ' ' . $e->getTraceAsString();
                }
            } else {
                $this->log[] = 'No brand and category matched for the message';
            }
        } else {
            $this->log[] = 'Message type is not message or empty message';
        }

        $addKeyword->comment       = implode("\n\r", $this->log);
        $addKeyword->keyword_match = implode("\n\r", $this->keyword_match);
        $addKeyword->save();
    }

    public function checkWithBrand($sentence)
    {
        foreach ((array) $sentence as $s) {
            $brand = Brand::where('name', 'like', $s)->orderBy('id')->first();
            if ($brand) {
                $this->log[]           = "Brand name matched with '" . $s . "' and id is '" . $brand->id . "'";
                $this->keyword_match[] = $brand->name;

                return $brand;
            } else {
                $this->log[] = "Brand name is not matched with '" . $s . "'";
            }
        }

        return null;
    }

    public function checkWithCategory($sentence)
    {
        foreach ((array) $sentence as $s) {
            $category = Category::where('title', 'like', $s)->first();
            if ($category) {
                $this->log[]           = "Category name matched with '" . $s . "' and id is '" . $category->id . "'";
                $this->keyword_match[] = $category->title;

                return $category;
            } else {
                $this->log[] = "Category name is not matched with '" . $s . "'";
            }
        }
    }
}
