<?php

namespace App\Http\Controllers;
use App\Language;
use App\Http\Controllers\GoogleTranslateController;

use Illuminate\Support\Facades\Session;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Http\JsonResponse;
use App\User;
use App\Setting;
use App\EmailAddress;
use App\StoreWebsite;
use App\AssetsManager;
use App\AssetPlateForm;
use App\AssetsCategory;
use App\GoogleTranslate;
use App\WebsiteStoreView;
use App\ApiResponseMessage;
use Illuminate\Http\Request;
use App\Marketing\WhatsappConfig;
use Illuminate\Support\Facades\Auth;
use App\ApiResponseMessagesTranslation;
use App\ApiResponseMessageValueHistory;
use App\Services\CommonGoogleTranslateService;

class ApiResponseMessageController extends Controller
{
    public function index(Request $request): View
    {
        $api = ApiResponseMessage::with(['storeWebsite']);
        if ($request->store_website_id != '') {
            $api->where('store_website_id', $request->store_website_id);
        }
        if ($request->api_key != '') {
            $api->where('key', 'LIKE', '%' . $request->api_key . '%');
        }
        if ($request->api_value != '') {
            $api->where('value', 'LIKE', '%' . $request->api_value . '%');
        }
        $api_response   = $api->orderByDesc('created_at')->paginate(Setting::get('pagination'));
        $store_websites = StoreWebsite::orderByDesc('created_at')->get();
        if ($request->ajax()) {
            $page  = $request->page;
            $count = ($page - 1) * 15;

            return view('apiResponse/index_ajax', compact('api_response', 'store_websites', 'count'));
        } else {
            return view('apiResponse/index', compact('api_response', 'store_websites'));
        }
    }

    public function store(Request $request): RedirectResponse
    {
        $duplicate = ApiResponseMessage::where('store_website_id', $request->store_website_id)->where('key', $request->res_key)->first();
        if (! empty($duplicate)) {
            Session::flash('message', 'Key already exists for the selected store website');
            Session::flash('alert-class', 'alert-danger');

            return redirect()->route('api-response-message');
        }

        $response                   = new ApiResponseMessage();
        $response->store_website_id = $request->store_website_id;
        $response->key              = $request->res_key;
        $response->value            = $request->res_value;
        if ($response->save()) {
            Session::flash('message', 'Added successfully');
            Session::flash('alert-class', 'alert-success');

            return redirect()->route('api-response-message');
        } else {
            Session::flash('message', 'Something went wrong');
            Session::flash('alert-class', 'alert-danger');

            return redirect()->route('api-response-message');
        }
    }

    public function getEditModal(Request $request): JsonResponse
    {
        $id             = $request->id;
        $store_websites = StoreWebsite::orderByDesc('created_at')->get();
        $data           = ApiResponseMessage::where('id', $id)->first();
        $history        = ApiResponseMessageValueHistory::where('api_response_message_id', $id)->orderByDesc('created_at')->first();
        $returnHTML     = view('apiResponse/ajaxEdit')->with('data', $data)->with('store_websites', $store_websites)->with('history', $history)->render();

        return response()->json(['data' => $returnHTML, 'type' => 'success'], 200);
    }

    public function lodeTranslation(Request $request): JsonResponse
    {
        $id                 = $request->id;
        $apiResponseMessage = ApiResponseMessage::where('id', $id)->first();
        $returnHTML         = "<tr><td colspan='6'>No Data Found!</td></tr>";

        if ($apiResponseMessage) {
            $translations = ApiResponseMessagesTranslation::where('store_website_id', $apiResponseMessage->store_website_id)->where('key', $apiResponseMessage->key)->get();

            if (! $translations->isEmpty()) {
                $returnHTML = '';
                foreach ($translations as $translation) {
                    $returnHTML .= '<tr>';
                    $returnHTML .= '<td>' . $translation->id . '</td>';
                    $returnHTML .= '<td>' . $translation->storeWebsite->title . '</td>';
                    $returnHTML .= '<td>' . $translation->lang_code . '</td>';
                    $returnHTML .= '<td>' . $translation->key . '</td>';
                    $returnHTML .= '<td>' . $translation->value . '</td>';
                    $returnHTML .= '<td>' . $translation->user?->name . '</td>';
                    $returnHTML .= '</tr>';
                }
            }
        }

        return response()->json(['data' => $returnHTML, 'type' => 'success'], 200);
    }

    public function update(Request $request)
    {
        $old_value                  = '';
        $response                   = ApiResponseMessage::where('id', $request->id)->first();
        $old_value                  = $response->value;
        $response->store_website_id = $request->store_website_id;
        $response->key              = $request->key;
        $response->value            = $request->value;
        if ($response->save()) {
            $data = [
                'user_id'                 => Auth::User()->id,
                'api_response_message_id' => $response->id,
                'old_value'               => $old_value,
                'new_value'               => $request->value,
            ];
            ApiResponseMessageValueHistory::insert($data);
            Session::flash('message', 'Updated successfully');
            Session::flash('alert-class', 'alert-success');

            return response()->json(['type' => 'success'], 200);
        } else {
            Session::flash('message', 'Something went wrong');
            Session::flash('alert-class', 'alert-danger');

            return redirect()->route('api-response-message');
        }
    }

    public function destroy($id): RedirectResponse
    {
        if (ApiResponseMessage::where('id', $id)->delete()) {
            Session::flash('message', 'Deleted successfully');
            Session::flash('alert-class', 'alert-success');

            return redirect()->route('api-response-message');
        } else {
            Session::flash('message', 'Something went wrong');
            Session::flash('alert-class', 'alert-danger');

            return redirect()->route('api-response-message');
        }
    }

    public function messageTranslate(Request $request): JsonResponse
    {
        $id = $request->api_response_message_id;

        $apiResponseMessage = ApiResponseMessage::find($id);
        if ($apiResponseMessage) {
            $languages = Language::where('status', 1)->get();
            foreach ($languages as $l) {
                $websiteStoreViews = WebsiteStoreView::with('websiteStore.website.storeWebsite')
                    ->leftJoin('website_stores as ws', 'ws.id', 'website_store_views.website_store_id')->where('website_store_views.name', $l->name)->whereHas('websiteStore', function ($q) use ($apiResponseMessage) {
                        $q->whereHas('website', function ($query) use ($apiResponseMessage) {
                            $query->where('store_website_id', $apiResponseMessage->store_website_id);
                        });
                    })->select('website_store_views.code')->first();

                if ($websiteStoreViews) {
                    $lang_code = $websiteStoreViews->code;
                } else {
                    $websiteStoreViews = WebsiteStoreView::where('name', $l->name)->first();
                    if (! $websiteStoreViews) {
                        continue;
                    }
                    $lang_code = $websiteStoreViews->code;
                }

                $translatedValue = GoogleTranslateController::translateProducts(
                    new CommonGoogleTranslateService,
                    $l->locale,
                    [$apiResponseMessage->value]
                );
                if ($translatedValue == '') {
                    $translatedValue = $apiResponseMessage->value;
                }
                // Save translated text
                ApiResponseMessagesTranslation::updateOrCreate([
                    'store_website_id' => $apiResponseMessage->store_website_id,
                    'key'              => $apiResponseMessage->key,
                    'lang_code'        => $lang_code,
                    'lang_name'        => $l->name,
                ], [
                    'value' => $translatedValue,
                ]);
            }

            return response()->json(['code' => 200, 'data' => [], 'message' => 'Response message translated successfully']);
        }

        return response()->json(['code' => 400, 'data' => [], 'message' => 'There is a problem while translating']);
    }

    public function messageTranslateList(Request $request): View
    {
        $languages                           = Language::where('status', 1)->get();
        $apiResponseMessagesTranslations     = ApiResponseMessagesTranslation::all();
        $apiResponseMessagesTranslationsRows = ApiResponseMessagesTranslation::with('storeWebsite')->groupBy(['store_website_id', 'key'])->latest()->get();

        $rowValues = [];
        foreach ($apiResponseMessagesTranslations as $apiResponseMessagesTranslation) {
            $rowValues[$apiResponseMessagesTranslation->store_website_id][$apiResponseMessagesTranslation->key][$apiResponseMessagesTranslation->lang_name] = [
                'value'               => $apiResponseMessagesTranslation->value,
                'approved_by_user_id' => $apiResponseMessagesTranslation->approved_by_user_id,
            ];
        }

        return view('apiResponse/message-translate-list', compact('languages', 'apiResponseMessagesTranslationsRows', 'rowValues'));
    }

    public function messageTranslateApprove(Request $request): JsonResponse
    {
        $apiResponseMessagesTranslation = ApiResponseMessagesTranslation::where('store_website_id', $request->store_website_id)
            ->where('key', $request->key)
            ->where('lang_name', $request->lang_name)
            ->first();
        if (! $apiResponseMessagesTranslation) {
            return response()->json([
                'code'    => 500,
                'message' => 'Data Not found !!',
            ]);
        }
        $apiResponseMessagesTranslation->value               = $request->value;
        $apiResponseMessagesTranslation->updated_by_user_id  = Auth::User()->id;
        $apiResponseMessagesTranslation->approved_by_user_id = Auth::User()->id;
        $apiResponseMessagesTranslation->save();

        return response()->json([
            'code'             => 200,
            'message'          => 'Approved successfully !!',
            'new_value'        => $request->value,
            'store_website_id' => $request->store_website_id,
            'key'              => $request->key,
            'lang_name'        => $request->lang_name,
        ]);
    }

    public function indexJson(Request $request): JsonResponse
    {
        $assetsManager = AssetsManager::query()->orderByDesc('id')->get();

        return response()->json([
            'items' => (array) $assetsManager->getIterator(),
        ]);
    }

    public function loadTable(Request $request): JsonResponse
    {
        $assetsManagers  = AssetsManager::query()->orderByDesc('id')->get();
        $websites        = StoreWebsite::all();
        $plateforms      = AssetPlateForm::all();
        $emailAddress    = EmailAddress::all();
        $whatsappCon     = WhatsappConfig::all();
        $assets_category = AssetsCategory::all();
        $assets          = AssetsManager::query()->orderByDesc('id')->get();

        $users = User::get()->toArray();

        return response()->json([
            'tpl' => (string) view('partials.modals.assets-manager-listing-modal', [
                'assetsManagers'  => $assetsManagers,
                'websites'        => $websites,
                'plateforms'      => $plateforms,
                'emailAddress'    => $emailAddress,
                'whatsappCon'     => $whatsappCon,
                'assets_category' => $assets_category,
                'assets'          => $assets,
                'users'           => $users,
            ]),
        ]);
    }
}
