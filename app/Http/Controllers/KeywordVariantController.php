<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\JsonResponse;
use App\Brand;
use App\Category;
use App\Jobs\CreateHashTags;
use Illuminate\Http\Request;
use App\KeywordSearchVariants;
use Yajra\DataTables\DataTables;

class KeywordVariantController extends Controller
{
    public function create(Request $request): JsonResponse
    {
        if ($request->ajax()) {
            $variant          = new KeywordSearchVariants();
            $variant->keyword = $request->keyword;
            $variant->save();
            $this->generateHashTagKeywords();

            return response()->json(['message' => 'Variant add successfully']);
        }
    }

    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = KeywordSearchVariants::latest()->get();

            return DataTables::of($data)
                ->addIndexColumn()
                ->make(true);
        }
    }

    public function delete(Request $request, $id): JsonResponse
    {
        $variant = KeywordSearchVariants::find($id);

        if ($variant) {
            $variant->delete();
        }

        return response()->json(['message' => 'delete successfully']);
    }

    public function generateHashTagKeywords()
    {
        $brandList    = Brand::getAll();
        $categoryList = Category::select('title', 'id')->get()->toArray();

        /* Initialize queue for add hashtags */
        $keywordVariantList = KeywordSearchVariants::where('is_hashtag_generated', 0)->pluck('keyword', 'id')->chunk(100)->toArray();

        foreach ($keywordVariantList as $chunk) {
            CreateHashTags::dispatch(['data' => $chunk, 'user_id' => Auth::user()->id, 'category_list' => $categoryList, 'brand_list' => $brandList, 'type' => 'keyword_variant'])->onQueue('generategooglescraperkeywords');
        }
    }
}
