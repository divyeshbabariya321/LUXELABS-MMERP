<?php

namespace App\Http\Controllers;

use Illuminate\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\GoogleScrapperContent;
use App\GoogleScrapperKeyword;

class GoogleScrapperController extends Controller
{
    public function index(): View
    {
        $contents = GoogleScrapperContent::all();
        $keywords = GoogleScrapperKeyword::all();

        return view('google-scrapper.index', compact('keywords', 'contents'));
    }

    public function saveKeyword(Request $request): JsonResponse
    {
        $keywordData          = new GoogleScrapperKeyword();
        $keywordData->keyword = $request->get('name');
        $keywordData->start   = $request->get('start');
        $keywordData->end     = $request->get('end');
        $keywordData->save();

        return response()->json(['message' => 'Google Scrapper Keyword Saved']);
    }
}
