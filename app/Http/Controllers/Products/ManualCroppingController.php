<?php

namespace App\Http\Controllers\Products;

use App\Http\Controllers\Controller;
use App\Http\Requests\Products\UpdateManualCroppingRequest;
use App\ListingHistory;
use App\Models\UserManualCrop;
use App\Product;
use App\ScrapedProducts;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\View\View;
use Plank\Mediable\Facades\MediaUploader as MediaUploader;

class ManualCroppingController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        $products = Product::where('manual_crop', 1)
            ->where('stock', '>=', 1)
            ->where('is_crop_approved', 0)
            ->where('is_manual_cropped', 0)
            ->whereIn('id', UserManualCrop::where('user_id', Auth::id())->pluck('product_id')->toArray())
            ->get();

        $media_tags = config('constants.media_tags');

        return view('products.crop.manual.index', compact('products', 'media_tags'));
    }

    public function assignProductsToUser(): RedirectResponse
    {
        $currentUser = Auth::user();

        $reservedProductIds = UserManualCrop::pluck('product_id')->toArray();
        $products = Product::whereNotIn('id', $reservedProductIds)
            ->where('manual_crop', 1)
            ->where('is_crop-approved', 0)
            ->where('is_manual_cropped', 0)
            ->take(25)
            ->get();

        if ($products->count() === 0) {
            return redirect()->back()->with('message', 'There are no products to be assigned!');
        }

        $currentUser->manualCropProducts()->attach($products);

        return redirect()->back()->with('message', $products->count().' new products assigned successfully!');
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
     *
     * @return \Illuminate\Http\Response
     */
    public function show(int $id)
    {
        $product = Product::find($id);

        if (! $product) {
            return redirect()->action([ManualCroppingController::class, 'index'])->with('message', 'The product you were trying to open does not exist anymore.');
        }

        $originalMediaCount = 0;

        $medias = $product->getMedia(config('constants.media_tags'));
        foreach ($medias as $media) {
            if (stripos(strtoupper($media->filename), 'CROPPED') === false) {
                $originalMediaCount++;
            }
        }

        $references = ScrapedProducts::where('sku', $product->sku)->pluck('url', 'website');

        return view('products.crop.manual.show', compact('product', 'references', 'originalMediaCount'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     *
     * @return \Illuminate\Http\Response
     */
    public function edit(int $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateManualCroppingRequest $request, int $id): RedirectResponse
    {

        $product = Product::find($id);
        $files = $request->allFiles();

        if ($files !== []) {
            $this->deleteCroppedImages($product);
            foreach ($files['images'] as $file) {
                $media = MediaUploader::fromSource($file)
                    ->useFilename(uniqid('cropped_', true))
                    ->toDirectory('product/'.floor($product->id / config('constants.image_per_folder')))
                    ->upload();
                $product->attachMedia($media, config('constants.media_tags'));
            }
        }

        $product->is_crop_rejected = 0;
        $product->cropped_at = Carbon::now()->toDateTimeString();
        $product->manual_cropped_at = Carbon::now()->toDateTimeString();
        $product->is_image_processed = 1;
        $product->is_manual_cropped = 1;
        $product->manual_crop = 1;
        $product->manual_cropped_by = Auth::id();
        $product->save();

        $e = new ListingHistory;
        $e->user_id = Auth::user()->id;
        $e->product_id = $product->id;
        $e->content = ['action' => 'MANUAL_CROPPED', 'page' => 'Manual Crop Page'];
        $e->action = 'MANUAL_CROPPED';
        $e->save();

        $product = Product::where('manual_crop', 1)
            ->where('is_crop_approved', 0)
            ->where('is_manual_cropped', 0)
            ->whereIn('id', UserManualCrop::where('user_id', Auth::id())->pluck('product_id')->toArray())
            ->first();

        if (! $product) {
            return redirect()->action([ManualCroppingController::class, 'index'])->with('message', 'There are no assigned products available for cropping anymore.');
        }

        return redirect()->action([ManualCroppingController::class, 'show'], $product->id)->with('message', 'The previous product has been sent for approval!');
    }

    private function deleteCroppedImages($product)
    {
        if ($product->hasMedia(config('constants.media_tags'))) {
            foreach ($product->getMedia(config('constants.media_tags')) as $image) {
                if (stripos(strtoupper($image->filename), 'CROPPED') !== false) {
                    $image_path = $image->getAbsolutePath();

                    if (File::exists($image_path)) {
                        try {
                            File::delete($image_path);
                        } catch (Exception $exception) {
                            return response()->json(['code' => 400, 'message' => 'Error deleting file:'.$exception->getMessage()]);
                        }
                    }

                    $image->delete();
                }
            }

            $product->is_image_processed = 1;
            $product->save();
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy(int $id)
    {
        //
    }
}
