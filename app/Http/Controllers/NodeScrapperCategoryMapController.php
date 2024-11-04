<?php

namespace App\Http\Controllers;

use App\Category;
use App\Http\Requests\GetRecordNodeScrapperCategoryMapRequest;
use App\Http\Requests\StoreNodeScrapperCategoryMapRequest;
use App\Http\Requests\UpdateMultipleNodeScrapperCategoryMapsRequest;
use App\Http\Requests\UpdateNodeScrapperCategoryMapRequest;
use App\Models\NodeScrapperCategoryMap;
use App\Setting;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Validator;

class NodeScrapperCategoryMapController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $valuesToCheck = json_decode(request('filter_categories'), true);

        $suppliers = Cache::remember('suppliers_list', 60, function () {
            return NodeScrapperCategoryMap::select('supplier')->distinct()->pluck('supplier');
        });

        $pagination = Setting::get('pagination');
        if (isset($request->paging) && ! empty($request->paging)) {
            $pagination = $request->paging;
        }

        $unmapped_categories_query = NodeScrapperCategoryMap::when(request()->filled('supplier'), function ($query) {
            return $query->where('supplier', request('supplier'));
        })->when(request()->filled('filter_categories'), function ($query) use ($valuesToCheck) {
            return $query->whereJsonContains('mapped_categories', $valuesToCheck);
        })->orderByDesc('id');

        $unmapped_categories = $unmapped_categories_query->paginate($pagination);
        $unmapped_categories_count = $unmapped_categories->total();

        // Caching the category tree for performance
        $category_array = Cache::remember('categories_tree', 60, function () {
            $categories = Category::with('childsOrderByTitle.childsOrderByTitle.childsOrderByTitle')
                ->where('parent_id', 0)
                ->orderBy('title')
                ->get();

            return $this->formatCategories($categories);  // Refactor category array building to a helper method
        });

        $title = 'Map Category';
        $categories_list = $this->buildTree(Category::all());

        if (request()->ajax()) {
            return view('node-category-map.partials.table-data', compact('unmapped_categories'));
        }

        return view('node-category-map.index', compact('unmapped_categories', 'category_array', 'title', 'suppliers', 'unmapped_categories_count', 'categories_list'));
    }

    private function formatCategories($categories)
    {
        $category_array = [];
        foreach ($categories as $key => $cat) {
            $category_array[$key] = [
                'id' => $cat->id,
                'name' => $cat->title,
            ];

            foreach ($cat->childsOrderByTitle as $key1 => $firstChild) {
                $category_array[$key]['child'][$key1] = [
                    'id' => $firstChild->id,
                    'name' => $firstChild->title,
                ];

                foreach ($firstChild->childsOrderByTitle as $key2 => $secondChild) {
                    $category_array[$key]['child'][$key1]['child'][$key2] = [
                        'id' => $secondChild->id,
                        'name' => $secondChild->title,
                    ];

                    foreach ($secondChild->childsOrderByTitle as $key3 => $thirdChild) {
                        $category_array[$key]['child'][$key1]['child'][$key2]['child'][$key3] = [
                            'id' => $thirdChild->id,
                            'name' => $thirdChild->title,
                        ];
                    }
                }
            }
        }

        $categories = Category::all();
        $this->buildTree($categories);

        if (request()->ajax()) {
            return view('node-category-map.partials.table-data', compact('unmapped_categories'));
        }

        return $category_array;
    }

    private function buildTree($elements, $parentId = 0)
    {
        $branch = [];
        foreach ($elements as $element) {
            if ($element->parent_id == $parentId) {
                $children = $this->buildTree($elements, $element->id);
                if ($children) {
                    $element->children = $children;
                }
                $branch[] = $element;
            }
        }

        return $branch;
    }

    public function store(StoreNodeScrapperCategoryMapRequest $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'category_stack' => 'required',
            'product_urls' => 'required',
            'supplier' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'code' => 400,
                'errors' => $validator->errors(),
            ], 400);
        }
        try {
            $nodeScrapper = new NodeScrapperCategoryMap;
            $nodeScrapper->category_stack = $request->category_stack;
            $nodeScrapper->product_urls = $request->product_urls;
            $nodeScrapper->supplier = $request->supplier;
            $nodeScrapper->save();

            return response()->json(['message' => 'Category stored successfully'], 200);
        } catch (Exception $e) {
            return response()->json(['message' => 'An error occurred.'.$e->getMessage()], 500);
        }
    }

    public function update(UpdateNodeScrapperCategoryMapRequest $request, NodeScrapperCategoryMap $scrapperCategoryMap)
    {
        if ($request->action == 'assign_category') {
            $scrapperCategoryMap->mapped_categories = json_decode($request->mapped_categories) ? json_decode($request->mapped_categories) : null;
            $scrapperCategoryMap->mapped_at = now();
            $scrapperCategoryMap->save();
        }
    }

    public function updateMultiple(UpdateMultipleNodeScrapperCategoryMapsRequest $request)
    {
        if ($request->action == 'assign_category') {
            $checked = explode(',', $request->checked);
            $mapped_categories = json_decode($request->mapped_categories) ? json_decode($request->mapped_categories) : null;
            NodeScrapperCategoryMap::whereIn('id', $checked)->update(['mapped_categories' => $mapped_categories, 'mapped_at' => now()]);
        }
    }

    public function list(Request $request): JsonResponse
    {
        $unmapped_categories = NodeScrapperCategoryMap::all()->whereNotNull('mapped_categories')->sortByDesc('id');
        $retun_array = [];
        foreach ($unmapped_categories as $unmapped_category) {
            $disp_cat = $unmapped_category->categories();
            if ($disp_cat) {
                $retun_array[] = [
                    'mapped_category' => $disp_cat,
                    'category_stack' => $unmapped_category->category_stack,
                    'product_urls' => $unmapped_category->product_urls,
                    'supplier' => $unmapped_category->supplier,

                ];
            }
        }

        return response()->json(['status' => true, 'data' => $retun_array], 200);
    }

    public function getRecord(GetRecordNodeScrapperCategoryMapRequest $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'category_stack' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'code' => 400,
                'errors' => $validator->errors(),
            ], 400);
        }
        $unmapped_category = NodeScrapperCategoryMap::select('id', 'category_stack', 'mapped_categories', 'product_urls', 'supplier')
            ->whereJsonContains('category_stack', $request->category_stack)
            ->latest('updated_at')
            ->first();

        if ($unmapped_category) {
            $return_array = [
                'mapped_category' => $unmapped_category->categories() ?? [],
                'category_stack' => $unmapped_category->category_stack,
                'product_urls' => $unmapped_category->product_urls,
                'supplier' => $unmapped_category->supplier,
            ];

            return response()->json(['status' => true, 'data' => $return_array], 200);
        }

        return response()->json(['status' => false, 'message' => 'Category Stack not found'], 404);
    }

    public function getCategoryBySearch(Request $request): JsonResponse
    {
        $search = $request->get('term');
        $categoryArray = [];
        if (! empty($search)) {
            $categories = Category::select(['id', 'title'])
                ->where('parent_id', '!=', 0)
                ->where('title', 'like', '%'.$search.'%')
                ->get();

            if (! empty($categories) && count($categories) > 0) {
                foreach ($categories as $category) {
                    $categoryData = Category::getCategoryIdsAndPathById($category);
                    // $categoryData['category_path'] = "test > test";
                    $categoryArray[] = ['id' => $category->id, 'text' => $categoryData['category_path']];
                }
            }
        } else {
            $categoryArray = Category::query()->groupBy('title')->pluck('title as text, id')->toArray();
        }

        return response()->json(['results' => $categoryArray], 200);
    }

    public function getCategoryById(Request $request)
    {
        $categoryPath = '';
        $categoryId = $request->category_id;
        $categoryIds = $categoryTitleArray = [];
        $category = Category::find($categoryId);
        if ($categoryId) {
            $categoryData = Category::getCategoryIdsAndPathById($category);
            $categoryPath = $categoryData['category_path'];
            $categoryTitleArray = $categoryData['category_title_array'];
            $categoryIds = array_values($categoryData['category_ids']);
        }

        return response()->json(['results' => $categoryPath, 'mapped_categories_val' => $categoryTitleArray, 'category_ids' => $categoryIds], 200);
    }
}
