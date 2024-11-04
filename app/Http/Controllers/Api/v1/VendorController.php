<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Http\Resources\Vendor;
use App\Vendor as AppVendor;
use Illuminate\Http\Request;

class VendorController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        // Fetch paginated vendors
        $vendors = AppVendor::paginate($request->input('per_page') ?? 10);

        // Return paginated response with VendorResource
        return Vendor::collection($vendors);
    }
}
