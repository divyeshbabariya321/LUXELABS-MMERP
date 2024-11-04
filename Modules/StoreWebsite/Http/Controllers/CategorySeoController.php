<?php

namespace Modules\StoreWebsite\Http\Controllers;

use App\Category;
use App\Http\Controllers\GoogleTranslateController;
use App\Jobs\PushCategorySeoToMagento;
use App\Language;
use App\Services\CommonGoogleTranslateService;
use App\StoreWebsite;
use App\StoreWebsiteCategory;
use App\StoreWebsiteCategorySeo;
use App\StoreWebsiteCategorySeosHistories;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\View\View;

class CategorySeoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        $title = 'Category SEO | Store Website';
        $languages = Language::pluck('name', 'id')->toArray();
        $storeWebsites = StoreWebsite::all()->pluck('website', 'id');
        $categories = Category::all();
        $categories_list = Category::pluck('title', 'id')->toArray();
        $store_list = StoreWebsite::pluck('title', 'id')->toArray();
        $categroy_seos_list = StoreWebsiteCategorySeo::select('meta_title', 'id')->orderByDesc('id')->get();

        return view('storewebsite::category-seo.index', [
            'title' => $title,
            'storeWebsites' => $storeWebsites,
            'categories' => $categories,
            'categories_list' => $categories_list,
            'languages' => $languages,
            'store_list' => $store_list,
            'categroy_seos_list' => $categroy_seos_list,
        ]);
    }

    public function records(Request $request): JsonResponse
    {
        $storewebsite_category_seos = StoreWebsiteCategorySeo::join('categories as cat', 'cat.id', 'store_website_category_seos.category_id')
            ->leftjoin('categories as sub_cat', 'sub_cat.id', 'cat.parent_id')
            ->leftjoin('categories as main_cat', 'main_cat.id', 'sub_cat.parent_id')
            ->leftjoin('store_websites as store', 'store.id', 'store_website_category_seos.store_website_id')
            ->join('languages', 'languages.id', 'store_website_category_seos.language_id');

        if ($request->has('category_id') && ! empty($request->category_id)) {
            $storewebsite_category_seos = $storewebsite_category_seos->where(function ($q) use ($request) {
                $q->where('cat.id', $request->category_id);
            });
        }

        if ($request->has('store_website_id') && ! empty($request->store_website_id)) {
            $storewebsite_category_seos = $storewebsite_category_seos->where(function ($q) use ($request) {
                $q->where('store_website_category_seos.store_website_id', $request->store_website_id);
            });
        }

        // Check for keyword search
        if ($request->has('keyword')) {
            $storewebsite_category_seos = $storewebsite_category_seos->where(function ($q) use ($request) {
                $q->where('cat.title', 'like', '%'.$request->keyword.'%')->orWhere('store_website_category_seos.meta_title', 'like', '%'.$request->keyword.'%');
            });
        }

        $storewebsite_category_seos = $storewebsite_category_seos->orderByDesc('store_website_category_seos.id')->select(['languages.name', 'cat.title', 'sub_cat.title as sub_category', 'main_cat.title as main_category', 'store.title as store_name', 'store_website_category_seos.*'])->paginate();

        $items = $storewebsite_category_seos->items();

        $recItems = [];
        foreach ($items as $item) {
            $attributes = $item->getAttributes();
            $attributes['store_small'] = strlen($attributes['name']) > 15 ? substr($attributes['name'], 0, 15) : $attributes['name'];
            $attributes['category'] = $attributes['title'];
            if (! empty($attributes['sub_category'])) {
                $attributes['category'] = $attributes['sub_category'].' > '.$attributes['category'];
            }
            if (! empty($attributes['main_category'])) {
                $attributes['category'] = $attributes['main_category'].' > '.$attributes['category'];
            }
            $recItems[] = $attributes;
        }

        return response()->json(['code' => 200, 'data' => $recItems, 'total' => $storewebsite_category_seos->total(),
            'pagination' => (string) $storewebsite_category_seos->links(),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        return view('storewebsite::create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        $post = $request->all();
        $id = $request->get('id', 0);

        $params = [
            'meta_title' => 'required',
            'category_id' => 'required',
            'store_website_id' => 'required',
            'language_id' => 'required',
        ];

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

        $records = StoreWebsiteCategorySeo::find($id);

        if (! $records) {
            $records = new StoreWebsiteCategorySeo;
        }

        $records->fill($post);

        // if records has been save then call a request to push
        if ($records->save()) {
        }

        return response()->json(['code' => 200, 'data' => $records]);
    }

    /**
     * Show the specified resource.
     */
    public function show(int $id): View
    {
        return view('storewebsite::show');
    }

    public function edit(Request $request, $id): JsonResponse
    {
        $storewebsite_category_seo = StoreWebsiteCategorySeo::where('id', $id)->first();

        if ($storewebsite_category_seo) {
            if (empty($storewebsite_category_seo->meta_title)) {
                $request->category;
                if ($request->category) {
                    $category = explode('>', $request->category);
                    end($category);
                    $storewebsite_category_seo->meta_title = prev($category).end($category);
                }
            }

            return response()->json(['code' => 200, 'data' => $storewebsite_category_seo]);
        }

        return response()->json(['code' => 500, 'error' => 'Wrong category seo id!']);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, int $id): Response
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(int $id): JsonResponse
    {
        $storewebsite_category_seo = StoreWebsiteCategorySeo::where('id', $id)->first();

        if ($storewebsite_category_seo) {
            $storewebsite_category_seo->delete();

            return response()->json(['code' => 200]);
        }

        return response()->json(['code' => 500, 'error' => 'Wrong category seo id!']);
    }

    public function translateForOtherLanguage(Request $request, $id): JsonResponse
    {
        $store_website_category_seo = StoreWebsiteCategorySeo::find($id);

        if (! empty($store_website_category_seo)) {
            $languages = Language::where('status', 1)->get();
            foreach ($languages as $lang) {
                if ($lang->id != $store_website_category_seo->language_id) {
                    $categoryExist = StoreWebsiteCategorySeo::where('category_id', $store_website_category_seo->category_id)->where('language_id', $lang->id)->first();
                    if (empty($categoryExist)) {
                        $newStoreCategorySeo = new StoreWebsiteCategorySeo;

                        $meta_title = GoogleTranslateController::translateProducts(
                            new CommonGoogleTranslateService,
                            $lang->locale,
                            [$store_website_category_seo->meta_title]
                        );
                        $meta_description = GoogleTranslateController::translateProducts(
                            new CommonGoogleTranslateService,
                            $lang->locale,
                            [$store_website_category_seo->meta_description]
                        );
                        $meta_keyword = GoogleTranslateController::translateProducts(
                            new CommonGoogleTranslateService,
                            $lang->locale,
                            [$store_website_category_seo->meta_keyword]
                        );

                        $newStoreCategorySeo->category_id = $store_website_category_seo->category_id;
                        $newStoreCategorySeo->meta_title = $meta_title;
                        $newStoreCategorySeo->meta_description = $meta_description;
                        $newStoreCategorySeo->meta_keyword = $meta_keyword;
                        $newStoreCategorySeo->language_id = $lang->id;
                        $newStoreCategorySeo->parent_id = $id;
                        $newStoreCategorySeo->save();
                    }
                }
            }

            return response()->json(['code' => 200, 'data' => [], 'message' => 'Records copied succesfully']);
        }

        return response()->json(['code' => 500, 'data' => [], 'message' => 'Category does not exist']);
    }

    public function push($id): JsonResponse
    {
        $SeoCategory = StoreWebsiteCategorySeo::where('id', $id)->first();
        $stores = StoreWebsiteCategory::where('category_id', $SeoCategory->category_id)->pluck('store_website_id')->toArray();
        if ($SeoCategory) {
            PushCategorySeoToMagento::dispatch([$SeoCategory->category_id], array_unique($stores));

            return response()->json(['code' => 200, 'message' => 'category send for push']);
        }

        return response()->json(['code' => 500, 'message' => 'Wrong site id!']);
    }

    public function pushWebsiteInLive($id): JsonResponse
    {
        $categories = StoreWebsiteCategory::where('store_website_id', $id)->pluck('category_id')->toArray();
        // print_r($categories);
        // exit();
        if ($categories) {
            PushCategorySeoToMagento::dispatch($categories, [$id]);

            return response()->json(['code' => 200, 'message' => 'category send for push']);
        }

        return response()->json(['code' => 500, 'message' => 'Wrong site id!']);
    }

    public function history($id): JsonResponse
    {
        $histories = StoreWebsiteCategorySeosHistories::leftJoin('users as u', 'u.id', 'store_website_category_seos_histories.user_id')
            ->where('store_website_cate_seos_id', $id)
            ->orderByDesc('store_website_category_seos_histories.created_at')
            ->select(['store_website_category_seos_histories.*', 'u.name as user_name'])
            ->get();

        return response()->json(['code' => 200, 'data' => $histories]);
    }

    public function loadPage(Request $request, $id): JsonResponse
    {
        $page = StoreWebsiteCategorySeo::find($id);

        if ($page) {
            return response()->json(['code' => 200, 'content' => $page->content, 'meta_title' => $page->meta_title, 'meta_keyword' => $page->meta_keyword, 'meta_desc' => $page->meta_description]);
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

        $page = StoreWebsiteCategorySeo::where('id', $request->page)->first();

        if ($page) {
            if (! empty($request->to_page) || ! empty($request->entire_category)) {
                $updateData = [];

                if ($request->cttitle == 'true') {
                    $updateData['meta_title'] = $page->meta_title;
                }
                if ($request->ctkeyword == 'true') {
                    $updateData['meta_keyword'] = $page->meta_keyword;
                }
                if ($request->ctdesc == 'true') {
                    $updateData['meta_description'] = $page->meta_description;
                }
                dump($updateData);
                if ($updateData) {
                    if ($request->to_page) {
                        StoreWebsiteCategorySeo::where('id', $request->to_page)->update($updateData);
                    }
                    if ($request->entire_category == 'true') {
                        StoreWebsiteCategorySeo::where('category_id', $page->category_id)->update($updateData);
                    }

                    return response()->json(['code' => 200, 'success' => 'Success']);
                }
            }
        }

        return response()->json(['code' => 200, 'error' => 'Page not found']);
    }

    public function getTranslatedTextScore(Request $request, $id): JsonResponse
    {
        $page = StoreWebsiteCategorySeo::where('id', $id)->first();
        if ($page) {
            $originalData = StoreWebsiteCategorySeo::where('id', $page->parent_id)->first();
            if ($originalData) {
                $titleScore = app('translation-lambda-helper')->getTranslateScore($originalData->meta_title, $page->meta_title);
                $descScore = app('translation-lambda-helper')->getTranslateScore($originalData->meta_description, $page->meta_description);
                $keywordScore = app('translation-lambda-helper')->getTranslateScore($originalData->meta_keyword, $page->meta_keyword);

                $page->meta_title_score = ($titleScore != 0) ? $titleScore : 0.1;
                $page->meta_description_score = ($descScore != 0) ? $descScore : 0.1;
                $page->meta_keyword_score = ($keywordScore != 0) ? $keywordScore : 0.1;
                $page->save();

                return response()->json(['code' => 200, 'success' => 'Success', 'message' => 'Success']);
            }
        } else {
            return response()->json(['code' => 500, 'message' => 'Wrong site id!']);
        }
    }

    public function getMultiTranslatedTextScore(Request $request): JsonResponse
    {
        $categories = StoreWebsiteCategorySeo::whereIn('id', $request->ids)->whereNull('meta_title_score')->whereNotNull('parent_id')->get();
        if (! empty($categories) && count($categories) > 0) {
            foreach ($categories as $cate) {
                $originalData = StoreWebsiteCategorySeo::where('id', $cate->parent_id)->first();
                if ($originalData) {
                    $titleScore = app('translation-lambda-helper')->getTranslateScore($originalData->meta_title, $cate->meta_title);
                    $descScore = app('translation-lambda-helper')->getTranslateScore($originalData->meta_description, $cate->meta_description);
                    $keywordScore = app('translation-lambda-helper')->getTranslateScore($originalData->meta_keyword, $cate->meta_keyword);

                    $cate->meta_title_score = ($titleScore != 0) ? $titleScore : 0.1;
                    $cate->meta_description_score = ($descScore != 0) ? $descScore : 0.1;
                    $cate->meta_keyword_score = ($keywordScore != 0) ? $keywordScore : 0.1;
                    $cate->save();
                }
            }

            return response()->json(['code' => 200, 'success' => 'Success', 'message' => 'Success']);
        } else {
            return response()->json(['code' => 500, 'message' => 'Wrong site id!']);
        }
    }
}
