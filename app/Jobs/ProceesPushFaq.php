<?php

namespace App\Jobs;

use App\Models\FaqPlatformDetails;
use App\Models\ReplyLog;
use App\Reply;
use App\StoreWebsite;
use App\StoreWebsiteCategory;
use App\TranslateReplies;
use App\WebsiteStoreView;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ProceesPushFaq implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @param  private  $data
     * @param  private  $reqType
     * @return void
     */
    public function __construct(private $data, private $reqType = 'pushFaq') {}

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        Log::info('ProceesPushFaq Handle function');
        $reply_id = $this->data;
        $reqType = $this->reqType;

        //Loop the records one by one
        $this->_processSingleFaq($reply_id, $reqType);
    }

    private function _processSingleFaq($reply_id, $reqType)
    {
        $Reply = new Reply;
        $searchArray = (array) $reply_id;

        try {
            $replyInfoArray = $Reply
                ->select('replies.store_website_id', 'replies.platform_id', 'store_websites.tag_id', 'magento_url', 'stage_magento_url', 'dev_magento_url', 'stage_api_token', 'dev_api_token', 'api_token', 'replies.reply', 'rep_cat.name', 'replies.category_id', 'replies.id')
                ->join('store_websites', 'store_websites.id', '=', 'replies.store_website_id')
                ->join('reply_categories as rep_cat', 'rep_cat.id', '=', 'replies.category_id')
                ->whereIn('replies.id', $searchArray)
                ->get();

            if (empty($replyInfoArray)) {
                Log::info('Reply ID not found in table'.json_encode($reply_id));

                return false;
            }

            Log::info('Reply Found');

            foreach ($replyInfoArray as $key => $replyInfo) {
                // //get list of all store websites
                if ((isset($replyInfo->tag_id) && $replyInfo->tag_id != '')) {
                    $storeWebsite = new StoreWebsite;
                    $allWebsites = $storeWebsite->getAllTaggedWebsite($replyInfo->tag_id);
                } else {
                    $storeWebsite = new StoreWebsite;
                    $allWebsites = $storeWebsite->where('id', $replyInfo->store_website_id)->get();
                }

                if (! empty($allWebsites)) {
                    foreach ($allWebsites as $websitekey => $websitevalue) {
                        if (empty($websitevalue->id)) {
                            break;
                        }

                        $store_website_id = $websitevalue->id;
                        $websitevalue = $storeWebsite->where('id', $store_website_id)->first();
                        //Get stores of every single site

                        $fetchStores = WebsiteStoreView::join('website_stores as ws', 'ws.id', 'website_store_views.website_store_id')
                            ->join('websites as w', 'w.id', 'ws.website_id')
                            ->join('store_websites as sw', 'sw.id', 'w.store_website_id')
                            ->where('sw.id', $store_website_id)
                            ->select('website_store_views.website_store_id', 'website_store_views.*', 'website_store_views.code')
                            ->get();

                        $stores = [];
                        if (! $fetchStores->isEmpty()) {
                            foreach ($fetchStores as $fetchStore) {
                                $stores[] = $fetchStore->code;
                            }
                        }

                        Log::info('Stores');
                        Log::info($stores);

                        //get the Magento URL and token
                        $api_token = $websitevalue->api_token;

                        //create a payload for API
                        $faqQuestion = $replyInfo->name;

                        $categoryId = $replyInfo->category_id;
                        $parentCategoryId = $replyInfo->category->parent_id;

                        // $faqCategoryId  =   1;
                        if (! empty($replyInfo->magento_url) && ! empty($api_token) && ! empty($stores)) {
                            foreach ($stores as $key => $storeValue) {
                                $url = '';
                                $url = rtrim($replyInfo->magento_url, '/');

                                $faqParentCategoryId = 0;
                                if ($parentCategoryId) {
                                    //get parent category ID
                                    $faqParentCategoryId = (new StoreWebsiteCategory)->getPlatformId($store_website_id, $parentCategoryId, $storeValue);

                                    if (empty($faqParentCategoryId)) {
                                        Log::info('ParentCategory id not available');
                                        $faqParentCategoryId = (new StoreWebsiteCategory)->storeAndGetPlatformId($store_website_id, $parentCategoryId, $storeValue, $url, $api_token, $replyInfo->id);
                                        Log::info('ParentCategory id:'.$faqParentCategoryId);
                                    }

                                    if (empty($faqParentCategoryId)) {
                                        (new ReplyLog)->addToLog($replyInfo->id, 'System unable to generate  FAQ parent category ID on '.$url.' with ID '.$store_website_id.' on store '.$storeValue.' ', 'Push');
                                    }
                                }

                                //get platform id of category
                                $faqCategoryId = (new StoreWebsiteCategory)->getPlatformId($store_website_id, $categoryId, $storeValue);

                                if (empty($faqCategoryId)) {
                                    Log::info('Category d not available');
                                    $faqCategoryId = (new StoreWebsiteCategory)->storeAndGetPlatformId($store_website_id, $categoryId, $storeValue, $url, $api_token, $replyInfo->id);
                                    Log::info('Category id:'.$faqCategoryId);
                                }

                                if (empty($faqCategoryId)) {
                                    (new ReplyLog)->addToLog($replyInfo->id, 'System unable to generate  FAQ category ID on '.$url.' with ID '.$store_website_id.' on store '.$storeValue.' ', 'Push');

                                    continue; // If category id empty then move to next iteration.
                                }

                                $language = isset(explode('-', $storeValue)[1]) && explode('-', $storeValue)[1] != '' ? explode('-', $storeValue)[1] : '';
                                //if reply is already pushed to store then get the information

                                //Get translate reply and basic on language of reply
                                $translateReplies = TranslateReplies::where('translate_to', $language)->where('replies_id', $replyInfo->id)->first();

                                $faqAnswer = (isset($translateReplies->translate_text) && $translateReplies->translate_text != '') ? $translateReplies->translate_text : $replyInfo->reply;

                                if (! empty($translateReplies->translate_text)) {
                                    $platform_id = (new FaqPlatformDetails)->getFaqPlatformId($translateReplies->id, $store_website_id, $storeValue, 'translate');
                                } else {
                                    $platform_id = (new FaqPlatformDetails)->getFaqPlatformId($replyInfo->id, $store_website_id, $storeValue, 'reply');
                                }

                                if (! empty($platform_id)) {
                                    $urlFAQ = $url.'/'.$storeValue.'/rest/V1/faq/'.$platform_id;
                                    $postdata = "{\n        \"faq_category_id\": $faqCategoryId,\n        \"faq_parent_category_id\": $faqParentCategoryId,\n        \"id\": $platform_id,\n        \"faq_question\": \"$faqQuestion\",\n        \"faq_answer\": \"$faqAnswer\",\n        \"is_active\": true,\n        \"sort_order\": 10\n    }";
                                } else {
                                    $urlFAQ = $url.'/'.$storeValue.'/rest/V1/faq';
                                    $postdata = "{\n        \"faq_category_id\": $faqCategoryId,\n        \"faq_parent_category_id\": $faqParentCategoryId,\n        \"faq_question\": \"$faqQuestion\",\n        \"faq_answer\": \"$faqAnswer\",\n        \"is_active\": true,\n        \"sort_order\": 10\n    }";
                                }

                                $ch = curl_init();

                                curl_setopt($ch, CURLOPT_URL, $urlFAQ);
                                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                                curl_setopt($ch, CURLOPT_POST, 1);
                                curl_setopt($ch, CURLOPT_POSTFIELDS, $postdata);

                                $headers = [];
                                $headers[] = 'Authorization: Bearer '.$api_token;
                                $headers[] = 'Content-Type: application/json';
                                curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

                                $response = curl_exec($ch);
                                Log::info(print_r(['FAQ API:', $urlFAQ, $postdata, $api_token, $response], true));
                                $response = json_decode($response);

                                curl_close($ch);

                                //if we got the response
                                if (! empty($response->id) && empty($platform_id)) {
                                    if (! empty($translateReplies->translate_text)) {
                                        $platformDetails = new FaqPlatformDetails;
                                        $platformDetails->reply_id = $replyInfo->id;
                                        $platformDetails->store_website_id = $store_website_id;
                                        $platformDetails->store_code = $storeValue;
                                        $platformDetails->type = 'translate';
                                        $platformDetails->save();
                                    } elseif ($replyInfo->platform_id && ! empty($replyInfo->platform_id)) {
                                        $platformDetails = new FaqPlatformDetails;
                                        $platformDetails->reply_id = $replyInfo->id;
                                        $platformDetails->store_website_id = $store_website_id;
                                        $platformDetails->store_code = $storeValue;
                                        $platformDetails->type = 'reply';
                                        $platformDetails->save();
                                    }
                                }

                                if (! empty($response->id)) { //This means latest is pushed to server
                                    $replyInfo->is_pushed = 1;
                                    $replyInfo->save();

                                    (new ReplyLog)->addToLog($replyInfo->id, 'System pushed FAQ on '.$url.' with ID '.$store_website_id.' on store '.$storeValue.' ', 'Push');

                                    if (! empty($translateReplies->translate_text)) {
                                        //developer can add code to mark the translation pushed or not.
                                    }
                                } else {
                                    (new ReplyLog)->addToLog($replyInfo->id, ' Error while pushing FAQ on Store '.$storeValue.' : '.json_encode($response), 'Push');
                                }
                            }
                        } else {
                            (new ReplyLog)->addToLog($replyInfo->id, ' URL or API token not found linked with this FAQ ', 'Push');
                            Log::info(
                                'URL or API token not found linked with reply id '.
                                json_encode($reply_id)
                            );
                        }
                    }
                }
            }
        } catch (Exception $e) {
            Log::info('Error while pushing faq');
            Log::info($e->getMessage());
            Log::info($e->getLine());
        }
    }
}
