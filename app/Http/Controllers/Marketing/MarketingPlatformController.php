<?php

namespace App\Http\Controllers\Marketing;
use App\Http\Controllers\Marketing;

use App\Http\Requests\Marketing\EditMarketingPlatformRequest;
use App\Http\Requests\Marketing\StoreMarketingPlatformRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Response;
use App\Setting;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Marketing\MarketingPlatform;

class MarketingPlatformController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if ($request->term || $request->date) {
            $query = MarketingPlatform::query();

            //global search term
            if (request('term') != null) {
                $query->where('name', 'LIKE', "%{$request->term}%");
            }
            if (request('date') != null) {
                $query->whereDate('created_at', request('website'));
            }

            $platforms = $query->orderby('id', 'desc')->paginate(Setting::get('pagination'));
        } else {
            $platforms = MarketingPlatform::orderby('id', 'desc')->paginate(Setting::get('pagination'));
        }

        if ($request->ajax()) {
            return response()->json([
                'tbody' => view('marketing.platforms.partials.data', compact('platforms'))->render(),
                'links' => (string) $platforms->render(),
            ], 200);
        }

        return view('marketing.platforms.index', [
            'platforms' => $platforms,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreMarketingPlatformRequest $request): RedirectResponse
    {

        $data = $request->except('_token');
        MarketingPlatform::create($data);

        return redirect()->back()->withSuccess('You have successfully stored Marketing Platform');
    }

    /**
     * Display the specified resource.
     *
     *
     * @return \Illuminate\Http\Response
     */
    public function show(MarketingPlatform $marketingPlatform)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param \App\MarketingPlatform $marketingPlatform
     */
    public function edit(EditMarketingPlatformRequest $request): RedirectResponse
    {
        $platform = MarketingPlatform::findorfail($request->id);
        $data     = $request->except('_token', 'id');
        $platform->update($data);

        return redirect()->back()->withSuccess('You have successfully changed Marketing Platform');
    }

    /**
     * Update the specified resource in storage.
     *
     *
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, MarketingPlatform $marketingPlatform)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\MarketingPlatform $marketingPlatform
     */
    public function destroy(Request $request): JsonResponse
    {
        $platform = MarketingPlatform::findorfail($request->id);
        $platform->delete();

        return response()->json([
            'success' => true,
            'message' => 'Marketing Platform Deleted',
        ]);
    }
}
