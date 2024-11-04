<?php

namespace App\Http\Controllers\Api\v1;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\GoogleScrapperContent;
use App\Http\Controllers\Controller;

class GoogleScrapperController extends Controller
{
    public function extractedData(Request $request): JsonResponse
    {
        
        $ContentData            = new GoogleScrapperContent;
        $ContentData->title     = isset($request->title) ? $request->title : '';
        $ContentData->date      = isset($request->date) ? $request->date : '';
        $ContentData->image     = isset($request->image) ? $request->image : '';
        $ContentData->url       = isset($request->url) ? $request->url : '';
        $ContentData->email     = isset($request->email) ? $request->email : '';
        $ContentData->number    = isset($request->number) ? $request->number : '';
        $ContentData->about_us  = isset($request->about_us) ? $request->about_us : '';
        $ContentData->facebook  = isset($request->facebook) ? $request->facebook : '';
        $ContentData->instagram = isset($request->instagram) ? $request->instagram : '';

        $ContentData->save();

        return response()->json(['code' => 200, 'message' => 'Google Scrapper Data saved', 'data' => $ContentData]);
    }
}
