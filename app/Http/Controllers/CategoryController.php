<?php

namespace App\Http\Controllers;
use App\UserUpdatedAttributeHistory;
use App\User;
use App\ScrapedProducts;
use App\LogRequest;
use App\Jobs\UpdateProductCategoryFromErp;
use App\Helpers\StatusHelper;

use App\Brand;
use App\BrandCategoryPriceRange;
use App\Category;
use App\CategoryCancellationPolicyLog;
use App\CategorySegment;
use App\Http\Requests\AddCategoryCategoryRequest;
use App\Http\Requests\EditCategoryRequest;
use App\Jobs\CreateHashTags;
use App\KeywordSearchVariants;
use App\Product;
use App\ScrappedCategoryMapping;
use App\ScrappedProductCategoryMapping;
use App\Setting;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class CategoryController extends Controller
{
    /**
     * Show the application dashboard.
     */
    public function manageCategory(Request $request): View
    {
        $category_segments = CategorySegment::where('status', 1)->get()->pluck('name', 'id');

        $allCategories = Category::all();

        $selected_value = $request->filter;

        if (isset($request->filter)) {
            $categories = Category::with([
                'childsOrderByTitle' => function ($query) {
                    $query->with([
                        'childsOrderByTitle' => function ($query) {
                            $query->with([
                                'childsOrderByTitle' => function ($query) {
                                    $query->with('childsOrderByTitle'); // Add more levels as needed
                                },
                            ]);
                        },
                    ]);
                },
            ])->where('title', 'like', '%'.$request->filter.'%')->paginate(1);
            $final_cat = [];

            foreach ($categories as $key => $cat) {
                if ($cat->parentM) {
                    if ($cat->parentM->parentM) {
                        if ($cat->parentM->parentM->parentM) {
                            $final_cat[$cat->parentM->parentM->parentM->id] = $cat->parentM->parentM->parentM;
                        } else {
                            $final_cat[$cat->parentM->parentM->id] = $cat->parentM->parentM;
                        }
                    } else {
                        $final_cat[$cat->parentM->id] = $cat->parentM;
                    }
                } else {
                    $final_cat[$cat->id] = $cat;
                }
            }
            $categories = $final_cat;
        } else {
            $categories = Category::with([
                'childsOrderByTitle' => function ($query) {
                    $query->with([
                        'childsOrderByTitle' => function ($query) {
                            $query->with([
                                'childsOrderByTitle' => function ($query) {
                                    $query->with('childsOrderByTitle'); // Add more levels as needed
                                },
                            ]);
                        },
                    ]);
                },
            ])
                ->where('parent_id', 0)
                ->orderBy('title')
                ->paginate(1);
        }

        $old = $request->old('parent_id');

        $allCategoriesDropdown = Category::attr(['name' => 'parent_id', 'class' => 'form-control'])
            ->selected()
            ->renderAsDropdown();

        $allCategoriesDropdownEdit = Category::attr(['name' => 'edit_cat', 'class' => 'form-control'])
            ->selected($old ? $old : 1)
            ->renderAsDropdown();

        return view('category.treeview', compact('category_segments', 'categories', 'allCategories', 'allCategoriesDropdown', 'allCategoriesDropdownEdit', 'selected_value'))->with('i', (request()->input('page', 1) - 1) * 20);
    }

    public function logCategory(Request $request): View
    {
        $result = LogRequest::where('api_name', 'catalogCategoryCreate');
        $logRequest = $result->orderByDesc('created_at')->paginate(Setting::get('pagination'));
        $logRequestCount = LogRequest::where('api_name', 'catalogCategoryCreate')->count();

        return view('category.log', compact('logRequest', 'logRequestCount'));
    }

    public function pushCategoryInLive()
    {
        // Run the Artisan command
        Artisan::call('store-website:push-category-in-live');

        // You can return a response if needed
        return response()->json(['message' => 'Command executed successfully']);
    }

    public function manageCategory11(Request $request): View
    {
        $category_segments = CategorySegment::where('status', 1)->get()->pluck('name', 'id');
        $categories = Category::where('parent_id', '=', 0)->withCount('childs')->get();
        $allCategories = Category::pluck('title', 'id')->all();

        $old = $request->old('parent_id');

        $allCategoriesDropdown = Category::attr(['name' => 'parent_id', 'class' => 'form-control'])
            ->selected($old ? $old : 1)
            ->renderAsDropdown();

        $allCategoriesDropdownEdit = Category::attr(['name' => 'edit_cat', 'class' => 'form-control'])
            ->selected($old ? $old : 1)
            ->renderAsDropdown();

        return view('category.treeview-11', compact('category_segments', 'categories', 'allCategories', 'allCategoriesDropdown', 'allCategoriesDropdownEdit'));
    }

    /**
     * Show the application dashboard.
     */
    public function addCategory(AddCategoryCategoryRequest $request): RedirectResponse
    {
        $input = $request->all();
        $input['parent_id'] = empty($input['parent_id']) ? 0 : $input['parent_id'];

        Category::create($input);

        $this->generateHashTagKeywords();

        return redirect()->back()->with('success', 'New Category added successfully.');
    }

    public function generateHashTagKeywords()
    {
        $brandList = Brand::getAll();
        $keywordVariants = KeywordSearchVariants::list();

        /* Initialize queue for add hashtags */
        $categoryList = Category::where('is_hashtag_generated', 0)->pluck('title', 'id')->chunk(1000)->toArray();

        foreach ($categoryList as $chunk) {
            CreateHashTags::dispatch(['data' => $chunk, 'user_id' => Auth::user()->id, 'brand_list' => $brandList, 'keyword_variants' => $keywordVariants, 'type' => 'category'])->onQueue('generategooglescraperkeywords');
        }
    }

    public function edit(Category $category, EditCategoryRequest $request)
    {
        $data = [];
        $data['id'] = $category->id;
        $data['title'] = $category->title;
        $data['magento_id'] = $category->magento_id;
        $data['show_all_id'] = $category->show_all_id;
        $data['category_segment_id'] = $category->category_segment_id;
        $data['category_segments'] = CategorySegment::where('status', 1)->get()->pluck('name', 'id');

        if ($request->method() === 'POST') {

            $category->title = $request->input('title');
            $category->magento_id = $request->input('magento_id');
            $category->show_all_id = $request->input('show_all_id');
            if ($request->has('category_segment_id')) {
                $category->category_segment_id = $request->category_segment_id;
            }
            $category->save();

            return redirect()->route('category')
                ->with('success-remove', 'Category updated successfully');
        }

        return view('category.edit', $data);
    }

    public function remove(Request $request)
    {
        $category_instance = new Category;
        $category = $category_instance->find($request->input('edit_cat'));

        if ($request->ajax()) {
            if (Category::isParent($category->id)) {
                return response()->json(['error-remove' => 'Can\'t delete Parent category. Please delete all the childs first']);
            }

            if (Category::hasProducts($category->id)) {
                return response()->json(['error-remove' => 'Can\'t delete category is associated with products. Please remove all the association first']);
            }

            if ($category->id == 1) {
                return response()->json(['error-remove' => 'Can\'t be delete']);
            }

            $title = $category->title;
            $category->delete();

            return response()->json(['success-remove' => $title.'category Deleted']);
        }

        if (Category::isParent($category->id)) {
            return redirect()->back()->with('error-remove', 'Can\'t delete Parent category. Please delete all the childs first');
        }

        if (Category::hasProducts($category->id)) {
            return redirect()->back()->with('error-remove', 'Can\'t delete category is associated with products. Please remove all the association first');
        }

        if ($category->id == 1) {
            return redirect()->back()->with('error-remove', 'Can\'t be delete');
        }

        $title = $category->title;
        $category->delete();

        return redirect()->back()->with('success-remove', $title.'Category Deleted');
    }

    public static function getCategoryTree($id)
    {
        $category = new Category;
        $category_instance = $category->find($id);
        $categoryTree = [];

        if ($category_instance == null) {
            return false;
        }

        $parent_id = $category_instance->parent_id;

        $categoryTree[] = $category_instance->title;

        while ($parent_id != 0) {
            $category_instance = $category->find($parent_id);
            $categoryTree[] = $category_instance->title;
            $parent_id = $category_instance->parent_id;
        }

        return array_reverse($categoryTree);
    }

    public static function brandMinMaxPricing(): View
    {
        // Get all data
        // $results = \Illuminate\Support\Facades\DB::select('
        //     SELECT
        //         categories.title,
        //         categories.id as cat_id,
        //         ct.title as parent_name,
        //         ct.id as parent_id,
        //         MIN(price*1) AS minimumPrice,
        //         MAX(price*1) AS maximumPrice
        //     FROM
        //         products
        //     JOIN
        //         categories
        //     ON
        //         products.category=categories.id
        //     LEFT JOIN
        //         categories as ct
        //     ON
        //         categories.parent_id=ct.id
        //     GROUP BY
        //         products.category
        //     ORDER BY
        //         categories.title
        // ');
        $results = Product::select(
            'categories.title',
            'categories.id as cat_id',
            'ct.title as parent_name',
            'ct.id as parent_id',
            DB::raw('MIN(price*1) AS minimumPrice'),
            DB::raw('MAX(price*1) AS maximumPrice')
        )
            ->join('categories', 'products.category', '=', 'categories.id')
            ->leftJoin('categories as ct', 'categories.parent_id', '=', 'ct.id')
            ->groupBy('products.category')
            ->orderBy('categories.title')
            ->get();

        // Get all form data
        $resultsBrandCategoryPriceRange = BrandCategoryPriceRange::all();

        // Create array with brand segments
        $brandSegments = ['A', 'B', 'C'];

        // Create empty array
        $formResults = [];

        // Loop over results
        foreach ($resultsBrandCategoryPriceRange as $result) {
            $formResults[$result->brand_segment][$result->category_id]['min'] = $result->min_price;
            $formResults[$result->brand_segment][$result->category_id]['max'] = $result->max_price;
        }

        return view('category.minmaxpricing', compact('results', 'brandSegments', 'formResults'));
    }

    public static function updateBrandMinMaxPricing(Request $request)
    {
        // Check minimum price first
        if ($request->ajax() && $request->type == 'min' && (int) $request->price > 0) {
            return BrandCategoryPriceRange::updateOrCreate(
                ['brand_segment' => $request->brand_segment, 'category_id' => $request->category_id],
                ['min_price' => $request->price]
            );
        }

        // Check minimum price first
        if ($request->ajax() && $request->type == 'max' && (int) $request->price > 0) {
            return BrandCategoryPriceRange::updateOrCreate(
                ['brand_segment' => $request->brand_segment, 'category_id' => $request->category_id],
                ['max_price' => $request->price]
            );
        }
    }

    public static function getCategoryTreeMagentoIds($id)
    {
        $category = new Category;
        $category_instance = $category->find($id);
        $categoryTree = [];

        $categoryTree[] = $category_instance->magento_id;
        $parent_id = $category_instance->parent_id;

        while ($parent_id != 0) {
            $category_instance = $category->find($parent_id);
            $categoryTree[] = $category_instance->magento_id;

            if (! empty($category_instance->show_all_id)) {
                $categoryTree[] = $category_instance->show_all_id;
            }

            $parent_id = $category_instance->parent_id;
        }

        return array_reverse($categoryTree);
    }

    public static function getCategoryIdByName($term)
    {
        $category = Category::where('title', '=', $term)->first();

        return $category ? $category->id : 0;
    }

    public function mapCategory(): View
    {
        $fillerCategories = Category::where('id', '>', 1)->where('parent_id', 0)->whereIn('id', [143, 144])->get();

        $categories = Category::with('getSubChilds.getSupParent', 'getSupParent')->where('id', '>', 1)->where('parent_id', 0)->whereNotIn('id', [143, 144])->get();

        $allStatus = ['' => 'N/A'] + StatusHelper::getStatus();

        $allCategoriesDropdown = Category::attr(['name' => 'new_cat_id', 'class' => 'form-control new-category-update', 'style' => 'width:100%'])->renderAsDropdown();

        return view('category.references', compact('fillerCategories', 'categories', 'allStatus', 'allCategoriesDropdown'));
    }

    public function saveReferences(Request $request)
    {
        $categories = $request->get('category');
        $info = $request->get('info');

        if (! empty($info)) {
            foreach ($info as $catId => $reference) {
                [$catId, $reference] = explode('#', $reference);
                $catId = str_replace('cat_', '', $catId);
                $category = Category::find($catId);
                $category->references = $reference;
                $category->save();
            }
        } else {
            foreach ($categories as $catId => $reference) {
                $catId = str_replace('cat_', '', $catId);
                $category = Category::find($catId);
                $category->references = implode(',', $reference);
                $category->save();
            }
        }

        return redirect()->back()->with('message', 'Category updated successfully!');
    }

    public function saveReference(Request $request): JsonResponse
    {
        $oldCatId = $request->get('old_cat_id');
        $newcatId = $request->get('new_cat_id');
        $catName = strtolower($request->get('cat_name'));

        // assigned new category
        $newCategory = null;
        $oldCategory = null;

        // checking category id
        if (! empty($oldCatId) && ! empty($newcatId)) {
            $oldCategory = Category::find($oldCatId);
            if (! empty($oldCategory)) {
                $catArray = explode(',', $oldCategory->references);
                $catArray = array_map('strtolower', $catArray);

                // check matched array we got
                $findMe = array_search($catName, $catArray);
                if ($findMe !== false) {
                    unset($catArray[$findMe]);
                }

                // update new category
                $oldCategory->references = implode(',', array_unique(array_filter($catArray)));
                $oldCategory->save();
            }
            // update with new category id
            $newCategory = Category::find($newcatId);

            if ($newCategory) {
                $newCatArr = explode(',', $newCategory->references);
                $newCatArr = array_map('strtolower', $newCatArr);
                $newCatArr[] = strtolower($catName);
                $newCategory->references = implode(',', array_unique(array_filter($newCatArr)));
                $newCategory->save();

                // once we have new category id then we need to update all product from that old category
                $products = Product::where('category', $oldCatId)->select(['products.id', 'products.sku'])->get();
                if (! $products->isEmpty()) {
                    foreach ($products as $product) {
                        $scraped_products = $product->many_scraped_products;
                        if (! $scraped_products->isEmpty()) {
                            foreach ($scraped_products as $scraped_product) {
                                if (isset($scraped_product->properties['category'])) {
                                    if (is_array($scraped_product->properties['category'])) {
                                        $namesList = array_map('strtolower', $scraped_product->properties['category']);
                                        if (in_array(strtolower($catName), $namesList)) {
                                            $product->category = $newcatId;
                                            $product->save();
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }

        return response()->json(['code' => 200, 'data' => $newCategory]);
    }

    public function updateField(Request $request): JsonResponse
    {
        $id = $request->get('id');
        $field = $request->get('_f');
        $value = $request->get('_v');

        $category = Category::where('id', $id)->first();
        if ($category) {
            $category->{$field} = $value;
            $category->save();

            return response()->json(['code' => 200]);
        }

        return response()->json(['code' => 404]);
    }

    public function saveForm(Request $request): JsonResponse
    {
        $id = $request->id;
        if ($id != null) {
            $category = Category::find($id);
            if (! empty($category)) {
                $findChild = Category::whereNull('simplyduty_code')->where('parent_id', $category->id)->get();
                if (! empty($findChild) && ! $findChild->isEmpty()) {
                    foreach ($findChild as $child) {
                        $child->simplyduty_code = $request->simplyduty_code;
                        $child->save();
                    }
                }
                $category->simplyduty_code = $request->simplyduty_code;
                $category->save();
            }
        }

        return response()->json(['code' => 200, 'message' => 'Success']);
    }

    public function usedProducts(Request $request): JsonResponse
    {
        $q = $request->q;

        if ($q) {
            // check the type and then
            $q = str_replace('/', ',', $q);
            $products = ScrapedProducts::where('categories', $q)->join('products as p', 'p.id', 'scraped_products.product_id')
                ->where('p.stock', '>', 0)->groupBy('website')
                ->select('scraped_products.*')
                ->orderByDesc('scraped_products.created_at')
                ->get();

            $view = (string) view('compositions.preview-products', compact('products'));

            return response()->json(['code' => 200, 'html' => $view]);
        }

        return response()->json(['code' => 400, 'html' => '']);
    }

    public function affectedProduct(Request $request): JsonResponse
    {
        $old = $request->old_cat_id;
        $from = $request->cat_name;
        $to = $request->new_cat_id;

        if (! empty($from)) {
            // check the type and then
            $total = ScrapedProducts::matchedCategory($from)->count();

            return response()->json(['code' => 200, 'total' => $total]);
        }
    }

    public function affectedProductNew(Request $request): JsonResponse
    {
        $old = $request->old_cat_id;
        $from = $request->cat_name;
        $to = $request->new_cat_id;
        $wholeString = $request->wholeString;

        if (! empty($from)) {
            // check the type and then
            $total = ScrapedProducts::matchedCategory($from)->count();

            $view = (string) view('category.partials.affected-products', compact('total', 'old', 'from', 'to', 'wholeString'));

            return response()->json(['code' => 200, 'html' => $view]);
        }
    }

    public function categoryCancellationPolicyLog($categoryId = '', $changeParentId = '', $dayType = '', $dayChange = '', $dayOld = '')
    {
        return CategoryCancellationPolicyLog::create(
            [
                'category_id' => $categoryId,
                'change_parent_id' => $changeParentId,
                'day_type' => $dayType,
                'day_change' => $dayChange,
                'day_old' => $dayOld,
                'status' => '1',
            ]
        );
    }

    public function updateCancelationPolicy(Request $request, $id): JsonResponse
    {
        if ($request->days_cancelation) {
            Category::where('id', $id)->update(['days_cancelation' => $request->days_cancelation]);
            $this->categoryCancellationPolicyLog($id, $id, 'days_cancelation', $request->days_cancelation, $request->day_old);
        }

        if ($request->days_refund) {
            $request->input('days_refund');
            Category::where('id', $id)->update(['days_refund' => $request->days_refund]);
            $this->categoryCancellationPolicyLog($id, $id, 'days_refund', $request->days_refund, $request->day_old);
        }

        // $sql = "select  id,
        //             title,
        //             parent_id
        //     from    (select * from categories
        //             order by parent_id, id) categories,
        //             (select @pv := '" . $id . "') initialisation
        //     where   find_in_set(parent_id, @pv) > 0
        //     and     length(@pv := concat(@pv, ',', id))";
        // $results = DB::select($sql);

        $results = Category::select('id', 'title', 'parent_id')
            ->orderBy('parent_id')
            ->orderBy('id')
            ->join(DB::raw('(select @pv := '.$id.') initialisation'), function ($join) {
                $join->whereRaw('FIND_IN_SET(parent_id, @pv) > 0')
                    ->whereRaw("@pv := CONCAT(@pv, ',', id)");
            })
            ->get();

        foreach ($results as $result) {
            if ($request->days_cancelation) {
                Category::where('id', $result->id)->update(['days_cancelation' => $request->days_cancelation]);
                $this->categoryCancellationPolicyLog($result->id, $id, 'days_cancelation', $request->days_cancelation, $request->day_old);
            }

            if ($request->days_refund) {
                $request->input('days_refund');
                Category::where('id', $result->id)->update(['days_refund' => $request->days_refund]);
                $this->categoryCancellationPolicyLog($result->id, $id, 'days_refund', $request->days_refund, $request->day_old);
            }
        }

        return response()->json(['success-remove' => 'Days Cancelation '.$request->days_cancelation.' updated successfully']);
    }

    public function getCategoryCancellationPolicyLog(Request $request): JsonResponse
    {
        $getCanPol = CategoryCancellationPolicyLog::where(
            [
                ['change_parent_id', '=', $request->category_id],
                ['day_type', '=', $request->day_type],

            ]
        )->get();
        if (! empty($getCanPol)) {
            return response()->json(['code' => 200, 'data' => $getCanPol]);
        } else {
            return response()->json(['code' => 404, 'message' => 'Can not found Log']);
        }
    }

    public function updateCategoryReference(Request $request): JsonResponse
    {
        $loggedUser = $request->user();

        if (! isset($request->wholeString)) {
            $request->merge(['wholeString' => $request->cat_name]);
        }

        $scrappedCategory = ScrappedCategoryMapping::find($request->old_cat_id);
        $selectedCategory = Category::find($request->new_cat_id);

        if ($request->with_product == 'yes') {
            UpdateProductCategoryFromErp::dispatch([
                'from' => $scrappedCategory->cat_name,
                'to' => $selectedCategory->id,
                'user_id' => $loggedUser->id,
            ])->onQueue('supplier_products');
        }

        UserUpdatedAttributeHistory::create([
            'old_value' => $scrappedCategory->id,
            'new_value' => $selectedCategory->id,
            'attribute_name' => 'category',
            'attribute_id' => $selectedCategory->id,
            'user_id' => Auth::user()->id,
        ]);

        UserUpdatedAttributeHistory::create([
            'old_value' => $scrappedCategory->id,
            'new_value' => $selectedCategory->id,
            'attribute_name' => 'scraped-category',
            'attribute_id' => $scrappedCategory->id,
            'user_id' => Auth::user()->id,
        ]);

        $scrappedCategory->update([
            'category_id' => $selectedCategory->id,
            'is_skip' => 1,
        ]);

        return response()->json(['code' => 200, 'message' => 'Your request has been pushed successfully']);
    }

    public function updateMultipleCategoryReference(Request $request): JsonResponse
    {
        $loggedUser = $request->user();

        $selectedCategory = Category::find($request->to);

        foreach ($request->from as $f) {
            $scrappedCategory = json_decode($f);

            UpdateProductCategoryFromErp::dispatch([
                'from' => $scrappedCategory->name,
                'to' => $selectedCategory->id,
                'user_id' => $loggedUser->id,
            ])->onQueue('supplier_products');

            UserUpdatedAttributeHistory::create([
                'old_value' => $scrappedCategory->id,
                'new_value' => $selectedCategory->id,
                'attribute_name' => 'category',
                'attribute_id' => $selectedCategory->id,
                'user_id' => $loggedUser->id,
            ]);

            UserUpdatedAttributeHistory::create([
                'old_value' => $scrappedCategory->id,
                'new_value' => $selectedCategory->id,
                'attribute_name' => 'scraped-category',
                'attribute_id' => $scrappedCategory->id,
                'user_id' => $loggedUser->id,
            ]);

            ScrappedCategoryMapping::where('id', $scrappedCategory->id)->update([
                'category_id' => $selectedCategory->id,
                'is_skip' => 1,
            ]);
        }

        return response()->json([
            'code' => 200,
            'message' => 'Your request has been pushed successfully',
        ]);
    }

    public function newCategoryReferenceGroup(Request $request)
    {
        $categoryAll = Category::with('childs.childLevelSencond')
            ->where('title', 'NOT LIKE', '%Unknown Category%')
            ->where('magento_id', '!=', '0')
            ->get();

        $categoryArray = [];
        foreach ($categoryAll as $category) {
            $categoryArray[] = [$category->title];
            $childs = $category->childs;
            foreach ($childs as $child) {
                $categoryArray[] = [$child->title];
                $grandChilds = $child->childLevelSencond;
                if ($grandChilds != null) {
                    foreach ($grandChilds as $grandChild) {
                        $categoryArray[] = [$grandChild->title];
                    }
                }
            }
        }

        // Use array_map to extract the first element of each sub-array and convert to lowercase
        $categories = array_map(function ($subArray) {
            return strtolower($subArray[0]);
        }, $categoryArray);

        // Use array_unique to get unique values
        $uniqueCategories = array_unique($categories);

        // Convert unique values back to an array of arrays
        $categoryAll = array_map(function ($category) {
            return [ucwords($category)]; // Convert first letter to uppercase if needed
        }, $uniqueCategories);

        return view('category.new-reference-group', ['categoryAll' => $categoryAll]);
    }

    public function newCategoryReferenceGroupBy(Request $request, $name, $threshold)
    {
        $scrapped_category_mapping = ScrappedCategoryMapping::selectRaw('scrapped_category_mappings.*')
            ->leftJoin('scrapped_product_category_mappings', 'scrapped_category_mappings.id', '=', 'scrapped_product_category_mappings.category_mapping_id')
            ->groupBy('scrapped_category_mappings.id')
            ->groupBy('scrapped_category_mappings.name')
            ->get()->filter(function ($scrapped_category_mapping) use ($name, $threshold) {
                similar_text(strtolower($scrapped_category_mapping->name), strtolower($name), $percentage);

                return $percentage >= $threshold * 100;
            });

        $categoryAll = Category::with('childs.childLevelSencond')
            ->where('title', 'NOT LIKE', '%Unknown Category%')
            ->where('magento_id', '!=', '0')
            ->get();

        $categoryArray = [];
        foreach ($categoryAll as $category) {
            $categoryArray[] = ['id' => $category->id, 'value' => $category->title];
            $childs = $category->childs;
            foreach ($childs as $child) {
                $categoryArray[] = ['id' => $child->id, 'value' => $category->title.' > '.$child->title];
                $grandChilds = $child->childLevelSencond;
                if ($grandChilds != null) {
                    foreach ($grandChilds as $grandChild) {
                        $categoryArray[] = ['id' => $grandChild->id, 'value' => $category->title.' > '.$child->title.' > '.$grandChild->title];
                    }
                }
            }
        }

        $unKnownCategory = Category::where('title', 'LIKE', '%Unknown Category%')->first();

        return view('category.new-reference-group-listing', ['categories' => $scrapped_category_mapping, 'categoryAll' => $categoryArray, 'unKnownCategoryId' => $unKnownCategory->id]);
    }

    public function newCategoryReferenceIndex(Request $request): View
    {
        $users = [];
        $unKnownCategory = Category::where('title', 'LIKE', '%Unknown Category%')->first();

        $scrapped_category_mapping = ScrappedCategoryMapping::selectRaw('scrapped_category_mappings.*, COUNT(scrapped_product_category_mappings.category_mapping_id) as total_products')
            ->leftJoin('scrapped_product_category_mappings', 'scrapped_category_mappings.id', '=', 'scrapped_product_category_mappings.category_mapping_id')
            ->groupBy('scrapped_category_mappings.id')
            ->groupBy('scrapped_category_mappings.name')
            ->orderByDesc('total_products')
            ->orderBy('is_skip');

        if ($request->search) {
            $scrapped_category_mapping->where('name', 'LIKE', '%'.$request->search.'%');
        }

        if (isset($request->is_skipped)) {
            $scrapped_category_mapping->where('is_skip', $request->is_skipped);
        }
        if ($request->user_id != null) {
            $matchedArray = UserUpdatedAttributeHistory::where([
                'attribute_name' => 'scraped-category',
                'user_id' => $request->user_id,
            ])->pluck('attribute_id');
            $scrapped_category_mapping = $scrapped_category_mapping->whereIn('scrapped_category_mappings.id', $matchedArray);
            $users = User::where('id', $request->user_id)->select(['id', 'name'])->first();
        }
        $scrapped_category_mapping = $scrapped_category_mapping->paginate(Setting::get('pagination'));

        $mappingCategory = $scrapped_category_mapping->toArray();

        $mappedProduct = ScrappedProductCategoryMapping::select('category_mapping_id', 'scrapped_product_category_mappings.product_id', 'scraped_products.website')
            ->leftJoin('scraped_products', 'scraped_products.id', '=', 'scrapped_product_category_mappings.product_id')
            ->whereIn('category_mapping_id', array_column($mappingCategory['data'], 'id'))
            ->get()
            ->toArray();

        $mappedData = [];

        foreach ($mappedProduct as $productM) {
            $mappedData[$productM->category_mapping_id][] = $productM->website;
        }

        foreach ($scrapped_category_mapping as $index => $category) {
            $scrapped_category_mapping[$index]->total_products = isset($mappedData[$category->id]) ? count($mappedData[$category->id]) : 0;
            $scrapped_category_mapping[$index]->all_websites = isset($mappedData[$category->id]) ? implode('<br>', array_unique($mappedData[$category->id])) : '-';
        }

        $categoryAll = Category::with('childs.childLevelSencond')
            ->where('title', 'NOT LIKE', '%Unknown Category%')
            ->where('magento_id', '!=', '0')
            ->get();

        $categoryArray = [];
        foreach ($categoryAll as $category) {
            $categoryArray[] = ['id' => $category->id, 'value' => $category->title];
            $childs = $category->childs;
            foreach ($childs as $child) {
                $categoryArray[] = ['id' => $child->id, 'value' => $category->title.' > '.$child->title];
                $grandChilds = $child->childLevelSencond;
                if ($grandChilds != null) {
                    foreach ($grandChilds as $grandChild) {
                        $categoryArray[] = ['id' => $grandChild->id, 'value' => $category->title.' > '.$child->title.' > '.$grandChild->title];
                    }
                }
            }
        }

        return view('category.new-reference', ['categoryAll' => $categoryArray, 'need_to_skip_status' => true, 'unKnownCategoryId' => $unKnownCategory->id, 'scrapped_category_mapping' => $scrapped_category_mapping, 'users' => $users]);
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     *
     * @param  mixed  $items
     * @param  mixed  $perPage
     * @param  null|mixed  $page
     * @param  mixed  $options
     */
    public function paginate($items, $perPage = 20, $page = null, $options = [])
    {
        $page = $page ?: (Paginator::resolveCurrentPage() ?: 1);

        $items = $items instanceof Collection ? $items : Collection::make($items);

        $items = $items->sortByDesc('cat_product_count');

        return new LengthAwarePaginator($items->forPage($page, $perPage), $items->count(), $perPage, $page, $options);
    }

    public function history(Request $request, $id): View
    {
        $type = $request->get('type', 'category');
        $records = UserUpdatedAttributeHistory::where('attribute_id', $id)->where('attribute_name', $type)->latest()->get();

        return view('compositions.partials.show-update-history', compact('records'));
    }

    public function historyForScraper(Request $request, $id): View
    {
        $type = $request->get('type', 'category');
        $records = UserUpdatedAttributeHistory::where('attribute_id', $id)->where('attribute_name', $type)->latest()->get();
        dd($records);

        return view('compositions.partials.show-update-history-scrapeed', compact('records'));
    }

    public function ScraperUserHistory(Request $request): View
    {
        $type = 'scraped-category';
        $records = UserUpdatedAttributeHistory::where('attribute_id', $request->id)->where('attribute_name', $request->type)->latest()->get();

        return view('compositions.partials.show-update-history-scrapeed', compact('records'));
    }

    public function deleteUnused(): RedirectResponse
    {
        Artisan::call('delete-categories:with-no-products');

        return redirect()->back()->with('success', 'Your request has been finished successfully!');
    }

    public function fixAutoSuggested(Request $request): JsonResponse
    {
        $scrapped_category_mapping = ScrappedCategoryMapping::select('id', 'name', 'category_id');

        if (! empty($request->show_auto_fix)) {
            $scrapped_category_mapping->whereNotNull('category_id')->where('is_auto_skip', 0);
        } else {
            $scrapped_category_mapping->whereNull('category_id');
        }
        $scrapped_category_mapping = $scrapped_category_mapping->paginate(Setting::get('pagination'));

        $links = [];

        if (! $scrapped_category_mapping->isEmpty()) {
            foreach ($scrapped_category_mapping as $k => $category) {
                $filter = Category::updateCategoryAuto($category->name);

                if (isset($request->show_auto_fix) && $request->show_auto_fix) {
                    $links[] = [
                        'from_id' => $category->id,
                        'from' => $category->name,
                        'to' => $category->category_id,
                    ];
                } else {
                    $links[] = [
                        'from_id' => $category->id,
                        'from' => $category->name,
                        'to' => ($filter) ? $filter->id : null,
                    ];
                }
            }
        }
        $is_auto_fix = ! empty($request->show_auto_fix) ? true : false;
        $view = (string) view('category.partials.preview-categories', compact('links', 'is_auto_fix'));

        return response()->json(['code' => 200, 'html' => $view]);
    }

    public function fixAutoSuggestedString(Request $request): JsonResponse
    {
        $loeggedUser = $request->user();

        $scrapped_category_mapping = ScrappedCategoryMapping::select('id', 'name')
            ->whereNull('category_id');

        if ($request->show_skipeed_btn_value == 'false') {
            $scrapped_category_mapping->whereNull('category_id');
        }

        $scrapped_category_mapping = $scrapped_category_mapping->get();
        $links = [];

        if (! $scrapped_category_mapping->isEmpty()) {
            foreach ($scrapped_category_mapping as $k => $category) {
                $filter = Category::updateCategoryAuto($category->name);
                if ($filter) {
                    $links[$category->id] = ($filter) ? $filter->id : null;
                }
            }
        }

        $count = 0;
        if (! empty($links)) {
            foreach ($links as $scrappedCategoryId => $selectedCategoryId) {
                if ($selectedCategoryId != 1) {
                    $scrappedCategory = ScrappedCategoryMapping::find($scrappedCategoryId);
                    $selectedCategory = Category::find($selectedCategoryId);

                    UpdateProductCategoryFromErp::dispatch([
                        'from' => $scrappedCategory->name,
                        'to' => $selectedCategory->id,
                        'user_id' => $loeggedUser->id,
                    ])->onQueue('supplier_products');

                    UserUpdatedAttributeHistory::create([
                        'old_value' => $scrappedCategory->id,
                        'new_value' => $selectedCategory->id,
                        'attribute_name' => 'category',
                        'attribute_id' => $selectedCategory->id,
                        'user_id' => $loeggedUser->id,
                    ]);

                    UserUpdatedAttributeHistory::create([
                        'old_value' => $scrappedCategory->id,
                        'new_value' => $selectedCategory->id,
                        'attribute_name' => 'scraped-category',
                        'attribute_id' => $scrappedCategory->id,
                        'user_id' => $loeggedUser->id,
                    ]);

                    $isUpdtaed = $scrappedCategory->update([
                        'category_id' => $selectedCategory->id,
                        'is_skip' => 1,
                        'is_auto_fix' => 1,
                    ]);
                    if ($isUpdtaed) {
                        $count++;
                    }
                } else {
                    ScrappedCategoryMapping::where('id', $scrappedCategoryId)->update(['is_skip' => 1]);
                }
            }
        }

        return response()->json(['code' => 200, 'count' => $count]);
    }

    public function saveCategoryReference(Request $request): JsonResponse
    {
        $loeggedUser = $request->user();

        $unKnownCategory = Category::where('title', 'LIKE', '%Unknown Category%')->first();

        $items = $request->updated_category;
        if (! empty($items)) {
            foreach ($items as $scrappedCategoryId => $selectedCategoryId) {
                if ($selectedCategoryId != 1) {
                    $scrappedCategory = ScrappedCategoryMapping::find($scrappedCategoryId);
                    $selectedCategory = Category::find($selectedCategoryId);

                    UpdateProductCategoryFromErp::dispatch([
                        'from' => $scrappedCategory->name,
                        'to' => $selectedCategory->id,
                        'user_id' => $loeggedUser->id,
                    ])->onQueue('supplier_products');

                    UserUpdatedAttributeHistory::create([
                        'old_value' => $scrappedCategory->id,
                        'new_value' => $selectedCategory->id,
                        'attribute_name' => 'category',
                        'attribute_id' => $selectedCategory->id,
                        'user_id' => $loeggedUser->id,
                    ]);

                    UserUpdatedAttributeHistory::create([
                        'old_value' => $scrappedCategory->id,
                        'new_value' => $selectedCategory->id,
                        'attribute_name' => 'scraped-category',
                        'attribute_id' => $scrappedCategory->id,
                        'user_id' => $loeggedUser->id,
                    ]);

                    $scrappedCategory->update([
                        'category_id' => $selectedCategory->id,
                        'is_skip' => 0,
                        'is_auto_skip' => ! empty($request->is_auto_fix) ? 1 : 0,
                    ]);
                } else {
                    ScrappedCategoryMapping::where('id', $scrappedCategoryId)->update(['is_skip' => 1]);
                }
            }
        }

        return response()->json(['code' => 200, 'message' => 'Category updated successfully']);
    }

    public function childCategory(Request $request)
    {
        $cat = Category::with('childs')->find($request->subCat);
        $childs = $cat->childs;

        if ($childs) {
            return response()->json($childs);
        } else {
            return false;
        }
    }

    public function childEditCategory(Request $request)
    {
        $cat = Category::with(['childs.childLevelSencond', 'categorySegmentId'])->find($request->dataId);

        if ($cat) {
            return response()->json($cat);
        } else {
            return false;
        }
    }

    public function updateCategory(Request $request, $id): JsonResponse
    {
        $category = Category::findOrFail($id);

        if ($request->has('title')) {
            $category->title = $request->input('title');
            $category->save();

            return response()->json(['success-remove' => $category->title.' updated successfully']);
        }

        if ($request->has('magento_id')) {
            $category->magento_id = $request->input('magento_id');
            $category->save();

            return response()->json(['success-remove' => 'Magneto id of '.$category->title.' updated successfully']);
        }

        if ($request->has('show_all_id')) {
            $category->show_all_id = $request->input('show_all_id');
            $category->save();

            return response()->json(['success-remove' => 'Show all id of '.$category->title.' updated successfully']);
        }
        if ($request->has('parent_id')) {
            $category->parent_id = $request->parent_id;
            $category->save();

            return response()->json(['success-remove' => 'Parent category '.$category->title.' updated successfully.Please refresh page']);
        }

        if ($request->has('category_segment_id')) {
            $category->category_segment_id = $request->category_segment_id;
            $category->save();

            return response()->json(['success-remove' => 'Category segment id of '.$category->title.' updated successfully']);
        }

        if ($request->has('simplyduty_code')) {
            $findChild = Category::whereNull('simplyduty_code')->where('parent_id', $category->id)->get();
            if (! empty($findChild) && ! $findChild->isEmpty()) {
                foreach ($findChild as $child) {
                    $child->simplyduty_code = $request->simplyduty_code;
                    $child->save();
                }
            }
            $category->simplyduty_code = $request->simplyduty_code;
            $category->save();

            return response()->json(['success-remove' => 'SH code of '.$category->title.' updated successfully']);
        }

        if (isset($request->measurment)) {
            if ($request->has('need_to_check_measurement')) {
                $category->need_to_check_measurement = 1;
            } else {
                $category->need_to_check_measurement = 0;
            }
            $category->save();

            return response()->json(['success-remove' => 'Check measurement of '.$category->title.' updated successfully']);
        }

        if (isset($request->checkSize)) {
            if ($request->has('need_to_check_size')) {
                $category->need_to_check_size = 1;
            } else {
                $category->need_to_check_size = 0;
            }
            $category->save();

            return response()->json(['success-remove' => 'Check size of '.$category->title.' updated successfully']);
        }

        if (isset($request->checkSizeChart)) {
            if ($request->has('size_chart_needed')) {
                $category->size_chart_needed = 1;
            } else {
                $category->size_chart_needed = 0;
            }
            $category->save();

            return response()->json(['success-remove' => 'Check size chart of '.$category->title.' updated successfully']);
        }
    }

    public function updateMinMaxPriceDefault()
    {
        
        if (auth()->user()->isAdmin()) {

            // $results = \Illuminate\Support\Facades\DB::select('
            //     SELECT
            //         categories.title,
            //         categories.id as cat_id,
            //         ct.title as parent_name,
            //         ct.id as parent_id,
            //         MIN(price*1) AS minimumPrice,
            //         MAX(price*1) AS maximumPrice
            //     FROM
            //         products
            //     JOIN
            //         categories
            //     ON
            //         products.category=categories.id
            //     LEFT JOIN
            //         categories as ct
            //     ON
            //         categories.parent_id=ct.id
            //     GROUP BY
            //         products.category
            //     ORDER BY
            //         categories.title
            // ');

            $results = Product::select(
                'categories.title',
                'categories.id as cat_id',
                'ct.title as parent_name',
                'ct.id as parent_id',
                DB::raw('MIN(products.price * 1) AS minimumPrice'),
                DB::raw('MAX(products.price * 1) AS maximumPrice')
            )
                ->join('categories', 'products.category', '=', 'categories.id')
                ->leftJoin('categories as ct', 'categories.parent_id', '=', 'ct.id')
                ->groupBy('products.category')
                ->orderBy('categories.title')
                ->get();

            $brandSegments = ['A', 'B', 'C'];

            foreach ($brandSegments as $bs) {
                foreach ($results as $r) {
                    $bsRange = BrandCategoryPriceRange::where('brand_segment', $bs)->where('category_id', $r->cat_id)->first();
                    if (! $bsRange) {
                        BrandCategoryPriceRange::updateOrCreate(
                            ['brand_segment' => $bs, 'category_id' => $r->cat_id],
                            ['min_price' => 50, 'max_price' => 10000]
                        );
                    } else {
                        $bsRange->min_price = 50;
                        $bsRange->max_price = 10000;
                        $bsRange->save();
                    }
                }
            }

            echo 'script done';
        }else{
            return abort(404);
        }
    }

    public function changePushType(Request $request): JsonResponse
    {
        $category = Category::find($request->category_id);
        if ($category) {
            $category->push_type = $request->value;
            $category->save();

            return response()->json(['code' => 200, 'message' => 'Updated successfully']);
        } else {
            return response()->json(['code' => 404, 'message' => 'Category not found']);
        }
    }

    /**
     * copy data from One category To Another.
     */
    public function storeCopyCategory(Request $request): RedirectResponse
    {
        $sourceCategoryId = $request->sourceCategoryId;
        $targetCategoryId = $request->targetCategoryId;

        $categories = Category::where('parent_id', $sourceCategoryId)->orderBy('title')->get();

        $data = [];

        if ($categories) {
            foreach ($categories as $category) {
                $data['child'][$category->id] = $category->toArray();
                $insert_array = $category->toArray();
                $insert_array['parent_id'] = $targetCategoryId;
                $insert_array['magento_id'] = 0;
                unset($insert_array['id']);
                $pid = Category::insertGetId($insert_array);
                $data['child'][$category->id]['child'] = $this->getChildData($category->id, $pid);
            }
        }

        return redirect()->route('category')
            ->with('success-remove', 'Data Copyied Successfully');
    }

    /**
     * find child data from parent.
     *
     * @param  mixed  $parentId
     * @param  mixed  $pid
     * @return \Illuminate\Http\Response
     */
    public function getChildData($parentId, $pid)
    {
        $categories = Category::where('parent_id', $parentId)->orderBy('title')->get();
        $data = [];
        if ($categories) {
            foreach ($categories as $category) {
                $data[$category->id] = $category->toArray();
                $insert_array = $category->toArray();
                $insert_array['parent_id'] = $pid;
                $insert_array['magento_id'] = 0;
                unset($insert_array['id']);
                $newpid = Category::insertGetId($insert_array);
                $data[$category->id]['child'] = $this->getChildData($category->id, $newpid);
            }
        }

        return $data;
    }
}
