<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Response;
use App\DocumentCategory;
use Illuminate\Http\Request;

class DocuemntCategoryController extends Controller
{
    public function addCategory(Request $request): JsonResponse
    {
        $category = new DocumentCategory;

        $category->name = $request->name;

        $category->save();

        if ($category->id != null) {
            return response()->json([
                'success' => true,
                'message' => 'Category Created Sucessfully',
            ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Category Not Created',
            ]);
        }
    }
}
