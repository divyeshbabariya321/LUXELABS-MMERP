<?php

namespace App\Http\Controllers;
use App\User;
use App\Http\Controllers;
use App\CroppingInstance;

use App\Brand;
use App\Category;
use App\CropImageGetRequest;
use App\CroppedImageReference;
use App\Helpers\StatusHelper;
use App\LogRequest;
use App\Models\DataTableColumn;
use App\Product;
use App\Supplier;
use DataTables;
use GuzzleHttp\Client;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

class CroppedImageReferenceController extends Controller
{
    protected $dateFormatYmdHis;

    public function __construct()
    {
        $this->dateFormatYmdHis = date('Y-m-d H:i:s');
    }

    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        $products = CroppedImageReference::with(['media', 'newMedia'])->orderByDesc('id')->paginate(50);

        return view('image_references.index', compact('products'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function show(CroppedImageReference $croppedImageReference)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function edit(CroppedImageReference $croppedImageReference)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, CroppedImageReference $croppedImageReference)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy(CroppedImageReference $croppedImageReference)
    {
        //
    }

    public function grid_old(Request $request)
    {
        Log::info('#####crop_reference_grid_page_start#####: '.$this->dateFormatYmdHis);
        $query = new CroppedImageReference;
        $query = $query->where('product_id', '!=', 0);

        if ($request->category || $request->brand || $request->supplier || $request->crop || $request->status || $request->filter_id) {
            Log::info('crop_reference_grid_page_filter_start: '.$this->dateFormatYmdHis);
            if (is_array(request('category'))) {
                if (request('category') != null && request('category')[0] != 1) {
                    $query->whereHas('product', function ($qu) {
                        $qu->whereIn('category', request('category'));
                    });
                }
            } else {
                if (request('category') != null && request('category') != 1) {
                    $query->whereHas('product', function ($qu) {
                        $qu->where('category', request('category'));
                    });
                }
            }

            if (isset($request->filter_id) && $request->filter_id) {
                $query->whereHas('product', function ($qu) use ($request) {
                    $qu->whereIn('id', $request->filter_id);
                });
            }

            if (request('brand') != null && $request->brand) {
                $query->whereHas('product', function ($qu) {
                    $qu->whereIn('brand', request('brand'));
                });
            }

            if (request('supplier') != null) {
                $query = $query->whereHas('product', function ($qu) {
                    $qu->whereIn('supplier_id', request('supplier'));
                });
            }

            if (request('status') != null && request('status') != 0) {
                $query->whereHas('product', function ($qu) {
                    $qu->where('status_id', request('status'));
                });
            } else {
                $query->whereHas('product', function ($qu) {
                    $qu->where('status_id', '!=', StatusHelper::$cropRejected);
                });
            }

            if (request('crop') != null) {
                if (request('crop') == 2) {
                    $query->whereNotNull('new_media_id');
                } elseif (request('crop') == 3) {
                    $query->whereNull('new_media_id');
                }
            }
            $products = $query->orderByDesc('id')->paginate(10);
            Log::info('crop_reference_grid_page_filter_end: '.$this->dateFormatYmdHis);
        } else {
            Log::info('crop_reference_grid_page_without_filter_start: '.$this->dateFormatYmdHis);
            $query->whereHas('product', function ($qu) {
                $qu->where('status_id', '!=', StatusHelper::$cropRejected);
            });

            $products = $query->orderByDesc('id')->paginate(10);
            Log::info('crop_reference_grid_page_without_filter_end: '.$this->dateFormatYmdHis);
        }
        $total = $products->total();

        Log::info('crop_reference_grid_page_pending_product_start: '.$this->dateFormatYmdHis);
        $pendingProduct = Product::where('status_id', StatusHelper::$autoCrop)->where('stock', '>=', 1)->count();
        Log::info('crop_reference_grid_page_pending_product_end: '.$this->dateFormatYmdHis);

        Log::info('crop_reference_grid_page_pending_category_product_start: '.$this->dateFormatYmdHis);
        $pendingCategoryProduct = Product::where('status_id', StatusHelper::$attributeRejectCategory)->where('stock', '>=', 1)->count();
        Log::info('crop_reference_grid_page_pending_category_product_end: '.$this->dateFormatYmdHis);

        Log::info('crop_reference_grid_page_customer_range_start: '.$this->dateFormatYmdHis);
        if (request('customer_range') != null) {
            $dateArray = explode('-', request('customer_range'));
            $startDate = trim($dateArray[0]);
            $endDate = trim(end($dateArray));
            if ($startDate == '1995/12/25') {
                $totalCounts = CroppedImageReference::where('created_at', '>=', \Carbon\Carbon::now()->subHour())->count();
            } elseif ($startDate == $endDate) {
                $totalCounts = CroppedImageReference::whereDate('created_at', '=', end($dateArray))->count();
            } else {
                $totalCounts = CroppedImageReference::whereBetween('created_at', [$startDate, $endDate])->count();
            }

            if ($request->ajax()) {
                return response()->json([
                    'count' => $totalCounts,
                ], 200);
            }
        } else {
            $totalCounts = 0;
        }
        Log::info('crop_reference_grid_page_customer_range_end: '.$this->dateFormatYmdHis);

        if ($request->ajax()) {
            return response()->json([
                'tbody' => view('image_references.partials.griddata', compact('products', 'total', 'pendingProduct', 'totalCounts', 'pendingCategoryProduct'))->render(),
                'links' => (string) $products->appends(request()->except(['page']))->render(),
                'total' => $total,
            ], 200);
        }
        Log::info('####crop_reference_grid_page_end####: '.$this->dateFormatYmdHis);
        $quick_task_users = User::all();

        return view('image_references.grid', compact('products', 'total', 'pendingProduct', 'totalCounts', 'pendingCategoryProduct', 'quick_task_users'));
    }

    public function grid(Request $request)
    {
        Log::info('#####crop_reference_grid_page_start#####: '.$this->dateFormatYmdHis);
        if ($request->ajax()) {
            $query = CroppedImageReference::query();
            $query = $query->with(['differentWebsiteImages', 'product', 'httpRequestData.requestData', 'product.product_category', 'product.brands', 'newMedia']);
            $query = $query->where('product_id', '!=', 0);

            if ($request->category || $request->brand || $request->supplier || $request->crop || $request->status || $request->filter_id) {
                Log::info('crop_reference_grid_page_filter_start: '.$this->dateFormatYmdHis);
                if (is_array(request('category'))) {
                    if (request('category') != null && request('category')[0] != 1) {
                        $query->whereHas('product', function ($qu) {
                            $qu->whereIn('category', request('category'));
                        });
                    }
                } else {
                    if (request('category') != null && request('category') != 1) {
                        $query->whereHas('product', function ($qu) {
                            $qu->where('category', request('category'));
                        });
                    }
                }

                if (isset($request->filter_id) && $request->filter_id) {
                    $query->whereHas('product', function ($qu) use ($request) {
                        $qu->whereIn('id', $request->filter_id);
                    });
                }

                if (request('brand') != null && $request->brand) {
                    $query->whereHas('product', function ($qu) {
                        $qu->whereIn('brand', request('brand'));
                    });
                }

                if (request('supplier') != null) {
                    $query = $query->whereHas('product', function ($qu) {
                        $qu->whereIn('supplier_id', request('supplier'));
                    });
                }

                if (request('status') != null && request('status') != 0) {
                    $query->whereHas('product', function ($qu) {
                        $qu->where('status_id', request('status'));
                    });
                } else {
                    $query->whereHas('product', function ($qu) {
                        $qu->where('status_id', '!=', StatusHelper::$cropRejected);
                    });
                }

                if (request('crop') != null) {
                    if (request('crop') == 2) {
                        $query->whereNotNull('new_media_id');
                    } elseif (request('crop') == 3) {
                        $query->whereNull('new_media_id');
                    }
                }
                $query->orderByDesc('id');
                Log::info('crop_reference_grid_page_filter_end: '.$this->dateFormatYmdHis);
            } else {
                Log::info('crop_reference_grid_page_without_filter_start: '.$this->dateFormatYmdHis);
                $query->whereHas('product', function ($qu) {
                    $qu->where('status_id', '!=', StatusHelper::$cropRejected);
                });

                $query->orderByDesc('id');
                Log::info('crop_reference_grid_page_without_filter_end: '.$this->dateFormatYmdHis);
            }

            return Datatables::of($query)
                ->addIndexColumn()
                ->addColumn('id', function ($row) {
                    $id = '<input type="checkbox" name="issue" value="'.$row->id.'" class="checkBox" data-id="'.$row->product_id.'">';
                    $id .= $row->id;

                    return $id;
                })
                ->addColumn('product_id', function ($row) {
                    return $row->product_id;
                })
                ->addColumn('product_category_title', function ($row) {
                    return (isset($row->product->product_category->title) && ! empty($row->product->product_category->title)) ? $row->product->product_category->title : '';
                })
                ->addColumn('product_supplier', function ($row) {
                    return (isset($row->product->supplier) && ! empty($row->product->supplier)) ? $row->product->supplier : '';
                })
                ->addColumn('product_brands_name', function ($row) {
                    return (isset($row->product->brands->name) && ! empty($row->product->brands->name)) ? $row->product->brands->name : '';
                })
                ->addColumn('store_website', function ($row) {
                    $websites = [];
                    if (isset($row->product)) {
                        $listofWebsite = $row->product->getWebsites();
                        if (! $listofWebsite->isEmpty()) {
                            foreach ($listofWebsite as $lw) {
                                $websites[] = $lw->title;
                            }
                        }
                    }

                    return implode('</br>', $websites);
                })
                ->addColumn('image_score', function ($row) {
                    return $row->image_score;
                })
                ->addColumn('original_image', function ($row) {
                    $original_image = '<div style="width: 100px;margin-top: 25px; display: inline-block;">';
                    $src = $row->media ? getMediaUrl($row->media) : 'https://localhost/erp/public/uploads/product/29/296559/123.webp';
                    $onclick_url = $row->media ? getMediaUrl($row->media) : '';
                    $original_image .= '<img src="'.$src.'" alt="" height="100" width="100"  alt="" height="100" width="100" onclick="bigImg(`'.$onclick_url.'`)">';
                    $original_image .= '</div>';

                    return $original_image;
                })
                ->addColumn('cropped_image', function ($row) {
                    $cropped_image = '';
                    if ($row->newMedia) {
                        $cropped_image .= '<table class="table-striped table-bordered table" id="log-table">';
                        $cropped_image .= '<tbody>';
                        $cropped_image .= '<tr>';
                        foreach ($row->differentWebsiteImages as $images) {
                            $cropped_image .= '<td>';
                            $cropped_image .= '<div style="width: 100px;margin: 0px;display: inline-block;">';
                            $cropped_image .= ($images->newMedia) ? $images->getDifferentWebsiteName($images->newMedia->id) : 'N/A';
                            $src = $images->newMedia ? getMediaUrl($images->newMedia) : 'https://localhost/erp/public/uploads/product/29/296559/123.webp';
                            $onclick = $images->newMedia ? getMediaUrl($images->newMedia) : '';
                            $cropped_image .= '<img src="'.$src.'" alt="" height="100" width="100" onclick="bigImg(`'.$onclick.'`)">';
                            $cropped_image .= '</div>';
                            $cropped_image .= '</td>';
                        }
                        $cropped_image .= '</tr>';
                        $cropped_image .= '</tbody>';
                        $cropped_image .= '</table>';
                    }

                    return $cropped_image;
                })
                ->addColumn('speed', function ($row) {
                    $speed = number_format((float) str_replace('0:00:', '', $row->speed), 4, '.', '').' sec';

                    return $speed;
                })
                ->addColumn('date', function ($row) {
                    $date = $row->updated_at->format('d-m-Y : H:i:s');

                    return $date;
                })
                ->addColumn('action', function ($row) {
                    $actionBtn = '<select class="form-control-sm form-control reject-cropping bg-secondary text-light" name="reject_cropping" data-id="'.$row->product_id.'">
                                <option value="0">Reject Product</option>
                                <option value="Images Not Cropped Correctly">Images Not Cropped Correctly</option>
                                <option value="No Images Shown">No Images Shown</option>
                                <option value="Grid Not Shown">Grid Not Shown</option>
                                <option value="Blurry Image">Blurry Image</option>
                                <option value="First Image Not Available">First Image Not Available</option>
                                <option value="Dimension Not Available">Dimension Not Available</option>
                                <option value="Wrong Grid Showing For Category">Wrong Grid Showing For Category</option>
                                <option value="Incorrect Category">Incorrect Category</option>
                                <option value="Only One Image Available">Only One Image Available</option>
                                <option value="Image incorrect">Image incorrect</option>
                        </select>';

                    $response = $row->httpRequestData ? $row->httpRequestData->response : 'N/A';
                    $requestData = $row->httpRequestData ? $row->httpRequestData->requestData : 'N/A';

                    $actionBtn .= '<button 
                    style="float:right;padding-right:0px;" 
                    type="button" 
                    class="btn btn-xs show-http-status" 
                    title="Http Status" 
                    data-toggle="modal" data-target="#show-http-status"
                    data-request="'.$response.'"
                    data-response="'.$requestData.'"
                    >
                    <i class="fa fa-info-circle"></i>
                </button>';

                    return $actionBtn;
                })
                ->addColumn('issue', function ($row) {
                    $issue = $row->getProductIssueStatus($row->id);

                    return $issue;
                })
                ->rawColumns(['id', 'product_id', 'product_category_title', 'product_supplier', 'product_brands_name', 'store_website', 'image_score', 'original_image', 'cropped_image', 'speed', 'date', 'action', 'issue'])
                ->make(true);
        }

        Log::info('crop_reference_grid_page_pending_product_start: '.$this->dateFormatYmdHis);
        $pendingProduct = Product::where('status_id', StatusHelper::$autoCrop)->where('stock', '>=', 1)->count();
        Log::info('crop_reference_grid_page_pending_product_end: '.$this->dateFormatYmdHis);

        Log::info('crop_reference_grid_page_pending_category_product_start: '.$this->dateFormatYmdHis);
        $pendingCategoryProduct = Product::where('status_id', StatusHelper::$attributeRejectCategory)->where('stock', '>=', 1)->count();
        Log::info('crop_reference_grid_page_pending_category_product_end: '.$this->dateFormatYmdHis);

        $datatableModel = DataTableColumn::select('column_name')->where('user_id', auth()->user()->id)->where('section_name', 'crop-references-grid')->first();

        $dynamicColumnsToShowCrop = [];
        if (! empty($datatableModel->column_name)) {
            $hideColumns = $datatableModel->column_name ?? '';
            $dynamicColumnsToShowCrop = json_decode($hideColumns, true);
        }
        $quick_task_users = User::all();

        return view('image_references.grid2', compact('pendingProduct', 'pendingCategoryProduct', 'dynamicColumnsToShowCrop', 'quick_task_users'));
    }

    public function cropStats(Request $request): JsonResponse
    {
        Log::info('crop_reference_grid_page_customer_range_start: '.$this->dateFormatYmdHis);
        if (request('customer_range') != null) {
            $dateArray = explode('-', request('customer_range'));
            $startDate = trim($dateArray[0]);
            $endDate = trim(end($dateArray));
            if ($startDate == '1995/12/25') {
                $totalCounts = CroppedImageReference::where('created_at', '>=', \Carbon\Carbon::now()->subHour())->count();
            } elseif ($startDate == $endDate) {
                $totalCounts = CroppedImageReference::whereDate('created_at', '=', end($dateArray))->count();
            } else {
                $totalCounts = CroppedImageReference::whereBetween('created_at', [$startDate, $endDate])->count();
            }

            if ($request->ajax()) {
                return response()->json([
                    'count' => $totalCounts,
                ], 200);
            }
        }

        Log::info('crop_reference_grid_page_customer_range_end: '.$this->dateFormatYmdHis);
    }

    public function rejectCropImage(Request $request)
    {
        $reference = CroppedImageReference::find($request->id);
        $product = Product::find($reference->product_id);
        dd($product);
    }

    public function getCategories(Request $request): JsonResponse
    {
        $category_selection = Category::attr(['text' => 'Category', 'name' => 'category[]', 'class' => 'form-control select-multiple2', 'id' => 'category'])
            ->renderAsArray();
        $answer = $this->setByParent($category_selection);

        return response()->json(['result' => $answer]);
    }

    public function getProductIds(Request $request): JsonResponse
    {
        $response = Product::select('id')->get();

        return response()->json(['result' => $response]);
    }

    public function getBrands(Request $request): JsonResponse
    {
        $response = Brand::select(['id', 'name as text'])->get()->toArray();

        return response()->json(['result' => [['text' => 'Brands', 'children' => $response]]]);
    }

    public function getSupplier(Request $request): JsonResponse
    {
        $response = Supplier::select(['id', 'supplier as text'])->get();

        return response()->json(['result' => [['text' => 'Suppliers', 'children' => $response]]]);
    }

    private function setByParent($data, $step = 0, &$result = [])
    {
        $nbsp = '';
        if ($step) {
            for ($i = 0; $i < $step * 2; $i++) {
                $nbsp .= '&nbsp;';
            }
        }

        foreach ($data as $value) {
            $result[] = [
                'id' => $value['id'],
                'text' => $nbsp.$value['title'],
            ];
            if (! empty($value['child'])) {
                $this->setByParent($value['child'], $step + 1, $result);
            }
        }

        return $result;
    }

    public function manageInstance(Request $request): View
    {
        $instances = CroppingInstance::all();

        return view('image_references.partials.manage-instance', compact('instances'));
    }

    public function loginstance(Request $request)
    {
        $url = 'https://173.212.203.50:100/get-logs';
        $date = $request->date;
        $id = $request->id;
        $startTime = date('Y-m-d H:i:s', LARAVEL_START);
        $data = ['instance_id' => $id, 'date' => $date];
        $data = json_encode($data);
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json', 'accept: application/json']);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        $result = curl_exec($ch);
        $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        LogRequest::log($startTime, $url, 'POST', json_encode($data), $result, $httpcode, Commands\CroppedImageReferenceController::class, 'loginstance');
        echo $result;
    }

    public function addInstance(Request $request): View
    {
        $params = $request->except('_token');
        CroppingInstance::create($params);

        $instances = CroppingInstance::all();

        return view('image_references.partials.manage-instance', compact('instances'));
    }

    public function deleteInstance(Request $request): View
    {
        CroppingInstance::find($request->id)->delete();
        $instances = CroppingInstance::all();

        return view('image_references.partials.manage-instance', compact('instances'));
    }

    public function startInstance(Request $request): JsonResponse
    {
        $instance = CroppingInstance::find($request->id);
        if ($instance) {
            $client = new Client;
            $response = $client->request('POST', config('constants.py_crop_script').'/start', [
                'form_params' => [
                    'instanceId' => $instance->instance_id,
                ],
            ]);

            return response()->json(['code' => 200, 'message' => (string) $response->getBody()->getContents()]);
        } else {
            return response()->json(['code' => 500, 'message' => 'No instance id found']);
        }
    }

    public function stopInstance(Request $request): JsonResponse
    {
        $instance = CroppingInstance::find($request->id);
        if ($instance) {
            $client = new Client;
            $response = $client->request('POST', config('constants.py_crop_script').'/stop', [
                'form_params' => [
                    'instanceId' => $instance->instance_id,
                ],
            ]);

            return response()->json(['code' => 200, 'message' => (string) $response->getBody()->getContents()]);
        } else {
            return response()->json(['code' => 500, 'message' => 'No instance id found']);
        }
    }

    public function cropColumnVisbilityUpdate(Request $request): RedirectResponse
    {
        $userCheck = DataTableColumn::where('user_id', auth()->user()->id)->where('section_name', 'crop-references-grid')->first();

        if ($userCheck) {
            $column = DataTableColumn::find($userCheck->id);
            $column->section_name = 'crop-references-grid';
            $column->column_name = json_encode($request->column_crop);
            $column->save();
        } else {
            $column = new DataTableColumn;
            $column->section_name = 'crop-references-grid';
            $column->column_name = json_encode($request->column_crop);
            $column->user_id = auth()->user()->id;
            $column->save();
        }

        return redirect()->back()->with('success', 'column visiblity Added Successfully!');
    }

    public function cropReferencesLogs(Request $request): View
    {
        $title = 'Crop Image Logs';

        $CropImageGetRequest = CropImageGetRequest::orderByDesc('id');

        $CropImageGetRequest = $CropImageGetRequest->paginate(25);

        return view('image_references.index-crop-image-logs', compact('title', 'CropImageGetRequest'))->with('i', ($request->input('page', 1) - 1) * 10);
    }
}
