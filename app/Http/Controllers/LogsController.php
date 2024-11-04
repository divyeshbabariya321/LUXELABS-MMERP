<?php

namespace App\Http\Controllers;

use App\SearchAttachedImagesLog;
use App\SocialWebhookLog;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class LogsController extends Controller
{
    public function index(Request $request): View
    {
        $data = SearchAttachedImagesLog::with('customer')->latest()->paginate(15);

        return view('image-logs.index', compact('data'));
    }

    public function deleteLog(Request $request): JsonResponse
    {
        $logId = $request->id;
        SearchAttachedImagesLog::where('id', $logId)->delete();

        return response()->json(['code' => 200, 'message' => 'Log deleted successfully']);
    }

    public function socialWebhookLogs(): View
    {
        $data = SocialWebhookLog::orderByDesc('id')->latest()->paginate(100);

        return view('social-webhook-logs.index', compact('data'));
    }
}
