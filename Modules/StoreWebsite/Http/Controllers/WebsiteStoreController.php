<?php

namespace Modules\StoreWebsite\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Illuminate\Http\JsonResponse;
use App\Website;
use App\WebsiteStore;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class WebsiteStoreController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $title = 'Website Store | Store Website';

        //$websites = Website::all()->pluck('name', 'id')->toArray();
        $websites = Website::join('store_websites', 'store_websites.id', '=', 'websites.store_website_id')->select('websites.*', DB::raw('CONCAT(websites.name, " (", store_websites.title,")") AS full_name'))->get()->pluck('full_name', 'id')->toArray();

        return view('storewebsite::website-store.index', [
            'title'    => $title,
            'websites' => $websites,
        ]);
    }

    public function records(Request $request): JsonResponse
    {
        $websiteStores = WebsiteStore::leftJoin('websites as w', 'w.id', 'website_stores.website_id');

        // Check for keyword search
        if ($request->keyword != null) {
            $websiteStores = $websiteStores->where(function ($q) use ($request) {
                $q->where('website_stores.name', 'like', '%' . $request->keyword . '%')
                    ->orWhere('website_stores.code', 'like', '%' . $request->keyword . '%');
            });
        }

        if ($request->website_id != null) {
            $websiteStores = $websiteStores->where('website_stores.website_id', $request->website_id);
        }

        $websiteStores = $websiteStores->select(['website_stores.*', 'w.name as website_name'])->orderByDesc('website_stores.id')->paginate();

        return response()->json(['code' => 200, 'data' => $websiteStores->items(), 'total' => $websiteStores->total(), 'pagination' => (string) $websiteStores->render()]);
    }

    public function store(Request $request): JsonResponse
    {
        $post      = $request->all();
        $validator = Validator::make($post, [
            'name'       => 'required',
            'code'       => 'required',
            'website_id' => 'required',
        ]);

        if ($validator->fails()) {
            $outputString = '';
            $messages     = $validator->errors()->getMessages();
            foreach ($messages as $k => $errr) {
                foreach ($errr as $er) {
                    $outputString .= "$k : " . $er . '<br>';
                }
            }

            return response()->json(['code' => 500, 'error' => $outputString]);
        }

        $id = $request->get('id', 0);

        $records = WebsiteStore::find($id);

        if (! $records) {
            $records = new WebsiteStore;
        }

        //$post['code'] = replace_dash($post['code']);

        $records->fill($post);
        // if records has been save then call a request to push
        if ($records->save()) {
        }

        return response()->json(['code' => 200, 'data' => $records]);
    }

    /**
     * Edit Page
     *
     * @param Request $request [description]
     * @param mixed   $id
     */
    public function edit(Request $request, $id): JsonResponse
    {
        $websiteStore = WebsiteStore::where('id', $id)->first();

        if ($websiteStore) {
            return response()->json(['code' => 200, 'data' => $websiteStore]);
        }

        return response()->json(['code' => 500, 'error' => 'Wrong site id!']);
    }

    /**
     * delete Page
     *
     * @param Request $request [description]
     * @param mixed   $id
     */
    public function delete(Request $request, $id): JsonResponse
    {
        $websiteStore = WebsiteStore::where('id', $id)->first();

        if ($websiteStore) {
            $websiteStore->delete();

            return response()->json(['code' => 200]);
        }

        return response()->json(['code' => 500, 'error' => 'Wrong site id!']);
    }

    public function push(Request $request, $id): JsonResponse
    {
        $website = WebsiteStore::where('id', $id)->first();

        if ($website) {
            // check that store store has the platform id exist
            if ($website->website && $website->website->platform_id > 0) {
                $id = \seo2websites\MagentoHelper\MagentoHelper::pushWebsiteStore([
                    'type'       => 'store',
                    'name'       => $website->name,
                    'code'       => replace_dash(strtolower($website->code)),
                    'website_id' => $website->website->platform_id,
                ], $website->website->storeWebsite);

                if (! empty($id) && is_numeric($id)) {
                    $website->platform_id = $id;
                    $website->save();
                } else {
                    return response()->json(['code' => 500, 'data' => $website, 'error' => 'Website-Store push failed']);
                }

                return response()->json(['code' => 200, 'message' => 'Website-Store pushed successfully']);
            } else {
                return response()->json(['code' => 500, 'error' => 'Website platform id is not available!']);
            }
        }

        return response()->json(['code' => 500, 'error' => 'Wrong site id!']);
    }

    public function deteleMultiple(Request $request): JsonResponse
    {
        if ($request->ids) {
            $websiteStores = WebsiteStore::whereIn('id', $request->ids)->delete();
            if ($websiteStores) {
                return response()->json(['code' => 200, 'message' => 'Website-Store Deleted Successfully']);
            }

            return response()->json(['code' => 500, 'error' => "Website store id doesn't exist in this request, please request with a valid store id!"]);
        } else {
            return response()->json(['code' => 500, 'error' => "Website store id doesn't exist in this request, please request with a valid store id!"]);
        }
    }

    public function dropdown(): JsonResponse
    {
        if ($s = request('srch_website_store')) {
            $websites = Website::whereIn('store_website_id', $s)->pluck('id');

            if (! empty($websites)) {
                $websiteStores = WebsiteStore::whereIn('website_id', $websites)->orderBy('name')->pluck('name', 'id')->toArray();

                if ($websiteStores) {
                    $options = ['<option value="" >-- Select Website --</option>'];
                    foreach ($websiteStores as $key => $value) {
                        $options[] = '<option value="' . $key . '" ' . ($key == request('selected') ? 'selected' : '') . ' >' . $value . '</option>';
                    }
                } else {
                    $options = ['<option value="" >No records found.</option>'];
                }
            } else {
                $options = ['<option value="" >No records found.</option>'];
            }

            /*$websiteStores = WebsiteStore::whereRaw('website_id IN (SELECT id FROM websites WHERE store_website_id = ? )', [$s])
                ->orderBy('name', 'ASC')
                ->pluck('name', 'id')
                ->toArray();
            if ($websiteStores) {
                $options = ['<option value="" >-- Select Website --</option>'];
                foreach ($websiteStores as $key => $value) {
                    $options[] = '<option value="' . $key . '" ' . ($key == request('selected') ? 'selected' : '') . ' >' . $value . '</option>';
                }
            } else {
                $options = ['<option value="" >No records found.</option>'];
            }*/
        } else {
            $options = ['<option value="" >Please select website first</option>'];
        }

        return response()->json(['data' => implode('', $options)]);
    }
}
