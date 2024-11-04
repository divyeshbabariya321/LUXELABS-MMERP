<?php

namespace Modules\StoreWebsite\Http\Controllers;

use App\LogStoreWebsiteAttributes;
use App\StoreWebsite;
use App\StoreWebsiteAttributes;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\View\View;

class SiteAttributesControllers extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        $title = 'List | Site Attributes';

        return view('storewebsite::site-attributes.index', compact('title'));
    }

    public function log($log_case_id, $attribute_id, $attribute_key, $attribute_val, $store_website_id, $log_msg)
    {
        $log = new LogStoreWebsiteAttributes;
        $log->log_case_id = $log_case_id;
        $log->attribute_id = $attribute_id;
        $log->attribute_key = $attribute_key;
        $log->attribute_val = $attribute_val;
        $log->store_website_id = $store_website_id;
        $log->log_msg = $log_msg;
        $log->save();
    }

    public function attributesHistory(request $request): JsonResponse
    {
        $id = $request->id;
        $html = '';
        $paymentData = LogStoreWebsiteAttributes::where('attribute_id', $id)
            ->get();
        $i = 1;
        if (count($paymentData) > 0) {
            foreach ($paymentData as $history) {
                $html .= '<tr>';
                $html .= '<td>'.$i.'</td>';
                $html .= '<td>'.$history->log_case_id.'</td>';
                $html .= '<td>'.$history->attribute_id.'</td>';
                $html .= '<td>'.$history->attribute_key.'</td>';
                $html .= '<td>'.$history->attribute_val.'</td>';
                $html .= '<td>'.$history->store_website_id.'</td>';
                $html .= '<td>'.$history->log_msg.'</td>';
                $html .= '<td>'.$history->updated_at.'</td>';
                $html .= '</tr>';

                $i++;
            }

            return response()->json(['html' => $html, 'success' => true], 200);
        } else {
            $html .= '<tr>';
            $html .= '<td></td>';
            $html .= '<td></td>';
            $html .= '<td></td>';
            $html .= '<td></td>';
            $html .= '<td></td>';
            $html .= '<td></td>';
            $html .= '<td></td>';
            $html .= '</tr>';
        }

        return response()->json(['html' => $html, 'success' => true], 200);
    }

    /**
     * Store Page
     *
     * @param  Request  $request  [description]
     */
    public function store(Request $request): JsonResponse
    {
        $post = $request->all();
        $validator = Validator::make($post, [
            'attribute_key' => 'required',
            'attribute_val' => 'required',
            'store_website_id' => 'required',
        ]);

        if ($validator->fails()) {
            $outputString = '';
            $messages = $validator->errors()->getMessages();
            foreach ($messages as $k => $errr) {
                foreach ($errr as $er) {
                    $outputString .= "$k : ".$er.'<br>';
                }
            }

            return response()->json(['code' => 500, 'error' => $outputString]);
        }
        $storeWebsiteId = $request->get('store_website_id');
        $websites = StoreWebsite::where('parent_id', '=', $storeWebsiteId)->orWhere('id', '=', $storeWebsiteId)->get();

        $id = $request->get('id', 0);
        $records = StoreWebsiteAttributes::find($id);

        if (! $records) {
            $dataArray = [];
            foreach ($websites as $key => $website) {
                $data['attribute_key'] = $request->get('attribute_key');
                $data['attribute_val'] = $request->get('attribute_val');
                $data['store_website_id'] = $website->id;
                $dataArray[] = $data;
                $this->log('#1', $key + 1, $request->input('attribute_key'), $request->input('attribute_key'), $website->id, 'Store Website Attribute has stored.');
            }
            StoreWebsiteAttributes::insert($dataArray);
        } else {
            $records->fill($post);
            $response = $records->save();
            if ($response) {
                $this->log('#2', $records->id, $request->attribute_key, $request->attribute_val, $request->store_website_id, 'Store Website Attribute has updated.');
            }
        }

        return response()->json(['code' => 200, 'data' => $records]);
    }

    /**
     * Index Page
     *
     * @param  Request  $request  [description]
     */
    public function records(Request $request): JsonResponse
    {
        $StoreWebsiteAttributesViews = StoreWebsiteAttributes::join('store_websites', 'store_websites.id', 'store_website_attributes.store_website_id');
        if ($request->keyword != null) {
            $StoreWebsiteAttributesViews = $StoreWebsiteAttributesViews->where('store_websites.title', 'like', '%'.$request->keyword.'%');
        }
        if ($request->attribute_key != null) {
            $StoreWebsiteAttributesViews = $StoreWebsiteAttributesViews->orWhere('attribute_key', 'like', '%'.$request->attribute_key.'%');
        }
        if ($request->attribute_val != null) {
            $StoreWebsiteAttributesViews = $StoreWebsiteAttributesViews->orWhere('attribute_val', 'like', '%'.$request->attribute_val.'%');
        }
        if ($request->store_website_id != null) {
            $StoreWebsiteAttributesViews = $StoreWebsiteAttributesViews->orWhere('store_website_id', 'like', '%'.$request->store_website_id.'%');
        }

        $StoreWebsiteAttributesViews = $StoreWebsiteAttributesViews->select(['store_website_attributes.*', 'store_websites.website'])->paginate();

        return response()->json(['code' => 200, 'data' => $StoreWebsiteAttributesViews->items(), 'total' => $StoreWebsiteAttributesViews->count(),
            'pagination' => (string) $StoreWebsiteAttributesViews->render(),
        ]);
    }

    /**
     * Add Page
     *
     * @param  Request  $request  [description]
     */
    public function list(Request $request): JsonResponse
    {
        $websitelist = StoreWebsite::all();

        return response()->json(['code' => 200, 'data' => '', 'websitelist' => $websitelist]);
    }

    /**
     * delete Page
     *
     * @param  Request  $request  [description]
     * @param  mixed  $id
     */
    public function delete(Request $request, $id): JsonResponse
    {
        $StoreWebsiteAttributes = StoreWebsiteAttributes::where('id', $id)->first();

        if ($StoreWebsiteAttributes) {
            $StoreWebsiteAttributes->delete();

            return response()->json(['code' => 200]);
        }

        return response()->json(['code' => 500, 'error' => 'Wrong attribute id!']);
    }

    /**
     * Edit Page
     *
     * @param  Request  $request  [description]
     * @param  mixed  $id
     */
    public function edit(Request $request, $id): JsonResponse
    {
        $StoreWebsiteAttributes = StoreWebsiteAttributes::where('id', $id)->first();

        $websitelist = StoreWebsite::all();

        if ($StoreWebsiteAttributes) {
            return response()->json(['code' => 200, 'data' => $StoreWebsiteAttributes, 'websitelist' => $websitelist]);
        }

        return response()->json(['code' => 500, 'error' => 'Wrong site id!']);
    }
}
