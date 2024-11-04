<?php

namespace Modules\StoreWebsite\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Controllers\LiveChatController;
use App\Language;
use App\Models\GroupRoute;
use App\StoreViewCodeServerMap;
use App\StoreWebsite;
use App\Website;
use App\WebsiteStore;
use App\WebsiteStoreView;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\View\View;

class WebsiteStoreViewController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $title = 'Website Store View | Store Website';
        $storeWebsites = StoreWebsite::orderBy('title')->pluck('title', 'id')->toArray();

        $websiteStores = WebsiteStore::join('websites', 'websites.id', '=', 'website_stores.website_id')->join('store_websites', 'store_websites.id', '=', 'websites.store_website_id')->orderBy('websites.name')->select('websites.*', DB::raw('CONCAT(website_stores.name, " (", store_websites.title,")") AS full_name'))->pluck('full_name', 'id')->toArray();

        $languages = Language::orderBy('name')->pluck('name', 'name')->toArray();
        $storeServers = StoreViewCodeServerMap::groupBy('server_id')->get();

        return view('storewebsite::website-store-view.index', [
            'title' => $title,
            'storeWebsites' => $storeWebsites,
            'websiteStores' => $websiteStores,
            'languages' => $languages,
            'storeServers' => $storeServers,
        ]);
    }

    public function records(Request $request): JsonResponse
    {
        $websiteStoreViews = WebsiteStoreView::with('websiteStore.website.storeWebsite')
            ->leftJoin('website_stores as ws', 'ws.id', 'website_store_views.website_store_id');

        // Check for keyword search
        if ($request->keyword != null) {
            $websiteStoreViews = $websiteStoreViews->where(function ($q) use ($request) {
                $q->where('website_store_views.name', 'like', '%'.$request->keyword.'%');
            });
        }
        if ($request->website_store != null) {
            $websiteStoreViews = $websiteStoreViews->whereHas('websiteStore', function ($q) use ($request) {
                $q->whereHas('website', function ($query) use ($request) {
                    //$query->where('store_website_id', $request->website_store);
                    $query->whereIn('store_website_id', $request->website_store);
                });
            });
        }
        if ($request->website_store_id != null) {
            //$websiteStoreViews = $websiteStoreViews->where('website_store_id', $request->website_store_id);
            $websiteStoreViews = $websiteStoreViews->whereIn('website_store_id', $request->website_store_id);
        }

        $websiteStoreViews = $websiteStoreViews->select(['website_store_views.*', 'ws.name as website_store_name'])
            ->orderByDesc('website_store_views.id')
            ->paginate();

        return response()->json(['code' => 200, 'data' => $websiteStoreViews->items(), 'total' => $websiteStoreViews->total(), 'pagination' => (string) $websiteStoreViews->render()]);
    }

    public function store(Request $request): JsonResponse
    {
        $post = $request->all();
        $validator = Validator::make($post, [
            'name' => 'required',
            'code' => 'required',
            'website_store_id' => 'required',
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

        $id = $request->get('id', 0);

        $records = WebsiteStoreView::find($id);

        if (! $records) {
            $records = new WebsiteStoreView;
        }

        //$post['code'] = replace_dash($post['code']);

        $records->fill($post);
        if ($records->save()) {
            // check that store store has the platform id exist
        }

        return response()->json(['code' => 200, 'data' => $records]);
    }

    /**
     * Edit Page
     *
     * @param  Request  $request  [description]
     * @param  mixed  $id
     */
    public function edit(Request $request, $id): JsonResponse
    {
        $websiteStoreView = WebsiteStoreView::where('id', $id)->first();

        if ($websiteStoreView) {
            return response()->json(['code' => 200, 'data' => $websiteStoreView]);
        }

        return response()->json(['code' => 500, 'error' => 'Wrong site id!']);
    }

    /**
     * delete Page
     *
     * @param  Request  $request  [description]
     * @param  mixed  $id
     */
    public function delete(Request $request, $id): JsonResponse
    {
        $websiteStoreView = WebsiteStoreView::where('id', $id)->first();

        if ($websiteStoreView) {
            $websiteStoreView->delete();

            return response()->json(['code' => 200]);
        }

        return response()->json(['code' => 500, 'error' => 'Wrong site id!']);
    }

    public function push(Request $request, $id): JsonResponse
    {
        $website = WebsiteStoreView::where('id', $id)->first();

        if ($website) {
            // check that store store has the platform id exist
            if ($website->websiteStore && $website->websiteStore->platform_id > 0 && $website->websiteStore->website->platform_id > 0) {
                $id = \seo2websites\MagentoHelper\MagentoHelper::pushWebsiteStoreView([
                    'type' => 'store_view',
                    'name' => $website->name,
                    'code' => replace_by_sign($website->code),
                    'website_id' => $website->websiteStore->website->platform_id,
                    'group_id' => $website->websiteStore->platform_id,
                ], $website->websiteStore->website->storeWebsite);

                if (! empty($id) && is_numeric($id)) {
                    $website->platform_id = $id;
                    $website->save();
                } else {
                    return response()->json(['code' => 500, 'data' => $website, 'error' => 'Website-Store-View push failed']);
                }

                return response()->json(['code' => 200, 'message' => 'Website-Store-View pushed successfully']);
            } else {
                return response()->json(['code' => 500, 'error' => 'Website-Store platform id is not available!']);
            }
        }

        return response()->json(['code' => 500, 'error' => 'Wrong site id!']);
    }

    public function editGroup(Request $request, $id, $store_group_id): JsonResponse
    {
        $postURL = 'https://api.livechatinc.com/v3.2/configuration/action/get_group';

        $postData = [
            'id' => (int) $request->store_group_id,
            'fields' => ['agent_priorities', 'routing_status'],
        ];
        $postData = json_encode($postData, true);
        $result = app(LiveChatController::class)->curlCall($postURL, $postData, 'application/json', true, 'POST');

        if ($result['err']) {
            return response()->json(['status' => 'errors', 'errorMsg' => $result['err']], 403);
        } else {
            $response = json_decode($result['response']);
            if (isset($response->error)) {
                return response()->json(['status' => 'errors', $response], 403);
            } else {
                $response->row_id = (int) $request->id;
                $websiteStoreView = WebsiteStoreView::find($request->id);
                $response->type = 'edit';
                $response->ref_theme_group_id = $websiteStoreView->ref_theme_group_id;
                $response->agents = ($this->agents($request))->original['responseData'];
                $response->group_route = GroupRoute::where('group_id', $websiteStoreView->store_group_id)->latest()->first();

                return response()->json(['status' => 'success', 'responseData' => $response], 200);
            }
        }
    }

    public function storeGroup(Request $request): JsonResponse
    {
        $postData = $request->all();

        $validator = Validator::make($postData, [
            'name' => 'required',
            'agents' => 'required',
            'priorites' => 'required',
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

        $agent_priorities = [];
        foreach ($request->priorites as $key => $val) {
            $agent_priorities[$postData['agents'][$key]] = $val;
        }

        $id = (int) $request->id;
        $row_id = (int) $request->row_id;

        if ($id) {
            $postURL = 'https://api.livechatinc.com/v3.2/configuration/action/update_group';
        } else {
            $postURL = 'https://api.livechatinc.com/v3.2/configuration/action/create_group';
        }

        $postData = [
            'id' => $id ?? '',
            'name' => $request->name,
            'language_code' => $request->language_code,
            'agent_priorities' => $agent_priorities,
        ];

        $postData = json_encode($postData, true);
        $result = app(LiveChatController::class)->curlCall($postURL, $postData, 'application/json', true, 'POST');

        if ($result['err']) {
            return response()->json(['status' => 'errors', 'errorMsg' => $result['err']], 403);
        } else {
            $response = json_decode($result['response']);
            if (isset($response->error)) {
                return response()->json(['status' => 'errors', 'error' => $response->error->message, $response->error->message], 200);
            } else {
                $websiteStoreView = WebsiteStoreView::where('id', $row_id)->first();
                if ($id) {
                    $store_group_id = $id;
                } else {
                    $store_group_id = $response->id;
                }
                $group_id = $request->group;
                if ($group_id) {
                    $postURL1 = 'https://api.livechatinc.com/v2/properties/group/'.$group_id;
                    $result1 = app(LiveChatController::class)->curlCall($postURL1, [], 'application/json', true, 'GET');
                    $postURL2 = 'https://api.livechatinc.com/v2/properties/group/'.$store_group_id;
                    $result2 = app(LiveChatController::class)->curlCall($postURL2, $result1['response'], 'application/json', true, 'PUT');
                    $response->group_details1 = json_decode($result1['response']);
                    $response->group_details2 = json_decode($result2['response']);
                }
                if ($websiteStoreView) {
                    $websiteStoreView->store_group_id = $store_group_id;
                    $websiteStoreView->ref_theme_group_id = $group_id;
                    $websiteStoreView->save();
                }
                //Create route fo group

                if (! empty($request->route_url) && ! empty($request->route_domain)) {
                    $group_route = GroupRoute::where('group_id', $store_group_id)->latest()->first();
                    if ($group_route) {
                        $postURL = 'https://api.livechatinc.com/v3.3/configuration/action/update_auto_access';
                    } else {
                        $postURL = 'https://api.livechatinc.com/v3.3/configuration/action/add_auto_access';
                    }
                    $domain_values['value'] = $request->route_domain;
                    $url_values['value'] = $request->route_url;
                    $postData = [
                        'id' => $group_route == null ? null : $group_route->route_id,
                        'description' => $request->route_name,
                        'access' => [
                            'groups' => [$store_group_id],
                        ],
                        'conditions' => [
                            'domain' => [
                                'values' => [$domain_values],
                            ],
                            'url' => [
                                'values' => [$url_values],
                            ],
                        ],
                    ];
                    $postData = json_encode($postData, true);
                    $result = app(LiveChatController::class)->curlCall($postURL, $postData, 'application/json', true, 'POST');
                    $response = json_decode($result['response']);
                    if (! isset($response->error)) {
                        GroupRoute::updateOrInsert([
                            'group_id' => $store_group_id,
                        ], [
                            'group_id' => $store_group_id,
                            'route_id' => $group_route != null ? $group_route->route_id : $response->id,
                            'route_name' => $request->route_name,
                            'domain' => $request->route_domain,
                            'url' => $request->route_url,
                        ]);
                    }
                }

                return response()->json(['status' => 'success', 'responseData' => $response, 'code' => 200], 200);
            }
        }
    }

    public function deleteGroup(Request $request, $id, $store_group_id): JsonResponse
    {
        $id = (int) $request->id;
        $store_group_id = (int) $request->store_group_id;

        $postURL = 'https://api.livechatinc.com/v3.2/configuration/action/delete_group';

        $postData = [
            'id' => $store_group_id,
        ];
        $postData = json_encode($postData, true);
        $result = app(LiveChatController::class)->curlCall($postURL, $postData, 'application/json', true, 'POST');

        if ($result['err']) {
            return response()->json(['status' => 'errors', 'errorMsg' => $result['err']], 403);
        } else {
            $response = json_decode($result['response']);
            if (isset($response->error)) {
                return response()->json(['status' => 'errors', $response], 403);
            } else {
                $websiteStoreView = WebsiteStoreView::where('id', $id)->first();

                if ($websiteStoreView) {
                    $websiteStoreView->store_group_id = null;
                    $websiteStoreView->save();

                    return response()->json(['code' => 200]);
                }
            }
        }
    }

    public function agents(Request $request): JsonResponse
    {
        $postURL = 'https://api.livechatinc.com/v3.2/configuration/action/list_agents';

        $postData = [
            'fields' => [
                'max_chats_count',
                'job_title',
            ],
            'filters' => [
                'group_ids' => [
                    0,
                    1,
                ],
            ],
        ];
        $postData = json_encode($postData, true);
        $result = app(LiveChatController::class)->curlCall($postURL, $postData, 'application/json', true, 'POST');

        if ($result['err']) {
            return response()->json(['status' => 'errors', 'errorMsg' => $result['err']], 403);
        } else {
            $response = json_decode($result['response']);
            if (isset($response->error)) {
                return response()->json(['status' => 'errors', $response], 403);
            } else {
                return response()->json(['status' => 'success', 'responseData' => $response], 200);
            }
        }
    }

    public function groups(Request $request): JsonResponse
    {
        $postURL = 'https://api.livechatinc.com/v3.2/configuration/action/list_groups';

        $postData = [
            'fields' => ['agent_priorities', 'routing_status'],
        ];
        $postData = json_encode($postData, true);
        $result = app(LiveChatController::class)->curlCall($postURL, $postData, 'application/json', true, 'POST');

        if ($result['err']) {
            return response()->json(['status' => 'errors', 'errorMsg' => $result['err']], 403);
        } else {
            $response = json_decode($result['response']);
            $groups = [];
            foreach ($response as $res) {
                if (str_starts_with($res->name, 'theme')) {
                    $g['id'] = $res->id;
                    $g['name'] = $res->name;
                    $groups[] = $g;
                }
            }
            if (isset($response->error)) {
                return response()->json(['status' => 'errors', $response], 403);
            } else {
                return response()->json(['status' => 'success', 'responseData' => $groups], 200);
            }
        }
    }

    /**
     * Update store website of a website store view
     *
     * @param  $id
     * @param  $storeWebsiteId
     */
    public function updateStoreWebsite(Request $request): JsonResponse
    {
        $storeWebsiteId = $request->input('store_website_id');
        $selectedStoreViews = $request->input('selected_store_views');
        if (count($selectedStoreViews) == 0) {
            return response()->json(['code' => 500, 'message' => 'Select at least on store view to update the website!']);
        }

        $count = 0;
        foreach ($selectedStoreViews as $key => $views) {
            $websiteStoreView = WebsiteStoreView::find($views);
            if (! $storeWebsiteId || ! $websiteStoreView->websiteStore || ! $websiteStoreView->websiteStore->website) {
                return response()->json(['code' => 500, 'message' => 'Record not found!']);
            }

            $websiteId = $websiteStoreView->websiteStore->website->id;
            $website = Website::find($websiteId);
            $website->store_website_id = $storeWebsiteId;
            $response = $website->save();
            $count++;
        }
        if ($response && $count == $key + 1) {
            return response()->json(['code' => 200, 'message' => 'Website updated successfully!']);
        } else {
            return response()->json(['code' => 500, 'message' => 'Something went wrong!']);
        }
    }
}
