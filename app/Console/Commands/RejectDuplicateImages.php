<?php

namespace App\Console\Commands;

use App\CroppedImageReference;
use App\Helpers\StatusHelper;
use App\ListingHistory;
use App\Product;
use App\User;
use Carbon\Carbon;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class RejectDuplicateImages extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @param int media_id
     * @param int product_id
     */
    protected $signature = 'RejectDuplicateImages {media_id} {product_id}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Product duplicate image auto reject';

    /**
     * Create a new command instance.
     */

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $media_id = $this->argument('media_id');
        $product_id = $this->argument('product_id');
        $query = CroppedImageReference::query();

        $query->where('original_media_id', $media_id);
        $query->where('product_id', $product_id);

        $data = $query->orderByDesc('id')
            ->groupBy('original_media_id')
            ->with(['media', 'newMedia', 'differentWebsiteImages' => function ($q) {
                $q->with('newMedia');
            }])
            ->first();

        try {
            if (! empty($data)) {
                // Get product Details
                $products = Product::find($data->product_id);

                if (! empty($products)) {
                    $checksums = [];

                    // Get last image cropper
                    $lastImageCropper = $products->crop_approved_by;
                    $user = User::find($lastImageCropper);

                    foreach ($data->differentWebsiteImages as $key) {
                        if (empty($key)) {
                            continue;
                        }

                        $image = $key->newMedia->directory.'/'.$key->newMedia->filename.'.'.$key->newMedia->extension;

                        $image = public_path('uploads/'.$image);

                        // Check is directory
                        if (File::isDirectory($_ = "$image")) {
                            continue;
                        }

                        // Checking is file exists or not
                        if (! File::exists($image)) {
                            continue;
                        }

                        $_ = "$image";

                        $hash = hash_file('md5', $_);

                        // delete duplicate
                        if (in_array($hash, $checksums)) {
                            $products->status_id = StatusHelper::$autoReject;
                            $products->is_crop_rejected = 1;
                            $products->crop_remark = 'Auto rejected';
                            $products->crop_rejected_by = 0;
                            $products->is_approved = 0;
                            $products->is_crop_approved = 0;
                            $products->is_crop_ordered = 0;
                            $products->is_crop_being_verified = 0;
                            $products->crop_rejected_at = Carbon::now()->toDateTimeString();
                            $products->save();

                            if ($user) {
                                if ((int) $lastImageCropper > 0) {
                                    $e = new ListingHistory;
                                    $e->user_id = $lastImageCropper;
                                    $e->product_id = $products->id;
                                    $e->content = ['action' => 'CROP_APPROVAL_DENIED', 'page' => 'Approved Listing Page'];
                                    $e->action = 'CROP_APPROVAL_DENIED';
                                    $e->save();
                                }
                            }

                            // Log crop rejected
                            $e = new ListingHistory;
                            $e->user_id = 0;
                            $e->product_id = $products->id;
                            $e->content = ['action' => 'CROP_REJECTED', 'page' => 'Approved Listing Page'];
                            $e->action = 'CROP_REJECTED';
                            $e->save();
                        } // add hash to list
                        else {
                            $checksums[] = $hash;
                        }
                    }
                }
            }
        } catch (Exception $e) {
            //
        }

        $this->output->write('Cron complated', true);
    }
}
