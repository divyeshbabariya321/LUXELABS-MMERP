<?php

namespace Modules\StoreWebsite\Http\Controllers;

use App\Category;
use App\LogStoreWebsiteCategory;
use App\Models\DataTableColumn;
use App\StoreWebsite;
use App\StoreWebsiteCategory;
use App\StoreWebsiteCategoryUserHistory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\View\View;
use seo2websites\MagentoHelper\MagentoHelper;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param  mixed  $id
     */
    public function index(Request $request, $id)
    {
        $title = 'Attached Category | Store Website';

        if ($request->ajax()) {
            // send response into the json
            $categoryDropDown = Category::attr([
                'name' => 'category_id',
                'class' => 'form-control select-searchable',
            ])->renderAsDropdown();

            $storeWebsite = StoreWebsiteCategory::join('categories as c', 'c.id', 'store_website_categories.category_id')
                ->where('store_website_id', $id)
                ->select(['store_website_categories.*', 'c.title'])
                ->get();

            return response()->json([
                'code' => 200,
                'store_website_id' => $id,
                'data' => $storeWebsite,
                'scdropdown' => $categoryDropDown,
            ]);
        }

        return view('storewebsite::index', compact('title'));
    }

    /**
     * store cateogories
     */
    public function store(Request $request): JsonResponse
    {
        $storeWebsiteId = $request->get('store_website_id');
        $post = $request->all();

        $validator = Validator::make($post, [
            'store_website_id' => 'required',
            'category_id' => 'unique:store_website_categories,category_id,NULL,id,store_website_id,'.$storeWebsiteId.'|required',
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

        $storeWebsiteCategory = new StoreWebsiteCategory;
        $storeWebsiteCategory->fill($post);
        $storeWebsiteCategory->save();

        return response()->json(['code' => 200, 'data' => $storeWebsiteCategory]);
    }

    public function deleteCategory(Request $request): JsonResponse
    {
        $category = Category::where('id', $request->category_id)->first();
        $category->deleted_status = 1;
        $category->update();
        if ($category->deleted_status == 1) {
            return response()->json(['code' => 200, 'msg' => 'Category has been deleted.']);
        } else {
            return response()->json(['code' => 500, 'msg' => 'Category not deleted.']);
        }
    }

    public function delete(Request $request, $id, $store_category_id): JsonResponse
    {
        $storeCategory = StoreWebsiteCategory::where('store_website_id', $id)->where('id', $store_category_id)->first();
        if ($storeCategory) {
            $storeCategory->delete();
        }

        return response()->json(['code' => 200, 'data' => []]);
    }

    /**
     * Get child categories
     *
     * @param  mixed  $id
     * @return []
     */
    public function getChildCategories(Request $request, $id): JsonResponse
    {
        $categories = Category::where('id', $id)->first();
        $return = [];
        if ($categories) {
            $return[] = [
                'id' => $categories->id,
                'title' => $categories->title,
            ];

            $this->recursiveChildCat($categories, $return);
        }

        return response()->json(['code' => 200, 'data' => $return]);
    }

    /**
     * Recursive child category
     *
     * @param  mixed  $categories
     * @param  mixed  $return
     * @return []
     */
    public function recursiveChildCat($categories, &$return = [])
    {
        foreach ($categories->childs as $cat) {
            if ($cat->title != '') {
                $return[] = [
                    'id' => $cat->id,
                    'title' => $cat->title,
                ];
            }
            $this->recursiveChildCat($cat, $return);
        }
    }

    public function storeMultipleCategories(Request $request)
    {
        $swi = $request->get('website_id');
        $categories = $request->get('categories');

        // store website category
        $ccat = StoreWebsiteCategory::where('store_website_id', $swi)->get()
            ->pluck('name')
            ->toArray();

        // check unique records
        $unique = array_diff($categories, $ccat);
        if (! empty($unique) && is_array($unique)) {
            foreach ($unique as $cat) {
                // StoreWebsiteCategory::create([
                //     "store_website_id" => $swi,
                //     "category_id" => $cat
                // ]);

                $category = Category::find($cat);

                if ($category->parent_id == 0) {
                    $case = 'single';
                } elseif ($category->parent->parent_id == 0) {
                    $case = 'second';
                } else {
                    $case = 'third';
                }

                //Check if category
                if ($case == 'single') {
                    $data['id'] = $category->id;
                    $data['level'] = 1;
                    $data['name'] = ucwords($category->title);
                    $data['parentId'] = 0;
                    $parentId = 0;

                    if (class_exists('\\seo2websites\\MagentoHelper\\MagentoHelper')) {
                        $categ = MagentoHelper::createCategory($parentId, $data, $swi);
                    }
                    if ($category) {
                        $checkIfExist = StoreWebsiteCategory::where('store_website_id', $swi)->where('category_id', $category->id)->where('remote_id', $categ)->first();
                        if (empty($checkIfExist)) {
                            $storeWebsiteCategory = new StoreWebsiteCategory;
                            $storeWebsiteCategory->category_id = $category->id;
                            $storeWebsiteCategory->store_website_id = $swi;
                            $storeWebsiteCategory->remote_id = $categ;
                            $storeWebsiteCategory->save();
                        }
                    }
                }

                //if case second
                if ($case == 'second') {
                    $parentCategory = StoreWebsiteCategory::where('store_website_id', $swi)->where('category_id', $category->parent->id)->whereNotNull('remote_id')->first();
                    //if parent remote null then send to magento first
                    if (empty($parentCategory)) {
                        $data['id'] = $category->parent->id;
                        $data['level'] = 1;
                        $data['name'] = ucwords($category->parent->title);
                        $data['parentId'] = 0;
                        $parentId = 0;

                        if (class_exists('\\seo2websites\\MagentoHelper\\MagentoHelper')) {
                            $parentCategoryDetails = MagentoHelper::createCategory($parentId, $data, $swi);
                        }
                        if ($parentCategoryDetails) {
                            $checkIfExist = StoreWebsiteCategory::where('store_website_id', $swi)->where('category_id', $category->id)->where('remote_id', $parentCategoryDetails)->first();
                            if (empty($checkIfExist)) {
                                $storeWebsiteCategory = new StoreWebsiteCategory;
                                $storeWebsiteCategory->category_id = $category->id;
                                $storeWebsiteCategory->store_website_id = $swi;
                                $storeWebsiteCategory->remote_id = $parentCategoryDetails;
                                $storeWebsiteCategory->save();
                            }
                        }

                        $parentRemoteId = $parentCategoryDetails;
                    } else {
                        $parentRemoteId = $parentCategory->remote_id;
                    }

                    $data['id'] = $category->id;
                    $data['level'] = 2;
                    $data['name'] = ucwords($category->title);
                    $data['parentId'] = $parentRemoteId;

                    if (class_exists('\\seo2websites\\MagentoHelper\\MagentoHelper')) {
                        $categoryDetail = MagentoHelper::createCategory($parentRemoteId, $data, $swi);
                    }

                    if ($categoryDetail) {
                        $checkIfExist = StoreWebsiteCategory::where('store_website_id', $swi)->where('category_id', $category->id)->where('remote_id', $categoryDetail)->first();
                        if (empty($checkIfExist)) {
                            $storeWebsiteCategory = new StoreWebsiteCategory;
                            $storeWebsiteCategory->category_id = $category->id;
                            $storeWebsiteCategory->store_website_id = $swi;
                            $storeWebsiteCategory->remote_id = $categoryDetail;
                            $storeWebsiteCategory->save();
                        }
                    }
                }

                //if case third
                if ($case == 'third') {
                    //Find Parent
                    $parentCategory = StoreWebsiteCategory::where('store_website_id', $swi)->where('category_id', $category->id)->whereNotNull('remote_id')->first();

                    //Check if parent had remote id
                    if (empty($parentCategory)) {
                        //check for grandparent
                        $grandCategory = Category::find($category->parent->id);
                        $grandCategoryDetail = StoreWebsiteCategory::where('store_website_id', $swi)->where('category_id', $grandCategory->parent->id)->whereNotNull('remote_id')->first();

                        if (empty($grandCategoryDetail)) {
                            $data['id'] = $grandCategory->parent->id;
                            $data['level'] = 1;
                            $data['name'] = ucwords($grandCategory->parent->title);
                            $data['parentId'] = 0;
                            $parentId = 0;

                            if (class_exists('\\seo2websites\\MagentoHelper\\MagentoHelper')) {
                                $grandCategoryDetails = MagentoHelper::createCategory($parentId, $data, $swi);
                            }

                            if ($grandCategoryDetails) {
                                $checkIfExist = StoreWebsiteCategory::where('store_website_id', $swi)->where('category_id', $category->parent->id)->where('remote_id', $grandCategoryDetails)->first();
                                if (empty($checkIfExist)) {
                                    $storeWebsiteCategory = new StoreWebsiteCategory;
                                    $storeWebsiteCategory->category_id = $category->parent->id;
                                    $storeWebsiteCategory->store_website_id = $swi;
                                    $storeWebsiteCategory->remote_id = $grandCategoryDetails;
                                    $storeWebsiteCategory->save();
                                }
                            }

                            $grandRemoteId = $grandCategoryDetails;
                        } else {
                            $grandRemoteId = $grandCategoryDetail->remote_id;
                        }
                        //Search for child category

                        $data['id'] = $category->parent->id;
                        $data['level'] = 2;
                        $data['name'] = ucwords($category->parent->title);
                        $data['parentId'] = $grandRemoteId;
                        $parentId = $grandRemoteId;

                        if (class_exists('\\seo2websites\\MagentoHelper\\MagentoHelper')) {
                            $childCategoryDetails = MagentoHelper::createCategory($parentId, $data, $swi);
                        }

                        $checkIfExist = StoreWebsiteCategory::where('store_website_id', $swi)->where('category_id', $category->parent->id)->where('remote_id', $childCategoryDetails)->first();
                        if (empty($checkIfExist)) {
                            $storeWebsiteCategory = new StoreWebsiteCategory;
                            $storeWebsiteCategory->category_id = $category->parent->id;
                            $storeWebsiteCategory->store_website_id = $swi;
                            $storeWebsiteCategory->remote_id = $childCategoryDetails;
                            $storeWebsiteCategory->save();
                        }

                        $data['id'] = $category->id;
                        $data['level'] = 3;
                        $data['name'] = ucwords($category->title);
                        $data['parentId'] = $childCategoryDetails;

                        if (class_exists('\\seo2websites\\MagentoHelper\\MagentoHelper')) {
                            $categoryDetail = MagentoHelper::createCategory($childCategoryDetails, $data, $swi);
                        }

                        if ($categoryDetail) {
                            $checkIfExist = StoreWebsiteCategory::where('store_website_id', $swi)->where('category_id', $category->id)->where('remote_id', $categoryDetail)->first();
                            if (empty($checkIfExist)) {
                                $storeWebsiteCategory = new StoreWebsiteCategory;
                                $storeWebsiteCategory->category_id = $category->id;
                                $storeWebsiteCategory->store_website_id = $swi;
                                $storeWebsiteCategory->remote_id = $categoryDetail;
                                $storeWebsiteCategory->save();
                            }
                        }
                    }
                }
            }
        }

        // return response
        return response()->json(['code' => 200, 'data' => ['store_website_id' => $swi], 'message' => 'Category has been saved successfully']);
    }

    public function list(Request $request): View
    {
        ini_set('memory_limit', '-1');
        ini_set('max_execution_time', 1500);
        $title = 'Store Category';

        $allCategories = Category::query()->get();
        $allStoreWebsite = StoreWebsite::query()->get();

        $categories = Category::query();

        if ($request->keyword != null) {
            $categories = $categories->where('title', 'like', '%'.$request->keyword.'%');
        }
        if ($request->category_id != null) {
            $categories = $categories->whereIn('id', $request->category_id);
        }
        $categories = $categories->select('id', 'title')->orderBy('id');
        $categories = $categories->paginate(25);

        $selectedCategories = [];

        foreach ($categories as $category) {
            $selectedCategories[] = $category->id;
        }

        $storeWebsite = StoreWebsite::query();
        if ($request->website_id != null) {
            $storeWebsite = $storeWebsite->whereIn('id', $request->website_id)
                                        // ->orWhere('parent_id', $request->website_id)
                                        // ->orWhere('parent_id', $request->website_id)
                ->get();
        } else {
            $allStoreWebsite_data = StoreWebsite::query()->first();
            $storeWebsite = $storeWebsite->select('id', 'title')->where('id', $allStoreWebsite_data->id)->orWhere('parent_id', $allStoreWebsite_data->id)->get();
        }

        $result = StoreWebsite::leftJoin('store_website_categories as SWC', 'store_websites.id', '=', 'SWC.store_website_id')
            ->leftJoin('categories as C', 'C.id', '=', 'SWC.category_id')
            ->whereIn('C.id', $selectedCategories)
            ->select('store_websites.id as sw_id', 'store_websites.title as sw_title', 'C.id as c_id', 'C.title as c_title', 'SWC.store_website_id', 'SWC.category_id', 'SWC.remote_id')
            ->orderBy('store_websites.id')
            ->get();

        $resultSw = [];
        foreach ($result as $row) {
            $data[$row->sw_id]['sw_id'] = $row->sw_id;
            $data[$row->sw_id]['sw_title'] = $row->sw_title;
            $data[$row->sw_id]['category'][$row->c_id]['id'] = $row->c_id;
            $data[$row->sw_id]['category'][$row->c_id]['title'] = $row->c_title;
            $data[$row->sw_id]['category'][$row->c_id]['category_id'] = $row->category_id;
            $data[$row->sw_id]['category'][$row->c_id]['remote_id'] = $row->remote_id;

            $resultSw = $data;
        }

        $datatableModel = DataTableColumn::select('column_name')->where('user_id', auth()->user()->id)->where('section_name', 'store-website-category')->first();

        $dynamicColumnsToShowb = [];
        if (! empty($datatableModel->column_name)) {
            $hideColumns = $datatableModel->column_name ?? '';
            $dynamicColumnsToShowb = json_decode($hideColumns, true);
        }

        return view('storewebsite::category.index', compact(['title', 'allCategories', 'allStoreWebsite', 'categories', 'storeWebsite', 'resultSw', 'dynamicColumnsToShowb']));
    }

    public function columnVisibilityUpdateStoreWebsiteCategory(Request $request): RedirectResponse
    {
        $userCheck = DataTableColumn::where('user_id', auth()->user()->id)->where('section_name', 'store-website-category')->first();

        if ($userCheck) {
            $column = DataTableColumn::find($userCheck->id);
            $column->section_name = 'store-website-category';
            $column->column_name = json_encode($request->column_data);
            $column->save();
        } else {
            $column = new DataTableColumn;
            $column->section_name = 'store-website-category';
            $column->column_name = json_encode($request->column_data);
            $column->user_id = auth()->user()->id;
            $column->save();
        }

        return redirect()->back()->with('success', 'column visiblity Added Successfully!');
    }

    public function logadd($log_case_id, $category_id, $store_id, $log_detail, $log_msg)
    {
        $logadd = new LogStoreWebsiteCategory;
        $logadd->log_case_id = $log_case_id;
        $logadd->category_id = $category_id;
        $logadd->store_id = $store_id;
        $logadd->log_detail = $log_detail;
        $logadd->log_msg = $log_msg;
        $logadd->save();
    }

    public function categoryHistory(request $request): JsonResponse
    {
        $category_id = $request->input('category_id');
        $html = '';
        $categoryData = LogStoreWebsiteCategory::where('category_id', $category_id)
            ->orderBy('id')
            ->get();
        $i = 1;
        if (count($categoryData) > 0) {
            foreach ($categoryData as $history) {
                $html .= '<tr>';
                $html .= '<td>'.$i.'</td>';
                $html .= '<td>'.$history->log_case_id.'</td>';
                $html .= '<td>'.$history->category_id.'</td>';
                $html .= '<td>'.$history->store_id.'</td>';
                $html .= '<td>'.$history->log_detail.'</td>';
                $html .= '<td>'.$history->log_msg.'</td>';
                $html .= '<td>'.$history->created_at.'</td>';
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

    public function webiteCategoryUserHistory(request $request): JsonResponse
    {
        $store_id = $request->input('store_id');
        $category_id = $request->input('category_id');
        $html = '';
        $categoryData = StoreWebsiteCategoryUserHistory::where('category_id', $category_id)->where('store_id', $store_id)
            ->leftJoin('users', 'users.id', '=', 'store_website_category_user_history.user_id')
            ->orderBy('store_website_category_user_history.id')
            ->get();
        $i = 1;
        if (count($categoryData) > 0) {
            foreach ($categoryData as $history) {
                $html .= '<tr>';
                $html .= '<td>'.$history->created_at.'</td>';
                if ($history->website_action == 'checked') {
                    $html .= '<td>unchecked</td>';
                } else {
                    $html .= '<td>checked</td>';
                }

                $html .= '<td>'.$history->website_action.'</td>';
                $html .= '<td>'.$history->name.'</td>';
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

    public function saveStoreCategory(Request $request): JsonResponse
    {
        $storeId = $request->store;
        $catId = $request->category_id;

        if ($catId != null && $storeId != null) {
            $this->logadd('#1', $catId, $storeId, "$catId,$storeId", 'Category ID and Store id are not null.');

            $websites = StoreWebsite::where('parent_id', '=', $storeId)->orWhere('id', '=', $storeId)->get();
            $category = Category::find($catId);
            if ($category->parent_id == 0) {
                $case = 'single';
                $this->logadd('#2', $catId, $storeId, $case, 'From Category ID found parent_id 0 So case created single.');
            } elseif ($category->parent->parent_id == 0) {
                $case = 'second';
                $this->logadd('#3', $catId, $storeId, $case, "From Category ID found parent's parent_id 0 So case created second.");
            } elseif ($category->parent->parentC->parent_id == 0) {
                $case = 'third';
                $this->logadd('#4', $catId, $storeId, $case, 'From Category ID not found parent_id  So case created third.');
            } elseif ($category->parent->parentC->parentM->parent_id == 0) {
                $case = 'fourth ';
                $this->logadd('#4', $catId, $storeId, $case, 'From Category ID not found parent_id  So case created third.');
            }

            if (count($websites) > 0 && $category) {
                //coppied code
                foreach ($websites as $website) {
                    //Check if category
                    if ($case == 'single') {
                        $this->logadd('#5', $catId, $website->id, $case, 'Check single case is exit');
                        $data['id'] = $category->id;
                        $data['level'] = 1;
                        $data['name'] = ($request->category_name) ? ucwords($request->category_name) : ucwords($category->title);
                        $data['parentId'] = 0;
                        $parentId = 0;

                        if (class_exists('\\seo2websites\\MagentoHelper\\MagentoHelper')) {
                            $categ = MagentoHelper::createCategory($parentId, $data, $website->id);

                            if ($categ == false) {
                                $this->logadd('#6', $catId, $website->id, 0, 'Website not found.');
                            } else {
                                $this->logadd('#7', $catId, $website->id, $categ, "Found remote id $categ And created category catalog.");
                            }
                        }
                        if ($category) {
                            $this->logadd('#8', $catId, $website->id, $category->id, 'Check Category is an exit.');
                            $checkIfExist = StoreWebsiteCategory::where('store_website_id', $website->id)->where('category_id', $category->id)->where('remote_id', $categ)->first();
                            if (empty($checkIfExist)) {
                                $this->createStoreWebsiteCategory($category->id, $website->id, $categ);
                                $this->logadd('#9', $catId, $website->id, $category->id, 'If Category is exit then website category stored in case single');
                            }
                        }
                    }

                    //if case second
                    if ($case == 'second') {
                        $this->logadd('#10', $catId, $website->id, $case, 'Check second case is exit');
                        $parentCategory = StoreWebsiteCategory::where('store_website_id', $website->id)->where('category_id', $category->parent->id)->whereNotNull('remote_id')->first();
                        //if parent remote null then send to magento first as first level category
                        if (empty($parentCategory)) {
                            $data['id'] = $category->id;
                            $data['level'] = 1;
                            $data['name'] = ($request->category_name) ? ucwords($request->category_name) : ucwords($category->title);
                            $data['parentId'] = 0;
                            $parentId = 0;

                            if (class_exists('\\seo2websites\\MagentoHelper\\MagentoHelper')) {
                                $parentCategoryDetails = MagentoHelper::createCategory($parentId, $data, $website->id);

                                if ($parentCategoryDetails == false) {
                                    $this->logadd('#11', $catId, $website->id, 0, 'Website not found.');
                                } else {
                                    $this->logadd('#12', $catId, $website->id, $parentCategoryDetails, "Found remote id $parentCategoryDetails And created category catalog.");
                                }
                            }
                            if ($parentCategoryDetails) {
                                $checkIfExist = StoreWebsiteCategory::where('store_website_id', $website->id)->where('category_id', $category->id)->where('remote_id', $parentCategoryDetails)->first();
                                if (empty($checkIfExist)) {
                                    $this->createStoreWebsiteCategory($category->id, $website->id, $parentCategoryDetails);
                                    $this->logadd('#13', $catId, $website->id, $category->id, 'If Category is exit then category stored.');
                                }
                            }

                            $parentRemoteId = $parentCategoryDetails;
                        } else {
                            // if $parentCategory exists the remote id of the $parentCategory sets as parent id.
                            $parentRemoteId = $parentCategory->remote_id;
                        }

                        $data['id'] = $category->id;
                        $data['level'] = 2;
                        $data['name'] = ucwords($category->title);
                        $data['parentId'] = $parentRemoteId;

                        if (class_exists('\\seo2websites\\MagentoHelper\\MagentoHelper')) {
                            $categoryDetail = MagentoHelper::createCategory($parentRemoteId, $data, $website->id);

                            if ($categoryDetail == false) {
                                $this->logadd('#14', $catId, $website->id, 0, 'Website not found.');
                            } else {
                                $this->logadd('#15', $catId, $website->id, $categoryDetail, "Found remote id $categoryDetail And created category catalog.");
                            }
                        }

                        if ($categoryDetail) {
                            $checkIfExist = StoreWebsiteCategory::where('store_website_id', $website->id)->where('category_id', $category->id)->where('remote_id', $categoryDetail)->first();
                            if (empty($checkIfExist)) {
                                $this->createStoreWebsiteCategory($category->parent->id, $website->id, $categoryDetail);

                                $this->logadd('#16', $catId, $website->id, $category->id, 'If Category is exit then website category stored in case second.');
                            }
                        }
                    }

                    //if case third
                    if ($case == 'third') {
                        $this->logadd('#17', $catId, $website->id, $case, 'Check third case is exit');
                        //Find Parent
                        $parentCategory = StoreWebsiteCategory::where('store_website_id', $website->id)->where('category_id', $category->id)->whereNotNull('remote_id')->first();

                        //Check if parent had remote id
                        if (empty($parentCategory)) {
                            //check for grandparent
                            $grandCategory = Category::find($category->parent->id);
                            $grandCategoryDetail = StoreWebsiteCategory::where('store_website_id', $website->id)->where('category_id', $grandCategory->parent->id)->whereNotNull('remote_id')->first();

                            if (empty($grandCategoryDetail)) {
                                $data['id'] = $category->id;
                                $data['level'] = 1;
                                $data['name'] = ($request->category_name) ? ucwords($request->category_name) : ucwords($category->title);
                                $data['parentId'] = 0;
                                $parentId = 0;

                                if (class_exists('\\seo2websites\\MagentoHelper\\MagentoHelper')) {
                                    $grandCategoryDetails = MagentoHelper::createCategory($parentId, $data, $website->id);

                                    if ($grandCategoryDetails == false) {
                                        $this->logadd('#18', $catId, $website->id, 0, 'Website not found.');
                                    } else {
                                        $this->logadd('#19', $catId, $website->id, $grandCategoryDetails, "Found remote id $grandCategoryDetails And created category catalog.");
                                    }
                                }

                                if ($grandCategoryDetails) {
                                    $checkIfExist = StoreWebsiteCategory::where('store_website_id', $website->id)->where('category_id', $category->parent->id)->where('remote_id', $grandCategoryDetails)->first();
                                    if (empty($checkIfExist)) {
                                        $this->createStoreWebsiteCategory($category->parent->id, $website->id, $grandCategoryDetails);

                                        $this->logadd('#20', $catId, $website->id, $category->id, 'If Category is exit then category stored.');
                                    }
                                }

                                $grandRemoteId = $grandCategoryDetails;
                            } else {
                                $grandRemoteId = $grandCategoryDetail->remote_id;
                            }
                            //Search for child category

                            $data['id'] = $category->parent->id;
                            $data['level'] = 2;
                            $data['name'] = ucwords($category->parent->title);
                            $data['parentId'] = $grandRemoteId;
                            $parentId = $grandRemoteId;

                            if (class_exists('\\seo2websites\\MagentoHelper\\MagentoHelper')) {
                                $childCategoryDetails = MagentoHelper::createCategory($parentId, $data, $website->id);

                                if ($childCategoryDetails == false) {
                                    $this->logadd('#21', $catId, $website->id, 0, 'Website not found.');
                                } else {
                                    $this->logadd('#22', $catId, $website->id, $childCategoryDetails, "Found remote id $childCategoryDetails And created category catalog.");
                                }
                            }

                            $checkIfExist = StoreWebsiteCategory::where('store_website_id', $website->id)->where('category_id', $category->parent->id)->where('remote_id', $childCategoryDetails)->first();
                            if (empty($checkIfExist)) {
                                $this->createStoreWebsiteCategory($category->parent->id, $website->id, $childCategoryDetails);

                                $this->logadd('#23', $catId, $website->id, $category->parent->id, 'If Category parent id is exit then category stored.');
                            }

                            $data['id'] = $category->id;
                            $data['level'] = 3;
                            $data['name'] = ucwords($category->title);
                            $data['parentId'] = $childCategoryDetails;

                            if (class_exists('\\seo2websites\\MagentoHelper\\MagentoHelper')) {
                                $categoryDetail = MagentoHelper::createCategory($childCategoryDetails, $data, $website->id);

                                if ($childCategoryDetails == false) {
                                    $this->logadd('#24', $catId, $website->id, 0, 'Website not found.');
                                } else {
                                    $this->logadd('#25', $catId, $website->id, $categoryDetail, "Found remote id $categoryDetail And created category catalog.");
                                }
                            }

                            if ($categoryDetail) {
                                $checkIfExist = StoreWebsiteCategory::where('store_website_id', $website->id)->where('category_id', $category->id)->where('remote_id', $categoryDetail)->first();
                                if (empty($checkIfExist)) {
                                    $this->createStoreWebsiteCategory($category->id, $website->id, $categoryDetail);

                                    $this->logadd('#26', $catId, $website->id, $category->id, 'If Category is exit then website category stored in case third.');
                                }
                            }
                        }
                    }

                    if ($case == 'fourth') {
                        $this->logadd('#17', $catId, $website->id, $case, 'Check fourth case is exit');
                        //Find Parent
                        $parentCategory = StoreWebsiteCategory::where('store_website_id', $website->id)->where('category_id', $category->id)->whereNotNull('remote_id')->first();

                        //Check if parent had remote id
                        if (empty($parentCategory)) {
                            //check for grandparent
                            $grandCategory = Category::find($category->parent->id);
                            $grandCategoryDetail = StoreWebsiteCategory::where('store_website_id', $website->id)->where('category_id', $grandCategory->parent->id)->whereNotNull('remote_id')->first();

                            //Check if parent had remote id
                            if (empty($grandCategoryDetail)) {
                                //check for grandparent
                                $grandParentCategory = Category::find($category->parent->ParentC->id);
                                $grandParentCategoryDetail = StoreWebsiteCategory::where('store_website_id', $website->id)->where('category_id', $grandParentCategory->parent->id)->whereNotNull('remote_id')->first();

                                if (empty($grandParentCategoryDetail)) {
                                    $data['id'] = $category->id;
                                    $data['level'] = 1;
                                    $data['name'] = ($request->category_name) ? ucwords($request->category_name) : ucwords($category->title);
                                    $data['parentId'] = 0;
                                    $parentId = 0;

                                    if (class_exists('\\seo2websites\\MagentoHelper\\MagentoHelper')) {
                                        $grandParentCategoryDetails = MagentoHelper::createCategory($parentId, $data, $website->id);

                                        if ($grandParentCategoryDetails == false) {
                                            $this->logadd('#18', $catId, $website->id, 0, 'Website not found.');
                                        } else {
                                            $this->logadd('#19', $catId, $website->id, $grandParentCategoryDetails, "Found remote id $grandParentCategoryDetails And created category catalog.");
                                        }
                                    }

                                    if ($grandParentCategoryDetails) {
                                        $checkIfExist = StoreWebsiteCategory::where('store_website_id', $website->id)->where('category_id', $category->parent->id)->where('remote_id', $grandParentCategoryDetails)->first();
                                        if (empty($checkIfExist)) {
                                            $this->createStoreWebsiteCategory($category->parent->id, $website->id, $grandParentCategoryDetails);

                                            $this->logadd('#20', $catId, $website->id, $category->id, 'If Category is exit then category stored.');
                                        }
                                    }

                                    $grandParentRemoteId = $grandParentCategoryDetails;
                                } else {
                                    $grandParentRemoteId = $grandParentCategoryDetail->remote_id;
                                }
                                //Search for child category

                                $data['id'] = $category->parent->id;
                                $data['level'] = 2;
                                $data['name'] = ucwords($category->parent->title);
                                $data['parentId'] = $grandParentRemoteId;
                                $parentId = $grandParentRemoteId;

                                if (class_exists('\\seo2websites\\MagentoHelper\\MagentoHelper')) {
                                    $grandCategoryDetails = MagentoHelper::createCategory($parentId, $data, $website->id);

                                    if ($grandCategoryDetails == false) {
                                        $this->logadd('#21', $catId, $website->id, 0, 'Website not found.');
                                    } else {
                                        $this->logadd('#22', $catId, $website->id, $grandCategoryDetails, "Found remote id $grandCategoryDetails And created category catalog.");
                                    }
                                }

                                $checkIfExist = StoreWebsiteCategory::where('store_website_id', $website->id)->where('category_id', $category->parent->id)->where('remote_id', $grandCategoryDetails)->first();
                                if (empty($checkIfExist)) {
                                    $this->createStoreWebsiteCategory($category->parent->id, $website->id, $grandCategoryDetails);

                                    $this->logadd('#23', $catId, $website->id, $category->parent->id, 'If Category parent id is exit then category stored.');
                                }

                                $data['id'] = $category->parent->id;
                                $data['level'] = 3;
                                $data['name'] = ucwords($category->parent->title);
                                $data['parentId'] = $grandCategoryDetails;
                                $parentId = $grandCategoryDetails;

                                if (class_exists('\\seo2websites\\MagentoHelper\\MagentoHelper')) {
                                    $childCategoryDetails = MagentoHelper::createCategory($parentId, $data, $website->id);

                                    if ($childCategoryDetails == false) {
                                        $this->logadd('#21', $catId, $website->id, 0, 'Website not found.');
                                    } else {
                                        $this->logadd('#22', $catId, $website->id, $childCategoryDetails, "Found remote id $childCategoryDetails And created category catalog.");
                                    }
                                }

                                $checkIfExist = StoreWebsiteCategory::where('store_website_id', $website->id)->where('category_id', $category->parent->id)->where('remote_id', $childCategoryDetails)->first();
                                if (empty($checkIfExist)) {
                                    $this->createStoreWebsiteCategory($category->parent->id, $website->id, $childCategoryDetails);

                                    $this->logadd('#23', $catId, $website->id, $category->parent->id, 'If Category parent id is exit then category stored.');
                                }

                                $data['id'] = $category->id;
                                $data['level'] = 4;
                                $data['name'] = ucwords($category->title);
                                $data['parentId'] = $childCategoryDetails;

                                if (class_exists('\\seo2websites\\MagentoHelper\\MagentoHelper')) {
                                    $categoryDetail = MagentoHelper::createCategory($childCategoryDetails, $data, $website->id);

                                    if ($childCategoryDetails == false) {
                                        $this->logadd('#24', $catId, $website->id, 0, 'Website not found.');
                                    } else {
                                        $this->logadd('#25', $catId, $website->id, $categoryDetail, "Found remote id $categoryDetail And created category catalog.");
                                    }
                                }

                                if ($categoryDetail) {
                                    $checkIfExist = StoreWebsiteCategory::where('store_website_id', $website->id)->where('category_id', $category->id)->where('remote_id', $categoryDetail)->first();
                                    if (empty($checkIfExist)) {
                                        $this->createStoreWebsiteCategory($category->id, $website->id, $categoryDetail);

                                        $this->logadd('#26', $catId, $website->id, $category->id, 'If Category is exit then website category stored in case third.');
                                    }
                                }
                            }
                        }
                    }
                }
                //end copy
            }
            $storeWebsites = [];
            foreach ($websites as $website) {
                $storeWebsites[] = $website->id;
                $swc_user_history = new StoreWebsiteCategoryUserHistory;
                $swc_user_history->store_id = $website->id;
                $swc_user_history->category_id = $catId;
                $swc_user_history->user_id = Auth::user()->id;

                $msg = '';
                if ($request->check == 0) {
                    $categoryStore = StoreWebsiteCategory::where('category_id', $catId)->where('store_website_id', $website->id)->first();
                    if ($categoryStore) {
                        $categoryStore->delete();
                        $msg = 'Remove successfully';

                        $this->logadd('#27', $catId, $website->id, $catId, 'Website Category is Remove.');
                    }
                    $swc_user_history->website_action = 'unchecked';
                } else {
                    StoreWebsiteCategory::updateOrCreate(
                        ['category_id' => $catId, 'store_website_id' => $website->id],
                        ['category_name' => $request->category_name, 'remote_id' => @$categ]
                    );
                    $msg = 'Added successfully';

                    $this->logadd('#28', $catId, $website->id, $catId, 'Website Category update or store.');
                    $swc_user_history->website_action = 'checked';
                }

                $swc_user_history->save();
            }
        }

        return response()->json(['code' => 200, 'message' => $msg, 'storeWebsites' => $storeWebsites]);
    }

    public function createStoreWebsiteCategory($category_id, $store_website_id, $remote_id)
    {
        $storeWebsiteCategory = new StoreWebsiteCategory;
        $storeWebsiteCategory->category_id = $category_id;
        $storeWebsiteCategory->store_website_id = $store_website_id;
        $storeWebsiteCategory->remote_id = $remote_id;
        $storeWebsiteCategory->save();
    }
}
