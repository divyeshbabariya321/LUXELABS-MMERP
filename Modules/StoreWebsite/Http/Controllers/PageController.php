<?php

namespace Modules\StoreWebsite\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Controllers\GoogleTranslateController;
use App\Jobs\PushPageToMagento;
use App\Language;
use App\Models\StoreWebsiteStatus;
use App\Models\StoreWebsiteStatusHistory;
use App\Services\CommonGoogleTranslateService;
use App\StoreWebsite;
use App\StoreWebsitePage;
use App\StoreWebsitePageHistory;
use App\StoreWebsitePagePullLog;
use App\User;
use App\WebsiteStoreView;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\View\View;
use seo2websites\MagentoHelper\MagentoHelper;
use Spatie\Activitylog\Models\Activity;

class PageController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $title = 'Pages | Store Website';

        $storeWebsites = StoreWebsite::all()->pluck('website', 'id');
        $storeWebsitesModel = StoreWebsite::query()
            ->select('title', 'magento_url')
            ->pluck('magento_url', 'title');
        $pages = StoreWebsitePage::join('store_websites as  sw', 'sw.id', 'store_website_pages.store_website_id')
            ->select([DB::raw("concat(store_website_pages.title,'-',sw.title) as page_name"), 'store_website_pages.id'])
            ->pluck('page_name', 'id');

        $languages = Language::pluck('locale', 'code')->toArray(); //

        $languagesList = Language::pluck('name', 'name')->toArray(); //
        $statuses = StoreWebsiteStatus::all();
        $users = User::all();

        return view('storewebsite::page.index', [
            'title' => $title,
            'storeWebsites' => $storeWebsites,
            'pages' => $pages,
            'languages' => $languages,
            'languagesList' => $languagesList,
            'statuses' => $statuses,
            'users' => $users,
            'storeWebsitesModel' => $storeWebsitesModel,
        ]);
    }

    public function pageMetaTitleKeywords(): View
    {
        $title = 'Pages | Store Website';

        $storeWebsites = StoreWebsite::all()->pluck('website', 'id');
        $pages = StoreWebsitePage::join('store_websites as  sw', 'sw.id', 'store_website_pages.store_website_id')
            ->select([DB::raw("concat(store_website_pages.title,'-',sw.title) as page_name"), 'store_website_pages.id'])
            ->pluck('page_name', 'id');

        $languages = Language::pluck('locale', 'code')->toArray(); //

        $languagesList = Language::pluck('name', 'name')->toArray(); //
        $statuses = StoreWebsiteStatus::all();
        $users = User::all();

        return view('storewebsite::page.keywords', [
            'title' => $title,
            'storeWebsites' => $storeWebsites,
            'pages' => $pages,
            'languages' => $languages,
            'languagesList' => $languagesList,
            'statuses' => $statuses,
            'users' => $users,
        ]);
    }

    public function records(Request $request): JsonResponse
    {
        $pages = StoreWebsitePage::leftJoin('store_websites as sw', 'sw.id', 'store_website_pages.store_website_id')->leftJoin('users as u', 'u.id', 'store_website_pages.approved_by_user_id')->leftJoin('website_store_views_status as s', 's.id', 'store_website_pages.website_store_views_status_id');

        $statuses = StoreWebsiteStatus::all();

        // Check for keyword search
        if ($request->keyword != null) {
            $pages = $pages->where(function ($q) use ($request) {
                $q->where('store_website_pages.title', 'like', '%'.$request->keyword.'%')
                    ->orWhere('store_website_pages.content', 'like', '%'.$request->keyword.'%');
            });
        }

        if ($request->language != null) {
            $pages = $pages->whereIn('store_website_pages.language', $request->language);
        }

        if ($request->store_website_id != null) {
            $pages = $pages->whereIn('store_website_pages.store_website_id', $request->store_website_id);
        }

        if ($request->status_id != null) {
            $pages = $pages->whereIn('store_website_pages.website_store_views_status_id', $request->status_id);
        }

        if ($request->user_id != null) {
            $pages = $pages->whereIn('store_website_pages.approved_by_user_id', $request->user_id);
        }

        if ($request->is_pushed != '') {
            $pages = $pages->where('store_website_pages.is_pushed', $request->is_pushed);
        }

        if ($request->version_pushed != '') {
            $pages = $pages->where('store_website_pages.is_latest_version_pushed', $request->version_pushed);
        }

        if ($request->version_trans != '') {
            $pages = $pages->where('store_website_pages.is_latest_version_translated', $request->version_trans);
        }
        if ($request->revision_trans != '') {
            $pages = $pages->where('store_website_pages.is_latest_version_pushed', $request->revision_trans);
        }

        if ($request->date != '') {
            $pages = $pages->where('store_website_pages.created_at', 'LIKE', '%'.$request->date.'%');
        }

        if ($request->search_title != '') {
            $pages = $pages->where('store_website_pages.title', 'LIKE', '%'.$request->search_title.'%');
        }

        if ($request->search_url != '') {
            $pages = $pages->where('store_website_pages.url_key', 'LIKE', '%'.$request->search_url.'%');
        }

        $pages = $pages->orderByDesc('store_website_pages.id')->select(['store_website_pages.*', 'sw.website as store_website_name', 'u.name as approved_by', 's.color as colorcode'])->paginate();

        $items = $pages->items();

        $recItems = [];
        foreach ($items as $item) {
            $attributes = $item->getAttributes();
            $attributes['stores_small'] = strlen($attributes['stores']) > 15 ? substr($attributes['stores'], 0, 15) : $attributes['stores'];
            $attributes['stores'] = $attributes['stores'];
            $recItems[] = $attributes;
        }

        return response()->json(['code' => 200, 'pageUrl' => $request->page_url, 'data' => $recItems, 'statuses' => $statuses, 'total' => $pages->total(),
            'pagination' => (string) $pages->links(),
        ]);
    }

    public function getReviewTranslateRecords(Request $request): JsonResponse
    {
        $pages = StoreWebsitePage::leftJoin('store_websites as sw', 'sw.id', 'store_website_pages.store_website_id');

        // Check for keyword search
        if ($request->keyword != null) {
            $pages = $pages->where(function ($q) use ($request) {
                $q->where('store_website_pages.title', 'like', '%'.$request->keyword.'%')
                    ->orWhere('store_website_pages.content', 'like', '%'.$request->keyword.'%');
            });
        }

        if ($request->language != null) {
            $pages = $pages->where('store_website_pages.language', $request->language);
        }

        if ($request->store_website_id != null) {
            $pages = $pages->where('store_website_pages.store_website_id', $request->store_website_id);
        }

        if ($request->store_website_id != null) {
            $pages = $pages->where('store_website_pages.store_website_id', $request->store_website_id);
        }

        if ($request->is_pushed != '') {
            $pages = $pages->where('store_website_pages.is_pushed', $request->is_pushed);
        }

        $pages->where('store_website_pages.is_flagged_translation', 1);
        $pages = $pages->orderByDesc('store_website_pages.id')->select(['store_website_pages.*', 'sw.website as store_website_name'])->paginate();

        $items = $pages->items();

        $recItems = [];
        foreach ($items as $item) {
            $attributes = $item->getAttributes();
            $attributes['stores_small'] = strlen($attributes['stores']) > 15 ? substr($attributes['stores'], 0, 15) : $attributes['stores'];
            $attributes['stores'] = $attributes['stores'];
            $attributes['original_page'] = StoreWebsitePage::where('url_key', $item->url_key)->where('store_website_id', $item->store_website_id)->where('id', $item->translated_from)->first();
            $recItems[] = $attributes;
        }

        return response()->json(['code' => 200, 'pageUrl' => $request->page_url, 'data' => $recItems, 'total' => $pages->total(),
            'pagination' => (string) $pages->links(),
        ]);
    }

    public function reviewTranslate(Request $request, $language = '')
    {
        $title = 'Pages - Review Translate:'.$language.' | Store Website';
        $languagesList = Language::pluck('name', 'name')->toArray();
        if (! empty($languagesList) && $language == '') {
            $first = reset($languagesList);

            return redirect()->route('store-website.page.review.translate', ['language' => $first]);
        }

        $storeWebsites = StoreWebsite::all()->pluck('website', 'id');
        $pages = StoreWebsitePage::join('store_websites as  sw', 'sw.id', 'store_website_pages.store_website_id')
            ->select([DB::raw("concat(store_website_pages.title,'-',sw.title) as page_name"), 'store_website_pages.id'])
            ->where('store_website_pages.language', $language)
            ->where('is_flagged_translation', 1)
            ->pluck('page_name', 'id');

        $languages = Language::pluck('locale', 'code')->toArray(); //

        return view('storewebsite::page.review-translate', [
            'title' => $title,
            'storeWebsites' => $storeWebsites,
            'pages' => $pages,
            'languages' => $languages,
            'languagesList' => $languagesList,
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $post = $request->all();
        $id = $request->get('id', 0);

        try {
            if (! empty($post['copy_to']) && is_array($post['copy_to'])) {
                $pages = StoreWebsitePage::whereIn('id', $post['copy_to'])->getModels();

                foreach ($pages as $page) {
                    $page->content = $post['content'];
                    $page->save();
                }

                return response()->json(['code' => 200, 'data' => $pages]);
            }
        } catch (Exception|\Throwable $e) {
        }

        $params = [
            'title' => 'required',
            'content' => 'required',
            'language' => 'required',
            //'stores'           => 'required',
            //'store_website_id' => 'required',
        ];

        if (empty($id)) {
            $params['stores'] = 'required';
            $params['store_website_id'] = 'required';
        }

        $validator = Validator::make($post, $params);

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

        $records = StoreWebsitePage::find($id);

        if (! $records) {
            $records = new StoreWebsitePage;
        } else {
            if (is_null($records->translated_from)) {
                StoreWebsitePage::where('url_key', $records['url_key'])->where('store_website_id', $records['store_website_id'])->update(['is_latest_version_translated' => 0, 'is_latest_version_pushed' => 0]);
            }
        }

        if (empty($id)) {
            $post['stores'] = implode(',', $post['stores']);
            $string = str_replace(' ', '-', strtolower($post['title'])); // Replaces all spaces with hyphens.
            $string = preg_replace('/[^A-Za-z0-9\-]/', '', $string); // Removes special chars.
            $post['url_key'] = $string;
        }

        if (! empty($post['stores_str'])) {
            $post['stores'] = $post['stores_str'];
        }

        $post['is_pushed'] = 0;
        $post['is_latest_version_translated'] = 1;
        $post['is_latest_version_pushed'] = 0;
        $records->fill($post);
        if ($request->has('approved_by_user_id')) {
            activity()->causedBy(auth()->user())->performedOn($records)->log('Translation Approved by:'.auth()->user()?->name);
        }
        // if records has been save then call a request to push
        if ($records->save()) {
            //Logging activity
            if ($id == 0) {
                activity()->causedBy(auth()->user())->performedOn($records)->log('page created');
            } else {
                activity()->causedBy(auth()->user())->performedOn($records)->log('page updated');
            }

            $page = StoreWebsitePage::find($records->id);
            if (is_null($page->translated_from)) {
                $languages = Language::where('status', 1)->get();
                foreach ($languages as $l) {
                    if (strtolower($page->language) != strtolower($l->name)) {
                        $pageExist = StoreWebsitePage::where('url_key', $page->url_key)->where('store_website_id', $page->store_website_id)->where('language', $l->name)->first();
                        if (! $pageExist) {
                            $newPage = new StoreWebsitePage;
                        } else {
                            $newPage = StoreWebsitePage::find($pageExist->id);
                        }

                        $title = GoogleTranslateController::translateProducts(
                            new CommonGoogleTranslateService,
                            $l->locale,
                            [$page->title]
                        );

                        $metaTitle = GoogleTranslateController::translateProducts(
                            new CommonGoogleTranslateService,
                            $l->locale,
                            [$page->meta_title]
                        );

                        $metaKeywords = GoogleTranslateController::translateProducts(
                            new CommonGoogleTranslateService,
                            $l->locale,
                            [$page->meta_keywords]
                        );

                        $metaDescription = GoogleTranslateController::translateProducts(
                            new CommonGoogleTranslateService,
                            $l->locale,
                            [$page->meta_description]
                        );

                        $contentHeading = GoogleTranslateController::translateProducts(
                            new CommonGoogleTranslateService,
                            $l->locale,
                            [$page->content_heading]
                        );

                        $content = $page->content;
                        if (Str::contains($page->content, ['{{widget', '{{ widget', '{{WIDGET', '{{ WIDGET']) === false) {
                            $content = GoogleTranslateController::translateProducts(
                                new CommonGoogleTranslateService,
                                $l->locale,
                                [$page->content]
                            );
                        }

                        // assign the stores  column
                        $fetchStores = WebsiteStoreView::where('website_store_views.name', $l->name)
                            ->join('website_stores as ws', 'ws.id', 'website_store_views.website_store_id')
                            ->join('websites as w', 'w.id', 'ws.website_id')
                            ->where('w.store_website_id', $page->store_website_id)
                            ->select('website_store_views.*')
                            ->get();

                        $stores = [];
                        if (! $fetchStores->isEmpty()) {
                            foreach ($fetchStores as $fetchStore) {
                                $stores[] = $fetchStore->code;
                            }
                        }

                        $newPage->title = ! empty($title) ? $title : $page->title;
                        $newPage->meta_title = ! empty($metaTitle) ? $metaTitle : $page->meta_title;
                        $newPage->meta_keywords = ! empty($metaKeywords) ? $metaKeywords : $page->meta_keywords;
                        $newPage->meta_description = ! empty($metaDescription) ? $metaDescription : $page->meta_description;
                        $newPage->content_heading = ! empty($contentHeading) ? $contentHeading : $page->content_heading;
                        $newPage->content = ! empty($content) ? $content : $page->content;
                        $newPage->layout = $page->layout;
                        $newPage->url_key = $page->url_key;
                        $newPage->active = $page->active;
                        $newPage->stores = implode(',', $stores);
                        $newPage->store_website_id = $page->store_website_id;
                        $newPage->language = $l->name;
                        $newPage->copy_page_id = $page->id;
                        $newPage->translated_from = $page->id;
                        $newPage->is_pushed = 0;
                        $newPage->is_latest_version_pushed = 0;
                        $newPage->is_latest_version_translated = 1;
                        $newPage->is_flagged_translation = 1;
                        $newPage->save();

                        //PushPageToMagento::dispatch($newPage)->onQueue('magetwo');
                        //StoreWebsitePage::where('id', $newPage->id)->update(['is_pushed' => 1, 'is_latest_version_pushed' => 1]);

                        activity()->causedBy(auth()->user())->performedOn($page)->log('page translated to '.$l->name);
                        activity()->causedBy(auth()->user())->performedOn($newPage)->log('Parent Page Title:'.$newPage->title.' Page URL Key:'.$newPage->url_key);
                        /*else{
                            $errorMessage[] = "Page not pushed because of page already copied to {$pageExist->url_key} for {$l->name}";
                        }*/
                    }
                }
            }
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
        $page = StoreWebsitePage::where('id', $id)->first();

        if ($page) {
            $page->website_url = ! empty($page->storeWebsite) ? $page->storeWebsite->website : '';

            return response()->json(['code' => 200, 'data' => $page]);
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
        $page = StoreWebsitePage::where('id', $id)->first();

        if ($page) {
            $page->delete();
            activity()->causedBy(auth()->user())->performedOn($page)->log('page deleted');

            return response()->json(['code' => 200]);
        }

        return response()->json(['code' => 500, 'error' => 'Wrong site id!']);
    }

    public function push(Request $request, $id): JsonResponse
    {
        $page = StoreWebsitePage::where('id', $id)->first();

        if ($page) {
            $updated_by = auth()->user();
            PushPageToMagento::dispatch($page, $updated_by)->onQueue('magetwo');
            StoreWebsitePage::where('id', $id)->update(['is_pushed' => 1, 'is_latest_version_pushed' => 1]);

            return response()->json(['code' => 200, 'message' => 'Website send for push']);
        }

        return response()->json(['code' => 500, 'error' => 'Wrong site id!']);
    }

    public function pull(Request $request, $id): JsonResponse
    {
        $page = StoreWebsitePage::where('id', $id)->first();

        if ($page) {
            $website = $page->storeWebsite;
            $data = MagentoHelper::pullWebsitePage($website);
            if (! empty($data)) {
                foreach ($data as $key => $d) {
                    if ($page->platform_id == $d->id) {
                        activity()->causedBy(auth()->user())->performedOn($page)->log('page pulled');
                        $page->title = isset($d->title) ? $d->title : '';
                        $page->url_key = isset($d->identifier) ? $d->identifier : '';
                        $page->layout = isset($d->page_layout) ? $d->page_layout : '';
                        $page->meta_title = isset($d->meta_title) ? $d->meta_title : '';
                        $page->meta_keywords = isset($d->meta_keywords) ? $d->meta_keywords : '';
                        $page->meta_description = isset($d->meta_description) ? $d->meta_description : '';
                        $page->content_heading = isset($d->content_heading) ? $d->content_heading : '';
                        $page->content = isset($d->content) ? $d->content : '';
                        $page->created_at = isset($d->creation_time) ? $d->creation_time : '';
                        $page->updated_at = isset($d->update_time) ? $d->update_time : '';
                        $page->is_pushed = 0;
                        $page->save();

                        StoreWebsitePagePullLog::create(['title' => isset($d->title) ? $d->title : '',
                            'meta_title' => isset($d->meta_title) ? $d->meta_title : '',
                            'meta_keywords' => isset($d->meta_keywords) ? $d->meta_keywords : '',
                            'meta_description' => isset($d->meta_description) ? $d->meta_description : '',
                            'content_heading' => isset($d->content_heading) ? $d->content_heading : '',
                            'content' => isset($d->content) ? $d->content : '',
                            'layout' => isset($d->page_layout) ? $d->page_layout : '',
                            'url_key' => isset($d->identifier) ? $d->identifier : '',
                            'platform_id' => $d->id,
                            'page_id' => $page->id,
                            'store_website_id' => $website->id,
                            'response_type' => 'success', ]);
                    }
                }
            } else {
                StoreWebsitePagePullLog::create([
                    'page_id' => $page->id,
                    'store_website_id' => $website->id,
                    'response_type' => 'error', ]);
            }

            return response()->json(['code' => 200, 'message' => 'Website send for pull']);
        }

        return response()->json(['code' => 500, 'error' => 'Wrong site id!']);
    }

    public function getStores(Request $request, $id): JsonResponse
    {
        $stores = StoreWebsite::join('websites as w', 'w.store_website_id', 'store_websites.id')
            ->join('website_stores as ws', 'ws.website_id', 'w.id')
            ->join('website_store_views as wsv', 'wsv.website_store_id', 'ws.id')
            ->where('w.store_website_id', $id)
            ->select('wsv.*')
            ->get();

        return response()->json(['code' => 200, 'stores' => $stores]);
    }

    public function loadPage(Request $request, $id): JsonResponse
    {
        $page = StoreWebsitePage::find($id);

        if ($page) {
            $language = $request->language;

            // do by lanuage
            if (! empty($language)) {
                $translateDescription = GoogleTranslateController::translateProducts(
                    new CommonGoogleTranslateService,
                    $language,
                    [$page->content]
                );

                return response()->json(['code' => 200, 'content' => ! empty($translateDescription) ? $translateDescription : $page->content]);
            } else {
                return response()->json(['code' => 200, 'content' => $page->content, 'meta_title' => $page->meta_title, 'meta_keyword' => $page->meta_keywords, 'meta_desc' => $page->meta_description]);
            }
        }
    }

    public function copyTo(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'page' => 'required',
            'to_page' => 'different:page',
        ]);

        if (! $validator->passes()) {
            return response()->json(['error' => $validator->errors()->all()]);
        }

        $page = StoreWebsitePage::where('id', $request->page)->first();

        if ($page) {
            if (! empty($request->to_page) || ! empty($request->site_urls)) {
                $updateData = [];
                $updateData['is_pushed'] = 0;

                if ($request->cttitle == 'true') {
                    $updateData['meta_title'] = $page->meta_title;
                }
                if ($request->ctkeyword == 'true') {
                    $updateData['meta_keywords'] = $page->meta_keywords;
                }
                if ($request->ctdesc == 'true') {
                    $updateData['meta_description'] = $page->meta_description;
                }
                if ($updateData) {
                    if ($request->to_page) {
                        StoreWebsitePage::where('id', $request->to_page)->update($updateData);
                    }
                    if ($request->site_urls == 'true') {
                        StoreWebsitePage::where('url_key', $page->url_key)->update($updateData);
                    }

                    return response()->json(['code' => 200, 'success' => 'Success']);
                }
            }
        }

        return response()->json(['code' => 200, 'error' => 'Page not found']);
    }

    public function pageHistory(Request $request, $page): JsonResponse
    {
        $histories = StoreWebsitePageHistory::where('store_website_page_id', $page)->latest()->get();

        foreach ($histories as $h => $history) {
            // code...
            $history->user;
        }

        return response()->json(['code' => 200, 'data' => $histories]);
    }

    public function pageActivities(Request $request, $page): JsonResponse
    {
        $activities = Activity::with('causer')->where('subject_id', $page)->latest()->get();

        return response()->json(['code' => 200, 'data' => $activities]);
    }

    public function translateForOtherLanguage(Request $request, $id): JsonResponse
    {
        $page = StoreWebsitePage::find($id);

        $errorMessage = [];

        if ($page) {
            // find the language all active and then check that record page is exist or not
            if (is_null($page->translated_from)) {
                $languages = Language::where('status', 1)->get();
                foreach ($languages as $l) {
                    if (strtolower($page->language) != strtolower($l->name)) {
                        $pageExist = StoreWebsitePage::where('url_key', $page->url_key)->where('store_website_id', $page->store_website_id)->where('language', $l->name)->first();
                        if (! $pageExist) {
                            $newPage = new StoreWebsitePage;
                        } else {
                            $newPage = StoreWebsitePage::find($pageExist->id);
                        }

                        $title = GoogleTranslateController::translateProducts(
                            new CommonGoogleTranslateService,
                            $l->locale,
                            [$page->title]
                        );

                        $metaTitle = GoogleTranslateController::translateProducts(
                            new CommonGoogleTranslateService,
                            $l->locale,
                            [$page->meta_title]
                        );

                        $metaKeywords = GoogleTranslateController::translateProducts(
                            new CommonGoogleTranslateService,
                            $l->locale,
                            [$page->meta_keywords]
                        );

                        $metaDescription = GoogleTranslateController::translateProducts(
                            new CommonGoogleTranslateService,
                            $l->locale,
                            [$page->meta_description]
                        );

                        $contentHeading = GoogleTranslateController::translateProducts(
                            new CommonGoogleTranslateService,
                            $l->locale,
                            [$page->content_heading]
                        );

                        $content = $page->content;
                        if (Str::contains($page->content, ['{{widget', '{{ widget', '{{WIDGET', '{{ WIDGET']) === false) {
                            $content = GoogleTranslateController::translateProducts(
                                new CommonGoogleTranslateService,
                                $l->locale,
                                [$page->content]
                            );
                        }

                        // assign the stores  column
                        $fetchStores = WebsiteStoreView::where('website_store_views.name', $l->name)
                            ->join('website_stores as ws', 'ws.id', 'website_store_views.website_store_id')
                            ->join('websites as w', 'w.id', 'ws.website_id')
                            ->where('w.store_website_id', $page->store_website_id)
                            ->select('website_store_views.*')
                            ->get();

                        $stores = [];
                        if (! $fetchStores->isEmpty()) {
                            foreach ($fetchStores as $fetchStore) {
                                $stores[] = $fetchStore->code;
                            }
                        }

                        $newPage->title = ! empty($title) ? $title : $page->title;
                        $newPage->meta_title = ! empty($metaTitle) ? $metaTitle : $page->meta_title;
                        $newPage->meta_keywords = ! empty($metaKeywords) ? $metaKeywords : $page->meta_keywords;
                        $newPage->meta_description = ! empty($metaDescription) ? $metaDescription : $page->meta_description;
                        $newPage->content_heading = ! empty($contentHeading) ? $contentHeading : $page->content_heading;
                        $newPage->content = ! empty($content) ? $content : $page->content;
                        $newPage->layout = $page->layout;
                        $newPage->url_key = $page->url_key;
                        $newPage->active = $page->active;
                        $newPage->stores = implode(',', $stores);
                        $newPage->store_website_id = $page->store_website_id;
                        $newPage->language = $l->name;
                        $newPage->copy_page_id = $page->id;
                        $newPage->translated_from = $page->id;
                        $newPage->is_pushed = 0;
                        $newPage->is_latest_version_pushed = 0;
                        $newPage->is_latest_version_translated = 1;
                        $newPage->is_flagged_translation = 1;
                        $newPage->save();

                        activity()->causedBy(auth()->user())->performedOn($page)->log('page translated to '.$l->name);
                        activity()->causedBy(auth()->user())->performedOn($newPage)->log('Parent Page Title:'.$newPage->title.' Page URL Key:'.$newPage->url_key);
                        /*else{
                            $errorMessage[] = "Page not pushed because of page already copied to {$pageExist->url_key} for {$l->name}";
                        }*/
                    }
                }
            }

            return response()->json(['code' => 200, 'data' => [], 'message' => 'Records copied succesfully', 'errorMessage' => implode('<br>', $errorMessage)]);
        }

        return response()->json(['code' => 500, 'data' => [], 'message' => 'Page does not exist']);
    }

    public function pushWebsiteInLive(Request $request, $id): JsonResponse
    {
        $pages = StoreWebsitePage::where('store_website_id', $id)->get();
        activity()->causedBy(auth()->user())->log('pages pushed');
        if (! $pages->isEmpty()) {
            $updated_by = auth()->user();
            foreach ($pages as $page) {
                PushPageToMagento::dispatch($page, $updated_by)->onQueue('magetwo');
            }
        }

        return response()->json(['code' => 500, 'data' => [], 'message' => 'Page does not exist']);
    }

    public function pullLogs($pageId = null): JsonResponse
    {
        if ($pageId == null) {
            $logs = StoreWebsitePagePullLog::whereNull('page_id');
        } else {
            $logs = StoreWebsitePagePullLog::where('page_id', $pageId);
        }
        $logs = $logs->leftJoin('store_websites', 'store_websites.id', 'store_website_page_pull_logs.store_website_id')
            ->select('store_websites.title as website', 'store_website_page_pull_logs.*')->get();

        return response()->json(['code' => 200, 'data' => $logs]);
    }

    public function pullWebsiteInLive(Request $request, $id): JsonResponse
    {
        $website = StoreWebsite::where('id', $id)->where('api_token', '!=', '')->where('remote_software', '2')->where('website_source', 'magento')->first();
        if ($website) {
            activity()->causedBy(auth()->user())->log('pages pulled');
            $data = MagentoHelper::pullWebsitePage($website);
            if (! empty($data)) {
                foreach ($data as $key => $d) {
                    $pages = StoreWebsitePage::where('store_website_id', $website->id)->where('platform_id', $d->id)->first();
                    if (! $pages) {
                        $pages = new StoreWebsitePage;
                    }

                    $pages->store_website_id = $website->id;
                    $pages->platform_id = $d->id;
                    $pages->title = isset($d->title) ? $d->title : '';
                    $pages->url_key = isset($d->identifier) ? $d->identifier : '';
                    $pages->layout = isset($d->page_layout) ? $d->page_layout : '';
                    $pages->meta_title = isset($d->meta_title) ? $d->meta_title : '';
                    $pages->meta_keywords = isset($d->meta_keywords) ? $d->meta_keywords : '';
                    $pages->meta_description = isset($d->meta_description) ? $d->meta_description : '';
                    $pages->content_heading = isset($d->content_heading) ? $d->content_heading : '';
                    $pages->content = isset($d->content) ? $d->content : '';
                    $pages->created_at = isset($d->creation_time) ? $d->creation_time : '';
                    $pages->updated_at = isset($d->update_time) ? $d->update_time : '';

                    $pages->save();

                    StoreWebsitePagePullLog::create(['title' => isset($d->title) ? $d->title : '',
                        'meta_title' => isset($d->meta_title) ? $d->meta_title : '',
                        'meta_keywords' => isset($d->meta_keywords) ? $d->meta_keywords : '',
                        'meta_description' => isset($d->meta_description) ? $d->meta_description : '',
                        'content_heading' => isset($d->content_heading) ? $d->content_heading : '',
                        'content' => isset($d->content) ? $d->content : '',
                        'layout' => isset($d->page_layout) ? $d->page_layout : '',
                        'url_key' => isset($d->identifier) ? $d->identifier : '',
                        'platform_id' => $d->id,
                        'page_id' => $pages->id,
                        'store_website_id' => $website->id,
                        'response_type' => 'success', ]);
                }
            } else {
                StoreWebsitePagePullLog::create([
                    'store_website_id' => $website->id,
                    'response_type' => 'error', ]);
            }

            return response()->json(['code' => 200, 'data' => [], 'message' => 'Website pages pulled successfully!']);
        }

        return response()->json(['code' => 500, 'data' => [], 'message' => 'Page does not exist']);
    }

    public function histories(Request $request)
    {
        $title = 'Page History';

        $storeWebsites = StoreWebsite::pluck('title', 'id')->toArray();

        $records = StoreWebsitePage::join('store_website_page_histories as swh', 'swh.store_website_page_id', 'store_website_pages.id')
            ->join('store_websites as sw', 'sw.id', 'store_website_pages.store_website_id')
            ->join('users', 'users.id', 'swh.updated_by')
            ->select(['swh.*', 'sw.id as store_website_id', 'sw.title as store_website_name', 'store_website_pages.url_key', 'users.name as updatedBy'])
            ->orderByDesc('swh.id');

        if ($request->store_website_id != null) {
            $records = $records->where('store_website_id', $request->store_website_id);
        }

        if ($request->keyword != null) {
            $records = $records->where(function ($q) use ($request) {
                return $q->where('store_website_pages.url_key', 'like', '%'.$request->keyword.'%');
            });
        }

        $records = $records->paginate();

        return view('storewebsite::page.histories', compact('records', 'title', 'storeWebsites'));
    }

    public function store_platform_id()
    {
        $page = StoreWebsitePage::find(request()->page_id);
        $old = $page->platform_id;
        $page->platform_id = request()->platform_id;
        if ($page->save()) {
            activity()->causedBy(auth()->user())->performedOn($page)->log('page platform id updated from '.$old.' to '.request()->platform_id);

            return 'success';
        } else {
            return 'failed';
        }
    }

    public function statusCreate(request $request): RedirectResponse
    {
        try {
            $todoStatus = new StoreWebsiteStatus;
            $todoStatus->status_name = $request->status_name;
            $todoStatus->color = $request->status_color;
            $todoStatus->save();

            return redirect()->back()->with('success', 'Your Todo status has been Added!');
        } catch (Exception $e) {
            return redirect()->back()->withErrors($e->getMessage());
        }
    }

    public function StatusColorUpdate(Request $request): RedirectResponse
    {
        $statusColor = $request->all();
        $data = $request->except('_token');
        foreach ($statusColor['color_name'] as $key => $value) {
            $magentoModuleVerifiedStatus = StoreWebsiteStatus::find($key);
            $magentoModuleVerifiedStatus->color = $value;
            $magentoModuleVerifiedStatus->save();
        }

        return redirect()->back()->with('success', 'The status color updated successfully.');
    }

    public function websiteStatusUpdate(Request $request): JsonResponse
    {
        $storewebsite = StoreWebsitePage::find($request->dataId);
        $storewebsiteOldStatusId = $storewebsite->website_store_views_status_id;
        $storewebsite->website_store_views_status_id = $request->statusId;
        $storewebsite->save();

        $storewebsitHistory = new StoreWebsiteStatusHistory;
        $storewebsitHistory->old_status_id = $storewebsiteOldStatusId;
        $storewebsitHistory->new_status_id = $request->statusId;
        $storewebsitHistory->user_id = Auth::user()->id;
        $storewebsitHistory->store_website_page_id = $request->dataId;
        $storewebsitHistory->save();

        return response()->json(['code' => 200, 'data' => $storewebsite, 'message' => 'status updated successfully']);
    }

    public function statusHistoryList(Request $request): JsonResponse
    {
        $history = StoreWebsiteStatusHistory::with(['user', 'newstatus', 'oldstatus'])->where('store_website_page_id', $request->id)->latest()->get();

        return response()->json([
            'status' => true,
            'data' => $history,
            'message' => 'history listed successfully',
            'status_name' => 'success',
        ], 200);
    }

    public function getTranslatedTextScore(Request $request, $id): JsonResponse
    {
        $page = StoreWebsitePage::where('id', $id)->first();
        if ($page) {
            $originalData = StoreWebsitePage::where('id', $page->translated_from)->first();
            if ($originalData) {
                $titleScore = app('translation-lambda-helper')->getTranslateScore($originalData->title, $page->title);
                $metaTitleScore = app('translation-lambda-helper')->getTranslateScore($originalData->meta_title, $page->meta_title);
                $descScore = app('translation-lambda-helper')->getTranslateScore($originalData->meta_description, $page->meta_description);
                $keywordsScore = app('translation-lambda-helper')->getTranslateScore($originalData->meta_keywords, $page->meta_keywords);
                $contentHeadingScore = app('translation-lambda-helper')->getTranslateScore($originalData->content_heading, $page->content_heading);
                $contentScore = app('translation-lambda-helper')->getTranslateScore($originalData->content, $page->content);

                $page->title_score = ($titleScore != 0) ? $titleScore : 0;
                $page->meta_title_score = ($metaTitleScore != 0) ? $metaTitleScore : 0;
                $page->meta_description_score = ($descScore != 0) ? $descScore : 0;
                $page->meta_keywords_score = ($keywordsScore != 0) ? $keywordsScore : 0;
                $page->content_heading_score = ($contentHeadingScore != 0) ? $contentHeadingScore : 0;
                $page->content_score = ($contentScore != 0) ? $contentScore : 0;
                $page->save();

                return response()->json(['code' => 200, 'success' => 'Success', 'message' => 'Success']);
            }
        } else {
            return response()->json(['code' => 500, 'message' => 'Wrong site id!']);
        }
    }

    public function getMultiTranslatedTextScore(Request $request): JsonResponse
    {
        $pages = StoreWebsitePage::whereIn('id', $request->ids)->whereNull('title_score')->whereNotNull('translated_from')->get();
        if (! empty($pages) && count($pages) > 0) {
            foreach ($pages as $page) {
                $originalData = StoreWebsitePage::where('id', $page->translated_from)->first();
                if ($originalData) {
                    $titleScore = app('translation-lambda-helper')->getTranslateScore($originalData->title, $page->title);
                    $metaTitleScore = app('translation-lambda-helper')->getTranslateScore($originalData->meta_title, $page->meta_title);
                    $descScore = app('translation-lambda-helper')->getTranslateScore($originalData->meta_description, $page->meta_description);
                    $keywordsScore = app('translation-lambda-helper')->getTranslateScore($originalData->meta_keywords, $page->meta_keywords);
                    $contentHeadingScore = app('translation-lambda-helper')->getTranslateScore($originalData->content_heading, $page->content_heading);
                    $contentScore = app('translation-lambda-helper')->getTranslateScore($originalData->content, $page->content);

                    $page->title_score = ($titleScore != 0) ? $titleScore : 0.1;
                    $page->meta_title_score = ($metaTitleScore != 0) ? $metaTitleScore : 0.1;
                    $page->meta_description_score = ($descScore != 0) ? $descScore : 0.1;
                    $page->meta_keywords_score = ($keywordsScore != 0) ? $keywordsScore : 0.1;
                    $page->content_heading_score = ($contentHeadingScore != 0) ? $contentHeadingScore : 0.1;
                    $page->content_score = ($contentScore != 0) ? $contentScore : 0.1;
                    $page->save();
                }
            }

            return response()->json(['code' => 200, 'success' => 'Success', 'message' => 'Success']);
        } else {
            return response()->json(['code' => 500, 'message' => 'Wrong site id!']);
        }
    }
}
