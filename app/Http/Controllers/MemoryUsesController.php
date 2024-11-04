<?php

namespace App\Http\Controllers;

use Illuminate\View\View;
use Illuminate\Http\JsonResponse;
use App\Setting;
use App\MemoryUsage;
use Illuminate\Http\Request;

class MemoryUsesController extends Controller
{
    public function index(): View
    {
        $thresold_limit_for_memory_uses = Setting::where('name', 'thresold_limit_for_memory_uses')->first();

        if ($thresold_limit_for_memory_uses) {
            $thresold_limit = $thresold_limit_for_memory_uses->val;
        } else {
            $thresold_limit = 0;
        }

        $memoryUses = MemoryUsage::latest()->paginate(Setting::get('pagination', 20));

        return view('memory', compact('memoryUses', 'thresold_limit'))->with('i', (request()->input('page', 1) - 1) * 20);
    }

    public function updateThresoldLimit(Request $request): JsonResponse
    {
        $updatedData = Setting::updateOrCreate([
            'name' => 'thresold_limit_for_memory_uses',
        ], [
            'val'  => $request->limit,
            'type' => 'number',
        ]);

        return response()->json(['code' => 200, 'message' => 'Thresold limit updated to ' . $updatedData->val]);
    }
}
