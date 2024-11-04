<?php

namespace App\Http\Controllers;
use App\User;
use App\ProductColorHistory;
use App\ChatMessage;
use App\CategoryUpdateUser;
use App\Brand;

use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProductColorController extends Controller
{
    public function index(): View
    {
        $title  = 'Product Color';
        $brands = Brand::pluck('name', 'id')->toArray();
        $users  = User::pluck('name', 'id')->toArray();

        return view('product-color.index', compact('title', 'brands', 'users'));
    }

    public function history(Request $request, $id): JsonResponse
    {
        $productCategory = ProductColorHistory::leftJoin('users as u', 'u.id', 'product_color_histories.user_id')
            ->where('product_id', $id)
            ->orderByDesc('product_color_histories.created_at')
            ->select(['product_color_histories.*', 'u.name as user_name'])
            ->get();

        return response()->json(['code' => 200, 'data' => $productCategory]);
    }

    public function records(Request $request): JsonResponse
    {
        $brands   = $request->get('brands', []);
        $usresIds = $request->get('user_ids', []);

        $productCategory = ProductColorHistory::leftJoin('products as p', 'p.id', 'product_color_histories.product_id')
            ->leftJoin('users as u', 'u.id', 'product_color_histories.user_id');

        if (! empty($brands)) {
            $productCategory = $productCategory->whereIn('p.brand', $brands);
        }

        if (! empty($usresIds)) {
            $productCategory = $productCategory->whereIn('product_color_histories.user_id', $usresIds);
        }

        if ($request->keyword != null) {
            $productCategory = $productCategory->where(function ($q) use ($request) {
                $q->orWhere('p.id', 'like', '%' . $request->keyword . '%')->orWhere('p.name', 'like', '%' . $request->keyword . '%');
            });
        }

        $updatedHistory = clone $productCategory;
        $updatedHistory = $updatedHistory->groupBy('product_color_histories.user_id');
        $updatedHistory = $updatedHistory->select(['u.name as user_name', DB::raw('count(u.id) as total_updated')]);
        $updatedHistory = $updatedHistory->get()->toArray();

        $productCategory = $productCategory->orderByDesc('product_color_histories.created_at')
            ->select(['product_color_histories.*', 'u.name as user_name', 'p.name as product_name'])
            ->paginate();

        // total product without category by supplier
        return response()->json([
            'code'            => 200,
            'data'            => $productCategory->items(),
            'pagination'      => (string) $productCategory->render(),
            'total'           => $productCategory->total(),
            'updated_history' => $updatedHistory,
        ]);
    }

    public function updateCategoryAssigned(Request $request): JsonResponse
    {
        if (! empty($request->user_id) && $request->supplier_id != null) {
            $categoryUpdate = CategoryUpdateUser::where('supplier_id', $request->supplier_id)->where('user_id', $request->user_id)->first();
            if (! $categoryUpdate) {
                $categoryUpdate              = new CategoryUpdateUser;
                $categoryUpdate->user_id     = $request->user_id;
                $categoryUpdate->supplier_id = $request->supplier_id;
                $categoryUpdate->save();
            }

            if ($request->comment != '') {
                $message = "WORK ON {$categoryUpdate->supplier->supplier}: " . $request->comment;
                ChatMessage::sendWithChatApi($categoryUpdate->user->phone, null, $message);
            }

            return response()->json(['code' => 200, 'data' => [], 'message' => 'Request send succefully']);
        }

        return response()->json(['code' => 500, 'data' => [], 'message' => 'Required field is missing [user_id,supplier_id]']);
    }
}
