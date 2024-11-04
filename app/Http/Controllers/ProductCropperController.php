<?php

namespace App\Http\Controllers;

use App\Brand;
use App\Category;
use App\Colors;
use App\CropAmends;
use App\CroppedImageReference;
use App\Helpers\QueryHelper;
use App\Helpers\StatusHelper;
use App\Http\Requests\AmmendCropProductCropperRequest;
use App\Http\Requests\SaveAmendsProductCropperRequest;
use App\Image;
use App\ListingHistory;
use App\Mediables;
use App\Product;
use App\ProductStatus;
use App\ProductStatusHistory;
use App\ProductSupplier;
use App\Setting;
use App\Sizes;
use App\Stage;
use App\Supplier;
use App\User;
use App\UserProductFeedback;
use Carbon\Carbon;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\View\View;
use Plank\Mediable\Facades\MediaUploader as MediaUploader;
use Plank\Mediable\Media;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class ProductCropperController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Stage $stage): View
    {
        $products = Product::latest()
            ->where('stock', '>=', 1)
            ->where('stage', '>=', $stage->get('Supervisor'))
            ->whereNull('dnf')
            ->withMedia(config('constants.media_tags'))
            ->select(['id', 'sku', 'size', 'price_inr_special', 'brand', 'supplier', 'isApproved', 'stage', 'status', 'is_scraped', 'created_at'])
            ->paginate(Setting::get('pagination'));

        $roletype = 'ImageCropper';

        $category_selection = Category::attr(['name' => 'category[]', 'class' => 'form-control select-multiple'])
            ->selected(1)
            ->renderAsDropdown();

        $attach_image_tag = config('constants.attach_image_tag');
        $pending_products_count = Product::getPendingProductsCount($roletype);
        $brands = Brand::getAll();
        $colors = (new Colors)->all();

        $suppliers = Supplier::getProductSuppliers();

        return view('partials.grid', compact('products', 'roletype', 'category_selection', 'attach_image_tag', 'pending_products_count', 'brands', 'colors', 'suppliers', 'stage'))
            ->with('i', (request()->input('page', 1) - 1) * 10);
    }

    public function edit(Sizes $sizes, Product $productimagecropper)
    {
        if ($productimagecropper->isUploaded == 1) {
            return redirect()->route('products.show', $productimagecropper->id);
        }

        $data = [];

        $data['dnf'] = $productimagecropper->dnf;
        $data['id'] = $productimagecropper->id;
        $data['name'] = $productimagecropper->name;
        $data['short_description'] = $productimagecropper->short_description;
        $data['sku'] = $productimagecropper->sku;
        $data['description_link'] = $productimagecropper->description_link;
        $data['location'] = $productimagecropper->location;
        $data['product_link'] = $productimagecropper->product_link;

        $data['measurement_size_type'] = $productimagecropper->measurement_size_type;
        $data['lmeasurement'] = $productimagecropper->lmeasurement;
        $data['hmeasurement'] = $productimagecropper->hmeasurement;
        $data['dmeasurement'] = $productimagecropper->dmeasurement;

        $data['size_value'] = $productimagecropper->size_value;
        $data['sizes_array'] = $sizes->all();

        $data['size'] = $productimagecropper->size;

        $data['composition'] = $productimagecropper->composition;
        $data['made_in'] = $productimagecropper->made_in;
        $data['brand'] = $productimagecropper->brand;
        $data['color'] = $productimagecropper->color;
        $data['price'] = $productimagecropper->price;

        $data['isApproved'] = $productimagecropper->isApproved;
        $data['isUploaded'] = $productimagecropper->isUploaded;
        $data['isFinal'] = $productimagecropper->isFinal;
        $data['rejected_note'] = $productimagecropper->rejected_note;

        $data['images'] = $productimagecropper->getMedia(config('constants.media_tags'));

        $data['category'] = Category::attr(['name' => 'category', 'class' => 'form-control', 'disabled' => 'disabled'])
            ->selected($productimagecropper->category)
            ->renderAsDropdown();

        return view('imagecropper.edit', $data);
    }

    public function update(Request $request, Guard $auth, Product $productimagecropper, Stage $stage): RedirectResponse
    {
        $productimagecropper->stage = $stage->get('ImageCropper');

        $validations = [];

        //:-( ahead
        $check_image = 0;
        $images = $productimagecropper->getMedia(config('constants.media_tags'));
        $images_no = count($images);

        for ($i = 0; $i < 5; $i++) {
            if ($request->input('oldImage'.$i) != 0) {
                $validations['image.'.$i] = 'mimes:jpeg,bmp,png,jpg';

                if (empty($request->file('image.'.$i))) {
                    $check_image++;
                }
            }
        }

        $messages = [];
        if ($check_image == $images_no) {
            $validations['image'] = 'required';
            $messages['image.required'] = 'Atleast on image is required. Last image can not be removed';
        }
        //:-( over

        $this->validate($request, $validations);

        self::replaceImages($request, $productimagecropper);

        $productimagecropper->last_imagecropper = Auth::id();
        $productimagecropper->save();

        NotificaitonContoller::store('has searched', ['Listers'], $productimagecropper->id);
        ActivityConroller::create($productimagecropper->id, 'imagecropper', 'create');

        return redirect()->route('productimagecropper.index')
            ->with('success', 'ImageCropper updated successfully.');
    }

    public function replaceImages($request, $productattribute)
    {
        $delete_array = [];
        for ($i = 0; $i < 5; $i++) {
            if ($request->input('oldImage'.$i) != 0) {
                $delete_array[] = $request->input('oldImage'.$i);
            }

            if (! empty($request->file('image.'.$i))) {
                $media = MediaUploader::fromSource($request->file('image.'.$i))
                    ->toDirectory('product/'.floor($productattribute->id / config('constants.image_per_folder')))
                    ->upload();
                $productattribute->attachMedia($media, config('constants.media_tags'));
            }
        }

        $results = Media::whereIn('id', $delete_array)->get();
        $results->each(function ($media) {
            Image::trashImage($media->basename);
            $media->delete();
        });
    }

    public static function rejectedProductCountByUser()
    {
        return Product::where('last_imagecropper', Auth::id())
            ->where('isApproved', -1)
            ->count();
    }

    public function getListOfImagesToBeVerified(Request $request): View
    {
        $products = Product::where('status_id', StatusHelper::$cropApproval);
        $products = QueryHelper::approvedListingOrder($products);
        $products = $products->paginate(24);

        $totalApproved = 0;
        $totalRejected = 0;
        $totalSequenced = 0;

        if ($request->get('date') != '') {
            $date = $request->get('date');

            if (Auth::user()->hasRole('Crop Approval')) {
                $stats = UserProductFeedback::where('user_id')->whereIn('action', [
                    'CROP_APPROVAL_REJECTED',
                    'CROP_SEQUENCED_REJECTED',
                ])->get();
                $totalApproved = Product::where('crop_approved_by', Auth::id())->where('crop_approved_at', 'LIKE', "%$date%")->count();
                $totalRejected = Product::where('crop_rejected_by', Auth::id())->where('crop_rejected_at', 'LIKE', "%$date%")->count();
                $totalSequenced = Product::where('crop_rejected_by', Auth::id())->where('crop_rejected_at', 'LIKE', "%$date%")->count();
            } else {
                $stats = Product::selectRaw('SUM(is_image_processed) as cropped, COUNT(*) AS total, SUM(is_crop_approved) as approved, SUM(is_crop_rejected) AS rejected')
                    ->where('is_scraped', 1)
                    ->where('is_without_image', 0)
                    ->where('stock', '>=', (int) $request->stock)
                    ->first();
            }
        } else {
            if (Auth::user()->hasRole('Crop Approval')) {
                $stats = UserProductFeedback::where('user_id')->whereIn('action', [
                    'CROP_APPROVAL_REJECTED',
                    'CROP_SEQUENCED_REJECTED',
                ])->get();

                $totalApproved = Product::where('crop_approved_by', Auth::id());
                $totalApproved = QueryHelper::approvedListingOrder($totalApproved);
                $totalApproved = $totalApproved->count();

                $totalRejected = Product::where('crop_rejected_by', Auth::id());
                $totalRejected = QueryHelper::approvedListingOrder($totalRejected);
                $totalRejected = $totalRejected->count();

                $totalSequenced = Product::where('crop_rejected_by', Auth::id());
                $totalSequenced = QueryHelper::approvedListingOrder($totalSequenced);
                $totalSequenced = $totalSequenced->count();
            } else {
                $stats = new \stdClass;
                $stats->cropped = StatusHelper::getCroppedCount();
                $stats->total = StatusHelper::getTotalProductsScraped();
                $stats->approved = StatusHelper::getCropApprovedCount();
                $stats->rejected = StatusHelper::getCropRejectedCount();
            }
        }

        $rejectedCrops = Product::where('crop_rejected_by', Auth::user()->id)->where('is_crop_approved', 0)->where('is_crop_rejected', 0)->where('stock', '>=', 1)->paginate(20);

        return view('products.crop_list', compact('products', 'stats', 'totalRejected', 'totalSequenced', 'totalApproved', 'rejectedCrops'));
    }

    public function showImageToBeVerified($id, Request $request): View
    {
        $product = Product::find($id);
        $product->is_crop_being_verified = 1;
        $product->save();

        $secondProduct = Product::where('is_image_processed', 1)
            ->where('id', '!=', $id)
            ->where('is_crop_rejected', 0)
            ->where('is_crop_approved', 0)
            ->whereRaw('id NOT IN (SELECT product_id FROM crop_amends)')
            ->where('is_crop_being_verified', 0)
            ->orderByDesc('is_on_sale')
            ->first();

        $q = '';
        if ($request->get('rejected') === 'yes') {
            $q = 'rejected=yes';
        }

        $category = $product->category;
        $img = Category::getCroppingGridImageByCategoryId($category);

        $category_array = Category::renderAsArray();

        return view('products.crop', compact('q', 'product', 'secondProduct', 'img', 'category', 'category_array'));
    }

    public function getApprovedImages(Request $request): View
    {
        // Add check for out of stock
        $stock = $request->stock === 0 ? 0 : 1;

        // Get products which are crop approved
        $products = Product::where('status_id', StatusHelper::$cropSequencing)
            ->where('stock', '>=', $stock);

        // Limit to one user if this is requested
        if ($request->get('user_id') > 0) {
            $products = $products->where('crop_approved_by', $request->get('user_id'));
        }

        // Get images with cropApprover
        $products = $products->with('cropApprover')->paginate(25);

        // Get all users for dropdown
        $users = User::all();

        // Get requested user
        $userId = $request->get('user_id');

        return view('products.approved_crop_list', compact('products', 'users', 'userId'));
    }

    public function ammendCrop($id, AmmendCropProductCropperRequest $request, Stage $stage): RedirectResponse
    {

        $sizes = $request->get('size');
        $padding = $request->get('padding');
        $mediaIds = $request->get('mediaIds');

        foreach ($sizes as $key => $size) {
            if ($size != 'ok') {
                $rec = new CropAmends;
                //update mediaId
                $cropRefrence = CroppedImageReference::where('new_media_id', $mediaIds[$key])->first();
                if (! $cropRefrence) {
                    continue;
                }
                $rec->file_url = getMediaUrl($cropRefrence->media);
                $rec->settings = ['size' => $size, 'padding' => $padding[$key] ?? 96, 'media_id' => $cropRefrence->original_media_id];
                $rec->product_id = $id;
                $rec->save();

                Media::where('id', $mediaIds[$key])->delete();
            }
        }

        $secondProduct = Product::where('status_id', '=', StatusHelper::$cropApproval)
            ->where('id', '!=', $id)
            ->whereNotIn('id', CropAmends::pluck('product_id')->toArray())
            ->first();

        return redirect()->action([ProductCropperController::class, 'showImageToBeVerified'], $secondProduct->id)->with('message', 'Cropping approved successfully!');
    }

    /**
     * @SWG\Get(
     *   path="/crop/amends",
     *   tags={"Crop"},
     *   summary="Get Crop amends",
     *   operationId="crop-get-crop-amends",
     *
     *   @SWG\Response(response=200, description="successful operation"),
     *   @SWG\Response(response=406, description="not acceptable"),
     *   @SWG\Response(response=500, description="internal server error"),
     * )
     */
    public function giveAmends(): JsonResponse
    {
        $amend = CropAmends::where('status', 1)->first();

        return response()->json($amend);
    }

    /**
     * @SWG\Post(
     *   path="/crop/amends",
     *   tags={"Crop"},
     *   summary="Save Crop amends",
     *   operationId="crop-save-crop-amends",
     *
     *   @SWG\Response(response=200, description="successful operation"),
     *   @SWG\Response(response=406, description="not acceptable"),
     *   @SWG\Response(response=500, description="internal server error"),
     *
     *      @SWG\Parameter(
     *          name="file",
     *          in="formData",
     *          required=true,
     *          type="file"
     *      ),
     *      @SWG\Parameter(
     *          name="product_id",
     *          in="formData",
     *          required=true,
     *          type="string"
     *      ),
     *      @SWG\Parameter(
     *          name="media_id",
     *          in="formData",
     *          required=true,
     *          type="integer"
     *      ),
     *      @SWG\Parameter(
     *          name="amend_id",
     *          in="formData",
     *          required=true,
     *          type="integer"
     *      ),
     * )
     */
    public function saveAmends(SaveAmendsProductCropperRequest $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'product_id' => 'required',
            'amend_id' => 'required',
            'file' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'code' => 400,
                'errors' => $validator->errors(),
            ], 400);
        }

        $product = Product::findOrFail($request->get('product_id'));
        $product->is_crop_being_verified = 0;

        if ($request->hasFile('file')) {
            $image = $request->file('file');
            $media = MediaUploader::fromSource($image)
                ->toDirectory('product/'.floor($product->id / config('constants.image_per_folder')))
                ->upload();
            $product->attachMedia($media, config('constants.media_tags'));
        }

        $amend = CropAmends::findOrFail($request->get('amend_id'));
        $amend->delete();

        return response()->json([
            'status' => 'success',
        ]);
    }

    public function approveCrop($id, Request $request): RedirectResponse
    {
        $product = Product::findOrFail($id);
        $product->status_id = StatusHelper::$cropSequencing;
        $product->is_crop_approved = 1;
        $product->crop_approved_by = Auth::user()->id;
        $product->crop_approved_at = Carbon::now()->toDateTimeString();
        $product->is_crop_rejected = 0;
        $product->save();

        $e = new ListingHistory;
        $e->user_id = Auth::user()->id;
        $e->product_id = $product->id;
        $e->content = ['action' => 'CROP_APPROVAL', 'page' => 'Approved Listing Page'];
        $e->action = 'CROP_APPROVAL';
        $e->save();

        $secondProduct = null;

        if ($request->get('rejected') === 'yes') {
            $secondProduct = Product::where('crop_rejected_by', Auth::user()->id)->where('is_crop_approved', 0)->where('is_crop_rejected', 0)->first();
        }

        if (! $secondProduct) {
            $secondProduct = Product::where('is_image_processed', 1)
                ->where('id', '!=', $id)
                ->where('is_crop_rejected', 0)
                ->where('is_crop_approved', 0)
                ->where('is_crop_being_verified', 0)
                ->whereNotIn('id', CropAmends::pluck('product_id')->toArray())
                ->orderByDesc('is_on_sale')
                ->where(function ($q) {
                    $q->where('size', '!=', '')
                        ->orWhere(function ($qq) {
                            $qq->where('lmeasurement', '!=', '')
                                ->where('hmeasurement', '!=', '')
                                ->where('dmeasurement', '!=', '');
                        });
                })
                ->first();
        }

        if (! $secondProduct) {
            $secondProduct = Product::where('status_id', StatusHelper::$cropApproval);
            $secondProduct = QueryHelper::approvedListingOrder($secondProduct);
            $secondProduct = $secondProduct->first();
        }

        if (! $secondProduct || ! isset($secondProduct->id)) {
            return redirect()->action([ProductCropperController::class, 'getListOfImagesToBeVerified']);
        } else {
            return redirect()->action([ProductCropperController::class, 'showImageToBeVerified'], $secondProduct->id)->with('message', 'Cropping approved successfully!');
        }
    }

    public function cropApprovalConfirmation($id, Request $request)
    {
        // Get product
        $product = Product::findOrFail($id);

        // Insert crop approval confirmation
        ListingHistory::createNewListing(Auth::id(), $product->id, "[ 'action' => 'CROP_APPROVAL_CONFIRMATION', 'page' => 'Approved Listing Page' ]", 'CROP_APPROVAL_CONFIRMATION');

        // Add new status
        ProductStatus::updateStatus($product->id, 'CROP_APPROVAL_CONFIRMATION', 1);

        if ($product) {
            //sets initial status pending for finalApproval in product status histroy
            $data = [
                'product_id' => $product->id,
                'old_status' => $product->status_id,
                'new_status' => StatusHelper::$finalApproval,
                'pending_status' => 1,
                'created_at' => date('Y-m-d H:i:s'),
            ];
            ProductStatusHistory::addStatusToProduct($data);
        }

        // Set new status
        //check final approval
        if ($product->checkPriceRange()) {
            $product->status_id = StatusHelper::$finalApproval;
        } else {
            $product->status_id = StatusHelper::$priceCheck;
        }
        //$product->status_id = StatusHelper::$finalApproval;
        $product->save();

        return 'ok';
    }

    public function rejectCrop($id, Stage $stage, Request $request)
    {
        // Get product
        $product = Product::findOrFail($id);

        if ($product->status_id == StatusHelper::$cropRejected) {
            if ($request->ajax()) {
                return response()->json(['sucesss'], 200);
            }
        }

        if ($product->status_id == StatusHelper::$manualImageUpload) {
            if ($request->ajax()) {
                return response()->json(['sucesss'], 200);
            }
        }

        // Get last image cropper
        $lastImageCropper = $product->crop_approved_by;

        // Update product to status rejected
        if ($request->get('remark') == 'Image incorrect') {
            $product->status_id = StatusHelper::$manualImageUpload;
        } else {
            $product->status_id = StatusHelper::$cropRejected;
        }

        $product->is_crop_rejected = 1;
        $product->crop_remark = $request->get('remark');
        $product->crop_rejected_by = Auth::user()->id;
        $product->is_approved = 0;
        $product->is_crop_approved = 0;
        $product->is_crop_ordered = 0;
        $product->is_crop_being_verified = 0;
        $product->crop_rejected_at = Carbon::now()->toDateTimeString();
        $product->save();

        // Log crop approval denied
        if ((int) $lastImageCropper > 0) {
            $e = new ListingHistory;
            $e->user_id = $lastImageCropper;
            $e->product_id = $product->id;
            $e->content = ['action' => 'CROP_APPROVAL_DENIED', 'page' => 'Approved Listing Page'];
            $e->action = 'CROP_APPROVAL_DENIED';
            $e->save();
        }

        // Log crop rejected
        $e = new ListingHistory;
        $e->user_id = Auth::user()->id;
        $e->product_id = $product->id;
        $e->content = ['action' => 'CROP_REJECTED', 'page' => 'Approved Listing Page'];
        $e->action = 'CROP_REJECTED';
        $e->save();

        if ($request->get('senior') && $product) {
            $s = new UserProductFeedback;
            $s->user_id = $product->crop_approved_by;
            $s->senior_user_id = Auth::user()->id;
            $s->action = 'CROP_APPROVAL_REJECTED';
            $s->content = ['action' => 'CROP_APPROVAL_REJECTED', 'previous_action' => 'CROP_APPROVAL', 'current_action' => 'CROP_REJECTED', 'message' => 'Your cropping approval has been rejected.'];
            $s->message = 'Your cropping approval has been rejected. The reason was: '.$request->get('remark');
            $s->product_id = $product->id;
            $s->save();
        }

        if ($request->isXmlHttpRequest()) {
            return response()->json([
                'status' => 'success',
            ]);
        }

        $secondProduct = null;

        if ($request->get('rejected') === 'yes') {
            $secondProduct = Product::where('crop_rejected_by', Auth::user()->id)->where('is_crop_approved', 0)->where('is_crop_rejected', 0)->first();
        }

        if (! $secondProduct) {
            $secondProduct = Product::where('status_id', StatusHelper::$cropApproval);
            $secondProduct = QueryHelper::approvedListingOrder($secondProduct);
            $secondProduct = $secondProduct->first();
        }

        if (! $secondProduct) {
            return redirect()->action([ProductCropperController::class, 'getListOfImagesToBeVerified']);
        }

        return redirect()->action([ProductCropperController::class, 'showImageToBeVerified'], $secondProduct->id)->with('message', 'Cropping rejected!');
    }

    public function showRejectedCrops(Request $request): View
    {
        $products = Product::where('is_crop_rejected', 1);
        $reason = '';
        $supplier = [];
        $selected_categories = [];

        if ($request->get('reason') !== '') {
            $reason = $request->get('reason');
            $products = $products->where('stock', '>=', 1)->where(function ($query) use ($reason) {
                $query = $query->where('crop_remark', 'LIKE', "%$reason%")
                    ->orWhere('id', 'LIKE', "%$reason%")
                    ->orWhere('sku', 'LIKE', "%$reason%");
            });
        }

        if ($request->get('user_id')) {
            $products = $products->where('crop_rejected_by', $request->get('user_id'));
        }

        // $suppliers = DB::select('
        // 		SELECT id, supplier
        // 		FROM suppliers

        // 		INNER JOIN (
        // 			SELECT supplier_id FROM product_suppliers GROUP BY supplier_id
        // 			) as product_suppliers
        // 		ON suppliers.id = product_suppliers.supplier_id
        // ');
        $suppliers = Supplier::select('suppliers.id', 'suppliers.supplier')
            ->join(DB::raw('(SELECT supplier_id FROM product_suppliers GROUP BY supplier_id) AS product_suppliers'), function ($join) {
                $join->on('suppliers.id', '=', 'product_suppliers.supplier_id');
            })
            ->get();

        if ($request->supplier[0] != null) {
            $supplier = $request->get('supplier');
            $products = $products->whereIn('id', ProductSupplier::whereIn('supplier_id', $supplier)->pluck('product_id'));
        }

        $users = User::all();

        if ($request->category[0] != null && $request->category[0] != 1) {
            $category_children = [];
            foreach ($request->category as $category) {
                $is_parent = Category::isParent($category);

                if ($is_parent) {
                    $childs = Category::find($category)->childs()->get();

                    foreach ($childs as $child) {
                        $is_parent = Category::isParent($child->id);

                        if ($is_parent) {
                            $children = Category::find($child->id)->childs()->get();

                            foreach ($children as $chili) {
                                array_push($category_children, $chili->id);
                            }
                        } else {
                            array_push($category_children, $child->id);
                        }
                    }
                } else {
                    array_push($category_children, $category);
                }
            }
            $products = $products->whereIn('category', $category_children);
            $selected_categories = [$request->get('category')[0]];
        }

        if (! empty($request->brand)) {
            $products = $products->whereIn('brand', $request->brand);
        }

        if (! empty($request->color)) {
            $products = $products->whereIn('color', $request->color);
        }

        if (! empty($request->size)) {
            $products = $products->whereNotNull('size')->where(function ($query) use ($request) {
                $query->where('size', $request->size)->orWhere('size', 'LIKE', "%$request->size,")->orWhere('size', 'LIKE', "%,$request->size,%");
            });
        }

        if (! empty($request->location)) {
            $products = $products->whereIn('location', $request->location);
        }

        $products = $products->orderByDesc('updated_at')->paginate(24);

        $category_array = Category::attr(['name' => 'category[]', 'class' => 'form-control select2', 'placeholder' => 'Select Category'])->selected(request()->get('category', 1))->renderAsDropdown();
        $media_tags = config('constants.media_tags');

        return view('products.rejected_crop_list', compact('products', 'suppliers', 'supplier', 'reason', 'selected_categories', 'category_array', 'users', 'media_tags'));
    }

    public function cropIssuesPage(): View
    {
        $issues = Product::selectRaw('DISTINCT(crop_remark) as remark, COUNT(crop_remark) as issue_count')->where('stock', '>=', 1)->where('is_crop_rejected', 1)->groupBy('crop_remark')->orderByDesc('issue_count')->get();

        return view('products.crop_issue_summary', compact('issues'));
    }

    public function showRejectedImageToBeverified($id): View
    {
        $product = Product::find($id);
        $secondProduct = Product::where('id', '!=', $id)->where('is_crop_rejected', 1)->first();

        $category = $product->category;
        $img = Category::getCroppingGridImageByCategoryId($category);

        $medias = $product->getMedia(config('constants.media_tags'));
        $originalMediaCount = 0;

        foreach ($medias as $media) {
            if (stripos(strtoupper($media->filename), 'CROPPED') === false) {
                $originalMediaCount++;
            }
        }

        return view('products.rejected_crop', compact('product', 'secondProduct', 'img', 'originalMediaCount'));
    }

    public function downloadImagesForProducts($id, $type): BinaryFileResponse
    {
        $product = Product::findOrFail($id);

        $medias = $product->getMedia(config('constants.media_tags'));
        $zip_file = md5(time()).'.zip';
        $zip = new \ZipArchive;
        $zip->open($zip_file, \ZipArchive::CREATE);
        foreach ($medias as $media) {
            $fileName = $media->getAbsolutePath();
            if ($type === 'cropped' && stripos(strtoupper($media->filename), 'CROPPED') !== false) {
                $zip->addFile($fileName, $media->filename.'.'.$media->extension);
            }
            if ($type === 'original' && stripos(strtoupper($media->filename), 'CROPPED') === false) {
                $zip->addFile($fileName, $media->filename.'.'.$media->extension);
            }
        }

        $zip->close();

        return response()->download($zip_file);
    }

    public function approveRejectedCropped($id, Request $request): RedirectResponse
    {
        $product = Product::find($id);

        $action = 'MARK_NOT_CROPPED';
        if ($request->get('action') == 'uncropped') {
            $product->status_id = 4;
            $product->save();
        } else {
            if ($request->get('action') == 'approved') {
                $product->status_id = 6;
                $product->crop_approved_by = Auth::id();
                $product->crop_approved_at = Carbon::now()->toDateTimeString();
                $product->save();
                $action = 'CROP_APPROVAL';
            } else {
                if ($request->get('action') == 'manual') {
                    $product->status_id = 21;
                    $product->save();
                    $action = 'SENT_FOR_MANUAL_CROPPING';
                } else {
                    if ($request->get('unreject')) {
                        $product->status_id = 5;
                        $product->save();
                        $action = 'RESENT_FOR_APPROVAL';
                    }
                }
            }
        }

        $l = new ListingHistory;
        $l->action = $action;
        $l->content = ['action' => $action, 'message' => ''];
        $l->user_id = Auth::user()->id;
        $l->product_id = $product->id;
        $l->save();

        $secondProduct = Product::where('id', '!=', $id)->where('status_id', StatusHelper::$cropRejected)->first();

        return redirect()->action([ProductCropperController::class, 'showRejectedImageToBeverified'], $secondProduct->id)->with('message', 'Rejected image approved and has been moved to approval grid.');
    }

    public function updateCroppedImages(Request $request)
    {
        dd($request->all());
    }

    public function giveImagesToBeAmended(): JsonResponse
    {
        $image = CropAmends::where('status', 1)->first();

        return response()->json($image);
    }

    public function showCropOrderRejectedList()
    {
        Product::where('is_order_rejected', 1)->orderByDesc('updated_at')->paginate(24);
    }

    public function showCropVerifiedForOrdering(): View
    {
        // Set initial product
        $product = Product::where('status_id', StatusHelper::$cropSequencing);

        // Add queryhelper
        $product = QueryHelper::approvedListingOrder($product);

        // Get first
        $product = $product->first();

        // No products found
        if ($product == null) {
            exit('No products found');
        }

        // Get total number of products awaiting for sequencing
        $total = Product::where('status_id', StatusHelper::$cropSequencing)->count();

        // Update the status so this product will not show up
        $product->status_id = StatusHelper::$isBeingSequenced;
        $product->save();

        // Set count of crops ordered by the current logged in user
        $count = Product::where('crop_ordered_by', Auth::id())->count();
        $media_tags = config('constants.media_tags');

        // Return view
        return view('products.sequence', compact('product', 'total', 'count', 'media_tags'));
    }

    public function skipSequence($id, Request $request)
    {
        // Find product or fail
        $product = Product::findOrFail($id);

        // Check if the product is being sequenced
        if ($product->status_id != StatusHelper::$isBeingSequenced) {
            // Check for ajax
            if ($request->isXmlHttpRequest()) {
                return response()->json([
                    'status' => 'failed',
                ], 400);
            } else {
                // Redirect
                return redirect()->action([ProductCropperController::class, 'showCropVerifiedForOrdering']);
            }
        }

        $product->status_id = StatusHelper::$cropSkipped;
        $product->crop_rejected_at = Carbon::now()->toDateTimeString();
        $product->crop_rejected_by = $request->isXmlHttpRequest() ? 109 : Auth::id();
        $product->save();

        // Store listing history
        $listingHistory = new ListingHistory;
        $listingHistory->action = 'SKIP_SEQUENCE';
        $listingHistory->product_id = $product->id;
        $listingHistory->user_id = Auth::user()->id;
        $listingHistory->content = ['action' => 'SKIP_SEQUENCE', 'page' => 'Sequence Approver'];
        $listingHistory->save();

        // Return JSON if the request is ajax
        if ($request->isXmlHttpRequest()) {
            return response()->json([
                'status' => 'success',
            ]);
        }

        // Redirect
        return redirect()->action([ProductCropperController::class, 'showCropVerifiedForOrdering']);
    }

    public function rejectSequence($id, Request $request): JsonResponse
    {
        $product = Product::findOrFail($id);
        $product->is_crop_ordered = 0;
        $product->is_order_rejected = 1;
        $product->is_approved = 0;
        $product->save();

        $l = new ListingHistory;
        $l->action = 'REJECT_SEQUENCE';
        $l->product_id = $product->id;
        $l->user_id = Auth::user()->id;
        $l->content = ['action' => 'REJECT_SEQUENCE', 'page' => 'Approved Listing'];
        $l->save();

        if ($request->get('senior') && $product) {
            $s = new UserProductFeedback;
            $s->user_id = $product->crop_ordered_by;
            $s->senior_user_id = Auth::user()->id;
            $s->action = 'CROP_SEQUENCED_REJECTED';
            $s->content = ['action' => 'CROP_SEQUENCED_REJECTED', 'previous_action' => 'CROP_SEQUENCED', 'current_action' => 'CROP_SEQUENCED_REJECTED', 'message' => 'Your sequencing has been rejected.'];
            $s->message = 'Your crop sequence was not proper. Please check for this one';
            $s->product_id = $product->id;
            $s->save();
        }

        return response()->json([
            'status' => 'success',
        ]);
    }

    public function saveSequence($id, Request $request): RedirectResponse
    {
        // Find product or fail
        $product = Product::findOrFail($id);

        // Is this product currently being sequenced
        if ($product->status_id != StatusHelper::$isBeingSequenced) {
            // Redirect
            return redirect()->action([ProductCropperController::class, 'showCropVerifiedForOrdering']);
        }

        $medias = $request->get('images');
        foreach ($medias as $mediaId => $order) {
            if ($order !== null) {

                Mediables::where('media_id', $mediaId)->where('mediable_type', Product::class)->update([
                    'order' => $order,
                ]);
            } else {
                Mediables::where('media_id', $mediaId)->where('mediable_type', product::class)->delete();
                Media::where('id', $mediaId)->delete();

            }
        }

        // Update product
        $product->status_id = StatusHelper::$imageEnhancement;
        $product->crop_ordered_by = Auth::user()->id;
        $product->crop_ordered_at = Carbon::now()->toDateTimeString();
        $product->save();

        $listingHistory = new ListingHistory;
        $listingHistory->action = 'CROP_SEQUENCED';
        $listingHistory->user_id = Auth::user()->id;
        $listingHistory->product_id = $product->id;
        $listingHistory->content = ['action' => 'CROP_SEQUENCED', 'page' => 'Crop Sequencer'];
        $listingHistory->save();

        return redirect()->action([ProductCropperController::class, 'showCropVerifiedForOrdering'])->with('message', 'Previous image ordered successfully!');
    }
}
