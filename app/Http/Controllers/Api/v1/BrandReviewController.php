<?php

namespace App\Http\Controllers\Api\v1;

use Illuminate\View\View;
use Illuminate\Http\JsonResponse;
use App\ReviewBrandList;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\BrandReviews;
use Illuminate\Support\Facades\Input;

class BrandReviewController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        return view('brand-review.index');
    }

    public function store(Request $request): JsonResponse
    {
        if ($request->name) {
            ReviewBrandList::insert([
                'name' => $request->name,
                'url'  => $request->url,
            ]);

            return response()->json(['status' => '200']);
        }

        return response()->json(['status' => '500']);
    }

    public function getAllBrandReview()
    {
        $data = ReviewBrandList::select('name', 'url')->get();

        return $data;
    }

    public function storeReview(Request $request): JsonResponse
    {
        $data = Input::all();
        if ($data) {
            foreach ($data as $key => $value) {
                $exists = BrandReviews::where('brand', $value['brand'])
                    ->where('review_url', $value['review_url'])
                    ->first();

                if (! $exists) {
                    BrandReviews::insert([
                        'website'    => $value['website'],
                        'brand'      => $value['brand'],
                        'review_url' => $value['review_url'],
                        'username'   => $value['username'],
                        'title'      => $value['title'],
                        'body'       => $value['body'],
                        'stars'      => $value['stars'],
                    ]);
                }
            }

            return response()->json([
                'code'    => 200,
                'message' => 'Data have been updated successfully',
            ]);
        }

        return response()->json([
            'code'    => 500,
            'message' => 'Error Occured, please try again later.',
        ]);
    }
}
