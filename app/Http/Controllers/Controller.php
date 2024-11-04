<?php

namespace App\Http\Controllers;

use App\Language;
use App\GoogleTranslate;
use App\WebsiteStoreView;
use App\ApiResponseMessage;
use App\ApiResponseMessagesTranslation;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use App\Services\CommonGoogleTranslateService;

/**
 * @SWG\Swagger(
 *     schemes={"http" , "https"},
 *   basePath="/api" ,
 *
 *   @SWG\Info(
 *     title="API Documentation",
 *     version="1.0",
 *
 *     description="This API documentation list all API endpoints available in applicaiton. Documentation also list Modules and variable available in each Modules",
 *
 *     @SWG\Contact(
 *         email="xyz@xyz.com"
 *     )
 *   )
 * )
 */
class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;

    public function generate_erp_response($key, $store_website_id, $default = '', $lang_code = null)
    {
        $message = $default;

        //check by store_website_id and lang_code and key
        $translated_message = ApiResponseMessagesTranslation::where('store_website_id', $store_website_id)->where('lang_code', $lang_code)->where('key', $key)->first();

        if (! empty($translated_message)) {
            return $translated_message->value;
        }

        $return_message = ApiResponseMessage::where('store_website_id', $store_website_id)->where('key', $key)->first();
        if (! empty($return_message)) {
            $message = $return_message->value;
        }

        if (! empty($lang_code)) {
            //check by lang_code and key
            if (empty($store_website_id)) {
                $translated_message = ApiResponseMessagesTranslation::where('lang_code', $lang_code)
                    ->where('key', $key)
                    ->first();

                if (! empty($translated_message)) {
                    //
                }
            }

            $lan_name = WebsiteStoreView::where('code', $lang_code)->first();

            if (isset($lan_name->name)) {
                //check by  lang_name and key
                $translated_message = ApiResponseMessagesTranslation::where('lang_name', $lan_name->name)
                    ->where('key', $key)
                    ->first();

                if (! empty($translated_message)) {
                    return $translated_message->value;
                }

                $local_code = Language::where('name', $lan_name->name)->first();

                if (isset($local_code->locale)) {
                    $googleTranslate   = new CommonGoogleTranslateService();
                    $translationString = GoogleTranslateController::translateProducts($googleTranslate, $local_code->locale, [$message]);

                    if ($translationString) {
                        $message = $translationString;
                        ApiResponseMessagesTranslation::create([
                            'store_website_id' => $store_website_id,
                            'key'              => $key,
                            'lang_code'        => $lang_code,
                            'lang_name'        => $lan_name->name,
                            'value'            => $message,
                        ]);
                    }
                }
            }
        }

        return $message;
    }
}
