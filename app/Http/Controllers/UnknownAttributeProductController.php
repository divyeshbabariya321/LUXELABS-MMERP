<?php

namespace App\Http\Controllers;
use App\ProductUpdatedAttributeHistory;
use App\Jobs\AttributeAssignment;
use App\Colors;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\JsonResponse;
use App\Size;
use Illuminate\Support\Facades\Validator;
use DataTables;
use App\Product;
use App\Category;
use Illuminate\Http\Request;
use App\Helpers\StatusHelper;

class UnknownAttributeProductController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        if ($request->ajax()) {
            $query = Product::query();
            $query->select(
                'products.id',
                'sku',
                'name',
                'supplier',
                'status_id',
                'categories.title as category_title',
                'category',
                'updated_attribute_job_status',
                'updated_attribute_job_attempt_count',
                DB::raw('(CASE WHEN status_id = 36 THEN "Unknown Category" WHEN status_id = 37 THEN "Unknown Color"  WHEN status_id = 38 THEN "Unknown Size" WHEN status_id = 40 THEN "Unknown Measurement" ELSE "" END) AS attribute_name'),
                DB::raw('(CASE WHEN status_id = 36 THEN categories.title WHEN status_id = 37 THEN color WHEN status_id = 38 THEN size WHEN status_id = 40 THEN CONCAT(lmeasurement," * ",hmeasurement," * ",dmeasurement) ELSE "" END) AS erp_value')
            );
            $query->leftjoin('categories', 'products.category', 'categories.id');
            if (isset($request->status_id) && ! empty($request->status_id)) {
                $query->where('status_id', $request->status_id);
            } else {
                $query->whereIn('status_id', [StatusHelper::$unknownSize, StatusHelper::$unknownMeasurement, StatusHelper::$unknownCategory, StatusHelper::$unknownColor]);
            }
            if (isset($request->filter_stock) && $request->filter_stock == 'out_of_stock') {
                $query->where('stock', 0);
            } elseif (isset($request->filter_stock) && $request->filter_stock == 'in_stock') {
                $query->where('stock', '>=', 1);
            }
            if (isset($request->filter_job_status) && $request->filter_job_status == 'pending') {
                $query->where('updated_attribute_job_status', 0);
            } elseif (isset($request->filter_job_status) && $request->filter_job_status == 'success') {
                $query->where('updated_attribute_job_status', 1);
            } elseif (isset($request->filter_job_status) && $request->filter_job_status == 'failed') {
                $query->where('updated_attribute_job_status', 2);
            }

            return Datatables::of($query)
                ->addIndexColumn()
                ->addColumn('original_value', function ($row) {
                    $original_value   = $row->erp_value;
                    $status_id        = $row->status_id;
                    $old_category     = '';
                    $old_size         = '';
                    $old_dmeasurement = '';
                    $old_hmeasurement = '';
                    $old_dmeasurement = '';
                    if (isset($row->attribute_histories) && count($row->attribute_histories)) {
                        $attribute_histories = $row->attribute_histories;
                        foreach ($attribute_histories as $ah_key => $ah_value) {
                            if ($ah_value->attribute_name == 'category') {
                                $old_category = $ah_value->old_value;
                                if (isset($ah_value->old_category) && ! empty($ah_value->old_category)) {
                                    $old_category = $ah_value->old_category->title;
                                }
                            } elseif ($ah_value->attribute_name == 'size') {
                                $old_size = $ah_value->old_value;
                            } elseif ($ah_value->attribute_name == 'lmeasurement') {
                                $old_lmeasurement = $ah_value->old_value;
                            } elseif ($ah_value->attribute_name == 'hmeasurement') {
                                $old_hmeasurement = $ah_value->old_value;
                            } elseif ($ah_value->attribute_name == 'dmeasurement') {
                                $old_dmeasurement = $ah_value->old_value;
                            } else {
                                $original_value = $ah_value->old_value;
                            }
                        }
                        if ($status_id == StatusHelper::$unknownCategory) {
                            $original_value = $old_category;
                        } elseif ($status_id == StatusHelper::$unknownSize) {
                            $original_value = $old_size;
                        } elseif ($status_id == StatusHelper::$unknownMeasurement) {
                            if ($old_lmeasurement == '' && $old_hmeasurement == '' && $old_dmeasurement == '') {
                                $original_value = '';
                            } else {
                                $original_value = $old_lmeasurement . ' * ' . $old_hmeasurement . ' * ' . $old_dmeasurement;
                            }
                        }
                    } elseif ($status_id == StatusHelper::$unknownCategory) {
                        $original_value = ($row->category_title != null) ? $row->category_title : $row->category;
                    }

                    return $original_value;
                })
                ->addColumn('erp_value', function ($row) {
                    $erp_value = $row->erp_value;
                    $status_id = $row->status_id;
                    if ($status_id == StatusHelper::$unknownCategory) {
                        $erp_value = ($row->category_title != null) ? $row->category_title : $row->category;
                    }

                    return $erp_value;
                })
                ->addColumn('action', function ($row) {
                    $actionBtn = '<a href="javascript:void(0)" data-id="' . $row->id . '" class="get-product-attribute-detail btn btn-warning btn-sm">Update</a>&nbsp;';
                    $actionBtn .= '<a href="javascript:void(0)" data-id="' . $row->id . '" class="get-product-attribute-history btn btn-warning btn-sm"><i class="fa fa-bars"></i></a>';

                    return $actionBtn;
                })
                ->rawColumns(['action'])
                ->make(true);
        }
        $status_list[StatusHelper::$unknownSize]        = 'Unknown Size';
        $status_list[StatusHelper::$unknownMeasurement] = 'Unknown Measurement';
        $status_list[StatusHelper::$unknownCategory]    = 'Unknown Category';
        $status_list[StatusHelper::$unknownColor]       = 'Unknown Color';

        $categories = Category::all();
        $sizes      = Size::all();

        $colors = new Colors;
        $colors = $colors->all();

        return view('unknown-attribute-product.index', compact('status_list', 'categories', 'sizes', 'colors'));
    }

    public function attributeAssignment(Request $request): JsonResponse
    {
        $validateArr['attribute_id'] = 'required';

        if ($request->attribute_id == StatusHelper::$unknownCategory) {
            $validateArr['find_category']    = 'nullable';
            $validateArr['replace_category'] = 'nullable';
            if ($request->find_category == $request->replace_category) {
                return response()->json(['code' => 500, 'message' => 'New value can\'t be same as old value. Please select/enter different value.']);
            }
        } elseif ($request->attribute_id == StatusHelper::$unknownColor) {
            $validateArr['find_color']    = 'nullable';
            $validateArr['replace_color'] = 'nullable';

            if ($request->find_color == $request->replace_color) {
                return response()->json(['code' => 500, 'message' => 'New value can\'t be same as old value. Please select/enter different value.']);
            }
        } elseif ($request->attribute_id == StatusHelper::$unknownSize) {
            $validateArr['find_size']    = 'nullable';
            $validateArr['replace_size'] = 'nullable';
            if ($request->find_size == $request->replace_size) {
                return response()->json(['code' => 500, 'message' => 'New value can\'t be same as old value. Please select/enter different value.']);
            }
        } elseif ($request->attribute_id == StatusHelper::$unknownMeasurement) {
            $validateArr['find_lmeasurement']    = 'nullable';
            $validateArr['replace_lmeasurement'] = 'nullable';

            $validateArr['find_hmeasurement']    = 'nullable';
            $validateArr['replace_hmeasurement'] = 'nullable';

            $validateArr['find_dmeasurement']    = 'nullable';
            $validateArr['replace_dmeasurement'] = 'nullable';

            if ($request->find_lmeasurement == $request->replace_lmeasurement || $request->find_hmeasurement == $request->replace_hmeasurement || $request->find_dmeasurement == $request->replace_dmeasurement) {
                return response()->json(['code' => 500, 'message' => 'New value can\'t be same as old value. Please select/enter different value.']);
            }
        }
        $validator = Validator::make($request->all(), $validateArr);

        if ($validator->fails()) {
            $return = ['code' => 500, 'message' => $validator->errors()->first()];
        } else {
            $data['data']            = $request->all();
            $data['data']['user_id'] = Auth::user()->id;
            AttributeAssignment::dispatch($data)->onQueue('attribute_assignment');

            $return = ['code' => 200, 'message' => 'Attribute assignment request is submitted'];
        }

        return response()->json($return);
    }

    public function updateAttributeAssignment(Request $request): JsonResponse
    {
        $validateArr['product_id']   = 'required';
        $validateArr['attribute_id'] = 'required';

        if ($request->attribute_id == StatusHelper::$unknownCategory) {
            $validateArr['replace_category'] = 'nullable';
        } elseif ($request->attribute_id == StatusHelper::$unknownColor) {
            $validateArr['replace_color'] = 'nullable';
        } elseif ($request->attribute_id == StatusHelper::$unknownSize) {
            $validateArr['replace_size'] = 'nullable';
        } elseif ($request->attribute_id == StatusHelper::$unknownMeasurement) {
            $validateArr['replace_lmeasurement'] = 'nullable';

            $validateArr['replace_hmeasurement'] = 'nullable';

            $validateArr['replace_dmeasurement'] = 'nullable';
        }
        $validator = Validator::make($request->all(), $validateArr);

        if ($validator->fails()) {
            $return = ['code' => 500, 'message' => $validator->errors()->first()];
        } else {
            $userId       = Auth::user()->id;
            $find_product = Product::where('status_id', $request->attribute_id);
            $find_product->where('id', $request->product_id);
            $find_product = $find_product->first();

            if (isset($find_product) && ! empty($find_product)) {
                if ($request->attribute_id == StatusHelper::$unknownSize) {
                    $old_value_size = $find_product->size;
                    $new_value_size = '';
                    if (isset($request->replace_size) && is_array($request->replace_size)) {
                        $new_value_size = implode(',', $request->replace_size);
                    }
                    if ($find_product->size == $new_value_size) {
                        return response()->json(['code' => 500, 'message' => 'Same size are not allowed!']);
                    }

                    $find_product->size = $new_value_size;
                    $find_product->save();

                    $productUpdatedAttributeHistory = ProductUpdatedAttributeHistory::create([
                        'old_value'      => $old_value_size,
                        'new_value'      => $new_value_size,
                        'attribute_name' => 'size',
                        'attribute_id'   => $request->attribute_id,
                        'product_id'     => $request->product_id,
                        'user_id'        => $userId,
                    ]);
                } elseif ($request->attribute_id == StatusHelper::$unknownMeasurement) {
                    $old_value_lmeasurement = $find_product->lmeasurement;
                    $old_value_hmeasurement = $find_product->hmeasurement;
                    $old_value_dmeasurement = $find_product->dmeasurement;

                    if ($old_value_lmeasurement == $request->replace_lmeasurement || $old_value_hmeasurement == $request->replace_hmeasurement || $old_value_dmeasurement == $request->replace_dmeasurement) {
                        return response()->json(['code' => 500, 'message' => 'New value can\'t be same as old value. Please select/enter different value.']);
                    }

                    $find_product->lmeasurement = $request->replace_lmeasurement;
                    $find_product->hmeasurement = $request->replace_hmeasurement;
                    $find_product->dmeasurement = $request->replace_dmeasurement;
                    $find_product->save();

                    $productUpdatedAttributeHistory = ProductUpdatedAttributeHistory::create([
                        'old_value'      => $old_value_lmeasurement,
                        'new_value'      => $request->replace_lmeasurement,
                        'attribute_name' => 'lmeasurement',
                        'attribute_id'   => $request->attribute_id,
                        'product_id'     => $request->product_id,
                        'user_id'        => $userId,
                    ]);

                    $productUpdatedAttributeHistory = ProductUpdatedAttributeHistory::create([
                        'old_value'      => $old_value_hmeasurement,
                        'new_value'      => $request->replace_hmeasurement,
                        'attribute_name' => 'hmeasurement',
                        'attribute_id'   => $request->attribute_id,
                        'product_id'     => $request->product_id,
                        'user_id'        => $userId,
                    ]);

                    $productUpdatedAttributeHistory = ProductUpdatedAttributeHistory::create([
                        'old_value'      => $old_value_dmeasurement,
                        'new_value'      => $request->replace_dmeasurement,
                        'attribute_name' => 'dmeasurement',
                        'attribute_id'   => $request->attribute_id,
                        'product_id'     => $request->product_id,
                        'user_id'        => $userId,
                    ]);
                } elseif ($request->attribute_id == StatusHelper::$unknownCategory) {
                    if ($find_product->category == $request->replace_category) {
                        return response()->json(['code' => 500, 'message' => 'New value can\'t be same as old value. Please select/enter different value.']);
                    }
                    $old_value_category = $find_product->category;

                    $find_product->category = $request->replace_category;
                    $find_product->save();

                    $productUpdatedAttributeHistory = ProductUpdatedAttributeHistory::create([
                        'old_value'      => $old_value_category,
                        'new_value'      => $request->replace_category,
                        'attribute_name' => 'category',
                        'attribute_id'   => $request->attribute_id,
                        'product_id'     => $request->product_id,
                        'user_id'        => $userId,
                    ]);
                } elseif ($request->attribute_id == StatusHelper::$unknownColor) {
                    $old_value_color = $find_product->color;
                    $new_value_color = ($request->replace_color != 'NULL') ? $request->replace_color : null;

                    if ($old_value_color == $new_value_color) {
                        return response()->json(['code' => 500, 'message' => 'New value can\'t be same as old value. Please select/enter different value.']);
                    }

                    $find_product->color = $new_value_color;
                    $find_product->save();

                    $productUpdatedAttributeHistory = ProductUpdatedAttributeHistory::create([
                        'old_value'      => $old_value_color,
                        'new_value'      => $new_value_color,
                        'attribute_name' => 'color',
                        'attribute_id'   => $request->attribute_id,
                        'product_id'     => $request->product_id,
                        'user_id'        => $userId,
                    ]);
                }
            }
            $return = ['code' => 200, 'message' => 'Update Attribute successfully!'];
        }

        return response()->json($return);
    }

    public function getProductAttributeDetails(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'product_id' => 'required',
        ]);

        if ($validator->fails()) {
            $return = ['code' => 500, 'message' => 'Invalid request parameters'];
        } else {
            $query = Product::select(
                'products.id',
                'sku',
                'name',
                'supplier',
                'status_id',
                'categories.title as category_title',
                'category',
                DB::raw('(CASE WHEN status_id = 36 THEN "Unknown Category" WHEN status_id = 37 THEN "Unknown Color"  WHEN status_id = 38 THEN "Unknown Size" WHEN status_id = 40 THEN "Unknown Measurement" ELSE "" END) AS attribute_name'),
                DB::raw('(CASE WHEN status_id = 36 THEN categories.title WHEN status_id = 37 THEN color WHEN status_id = 38 THEN size WHEN status_id = 40 THEN CONCAT(lmeasurement," * ",hmeasurement," * ",dmeasurement) ELSE "" END) AS erp_value')
            );
            $query->leftjoin('categories', 'products.category', 'categories.id');
            $query->where('products.id', $request->product_id);
            $product = $query->first();

            if (isset($product) && ! empty($product)) {
                $product->original_value = $product->erp_value;
                $status_id               = $product->status_id;
                $old_category            = '';
                $old_size                = '';
                $old_dmeasurement        = '';
                $old_hmeasurement        = '';
                $old_dmeasurement        = '';
                if (isset($product->attribute_histories) && count($product->attribute_histories)) {
                    $attribute_histories = $product->attribute_histories;
                    foreach ($attribute_histories as $ah_key => $ah_value) {
                        if ($ah_value->attribute_name == 'category') {
                            $old_category = $ah_value->old_value;
                            if (isset($ah_value->old_category) && ! empty($ah_value->old_category)) {
                                $old_category = $ah_value->old_category->title;
                            }
                        } elseif ($ah_value->attribute_name == 'size') {
                            $old_size = $ah_value->old_value;
                        } elseif ($ah_value->attribute_name == 'lmeasurement') {
                            $old_lmeasurement = $ah_value->old_value;
                        } elseif ($ah_value->attribute_name == 'hmeasurement') {
                            $old_hmeasurement = $ah_value->old_value;
                        } elseif ($ah_value->attribute_name == 'dmeasurement') {
                            $old_dmeasurement = $ah_value->old_value;
                        } else {
                            $product->original_value = $ah_value->old_value;
                        }
                    }
                    if ($status_id == StatusHelper::$unknownCategory) {
                        $product->original_value = $old_category;
                    } elseif ($status_id == StatusHelper::$unknownSize) {
                        $product->original_value = $old_size;
                    } elseif ($status_id == StatusHelper::$unknownMeasurement) {
                        if ($old_lmeasurement == '' && $old_hmeasurement == '' && $old_dmeasurement == '') {
                            $original_value = '';
                        } else {
                            $product->original_value = $old_lmeasurement . ' * ' . $old_hmeasurement . ' * ' . $old_dmeasurement;
                        }
                    }
                } elseif ($status_id == StatusHelper::$unknownCategory) {
                    $product->original_value = ($product->category_title != null) ? $product->category_title : $product->category;
                }

                if ($status_id == StatusHelper::$unknownCategory) {
                    $product->erp_value = ($product->category_title != null) ? $product->category_title : $product->category;
                }
                $return = ['code' => 200, 'message' => 'Success', 'results' => $product];
            } else {
                $return = ['code' => 500, 'message' => 'No Results Found.'];
            }
        }

        return response()->json($return);
    }

    public function getProductAttributeHistory(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'product_id' => 'required',
        ]);

        if ($validator->fails()) {
            $return = ['code' => 500, 'message' => 'Invalid request parameters'];
        } else {
            $histories = ProductUpdatedAttributeHistory::where('product_id', $request->product_id)->latest()->get();

            if (isset($histories) && ! empty($histories)) {
                $show_history = (string) view('unknown-attribute-product.product_updated_history', compact('histories'));
                $return       = ['code' => 200, 'message' => 'Success', 'results' => $show_history];
            } else {
                $return = ['code' => 500, 'message' => 'No Results Found.'];
            }
        }

        return response()->json($return);
    }
}
