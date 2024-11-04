<?php

namespace App\Http\Controllers\Marketing;

use App\Http\Requests\Marketing\StoreServiceRequest;
use Illuminate\View\View;
use Illuminate\Http\JsonResponse;
use App\Service;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ServiceController extends Controller
{
    public function index(): View
    {
        $data = Service::orderByDesc('id')->paginate(15);

        return view('marketing.services.index', compact('data'));
    }

    public function store(StoreServiceRequest $request): JsonResponse
    {

        $data = Service::create([
            'name'        => $request->name,
            'description' => $request->text,
        ]);

        return response()->json([
            $data,
        ]);
    }

    public function destroy(Request $request): JsonResponse
    {
        Service::destroy($request->id);

        return response()->json([
            $request->id,
        ]);
    }

    public function update(Request $request): JsonResponse
    {
        $updated = Service::findOrFail($request->id);

        $updated->name        = $request->name;
        $updated->description = $request->description;

        $updated->save();

        $data = Service::findOrFail($request->id);

        return response()->json([
            $data,
        ]);
    }
}
