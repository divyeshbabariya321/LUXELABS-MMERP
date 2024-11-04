<?php

namespace App\Http\Controllers\Products;

use App\Helpers\QueryHelper;
use App\Helpers\StatusHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\Products\StoreProductEnhancementRequest;
use App\Product;
use App\ProductStatusHistory;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Plank\Mediable\Facades\MediaUploader as MediaUploader;

class ProductEnhancementController extends Controller
{
    /**
     * @SWG\Get(
     *   path="/products/enhance",
     *   tags={"Products"},
     *   summary="get product enhance where product status is imageEnhancement",
     *   operationId="get-product-enhance",
     *
     *   @SWG\Response(response=200, description="successful operation"),
     *   @SWG\Response(response=406, description="not acceptable"),
     *   @SWG\Response(response=500, description="internal server error"),
     * )
     */
    public function index(): JsonResponse
    {
        // Get next product to be enhances
        $product = Product::where('status_id', StatusHelper::$imageEnhancement)
            ->where('stock', '>=', 1)
            ->whereRaw("(SELECT COUNT(media_id) FROM mediables WHERE mediables.mediable_id=products.id AND mediables.mediable_type LIKE '%Product') > 0");

        // Add query helper filter
        $product = QueryHelper::approvedListingOrder($product);

        // Get first product
        $product = $product->first();

        // Do we have a result
        if ($product == null) {
            return response()->json([
                'error' => 'No images to enhance',
            ], 400);
        }

        // Create array for product images
        $productImages = $product->media()->get();

        // Set empty array for product image URLs
        $productImageUrls = [];

        // Loop over images to get image URLs
        foreach ($productImages as $image) {
            $productImageUrls[] = getMediaUrl($image);
        }

        // Set status to being enhanced
        $product->status_id = StatusHelper::$isBeingEnhanced;
        $product->save();

        return response()->json([
            'id' => $product->id,
            'images' => $productImageUrls,
        ]);
    }

    /**
     * @SWG\Post(
     *   path="/products/enhance",
     *   tags={"Products"},
     *   summary="post product enhance",
     *   operationId="post-product-enhance",
     *
     *   @SWG\Response(response=200, description="successful operation"),
     *   @SWG\Response(response=406, description="not acceptable"),
     *   @SWG\Response(response=500, description="internal server error"),
     *
     *      @SWG\Parameter(
     *          name="images[]",
     *          in="formData",
     *          required=true,
     *          type="file"
     *      ),
     *      @SWG\Parameter(
     *          name="id",
     *          in="formData",
     *          required=true,
     *          type="integer"
     *      ),
     * )
     */
    public function store(StoreProductEnhancementRequest $request): JsonResponse
    {
        // Vaidate the request

        // Find product
        $product = Product::find($request->get('id'));

        // No product found
        if ($product == null) {
            Log::channel('productUpdates')->debug('Product '.$product->id.' not found');

            return response()->json([
                'error' => 'Product is not found',
            ], 400);
        }

        //sets initial status pending for finalApproval in product status histroy
        $data = [
            'product_id' => $product->id,
            'old_status' => $product->status_id,
            'new_status' => StatusHelper::$finalApproval,
            'pending_status' => 1,
            'created_at' => date('Y-m-d H:i:s'),
        ];
        ProductStatusHistory::addStatusToProduct($data);

        // Check if product is being enhanced
        if ($product->status_id != StatusHelper::$isBeingEnhanced) {
            Log::channel('productUpdates')->debug('Received enhanced files for '.$product->id.' but the status is not '.StatusHelper::$isBeingEnhanced.' but '.$product->status_id);

            return response()->json([
                'error' => 'Product is not being enhanced',
            ], 400);
        }

        // Get all files
        $files = $request->allFiles();

        // Do we have files?
        if ($files !== []) {
            // Delete cropped images
            $this->deleteCroppedImages($product);

            // Loop over files
            foreach ($files['images'] as $file) {
                // Upload media
                $media = MediaUploader::fromSource($file)
                    ->useFilename(uniqid('cropped_', true))
                    ->toDirectory('product/'.floor($product->id / config('constants.image_per_folder')))
                    ->upload();

                // Attach media to product
                $product->attachMedia($media, config('constants.media_tags'));
            }
        }

        // Update status
        //check final approval
        if ($product->checkPriceRange()) {
            $product->status_id = StatusHelper::$finalApproval;
        } else {
            $product->status_id = StatusHelper::$priceCheck;
        }
        $product->is_enhanced = 1;
        $product->save();

        // Return success
        return response()->json([
            'status' => 'success',
        ]);
    }

    private function deleteCroppedImages($product)
    {
        if ($product->hasMedia(config('constants.media_tags'))) {
            foreach ($product->getMedia(config('constants.media_tags')) as $image) {
                $image_path = $image->getAbsolutePath();

                if (File::exists($image_path)) {
                    try {
                        File::delete($image_path);
                    } catch (Exception $exception) {
                        return response()->json(['code' => 400, 'message' => 'Opps! Something went wrong, Please try again.']);

                    }
                }
                try {
                    $image->forceDelete();
                } catch (Exception $exception) {
                    return response()->json(['code' => 400, 'message' => 'Opps! Something went wrong, Please try again.']);
                }
            }
        }
    }
}
