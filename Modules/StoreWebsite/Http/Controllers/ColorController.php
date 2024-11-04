<?php

namespace Modules\StoreWebsite\Http\Controllers;

use App\ColorNamesReference;
use App\ColorReference;
use App\Colors;
use App\Http\Controllers\Controller;
use App\StoreWebsite;
use App\StoreWebsiteColor;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ColorController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $title = 'Colors | Store Website';
        $store_colors = new StoreWebsiteColor;

        if ($request->get('push') == 1) {
            $website = StoreWebsite::where('website_source', 'magento')->where('id', $request->get('store_website_id'))->where('api_token', '!=', '')->get();
            $colorsData = ColorNamesReference::groupBy('erp_name')->get();
            if (! $colorsData->isEmpty()) {
                foreach ($colorsData as $cd) {
                    foreach ($website as $web) {
                        $checkSite = StoreWebsiteColor::where('erp_color', $cd->erp_name)->where('store_website_id', $web->id)->where('platform_id', '>', 0)->first();
                        if (! $checkSite) {
                            $id = \seo2websites\MagentoHelper\MagentoHelper::addColor($cd->erp_name, $web);
                            if (! empty($id)) {
                                StoreWebsiteColor::where('erp_color', $cd->erp_name)->where('store_website_id', $web->id)->delete();
                                $swc = new StoreWebsiteColor;
                                $swc->erp_color = $cd->erp_name;
                                $swc->store_website_id = $web->id;
                                $swc->platform_id = $id;
                                $swc->save();
                            }
                        }
                    }
                }
            }

            return redirect()->back()->with('success', 'Color Request finished successfully');
        }

        // Check for keyword search
        $reqsstorecolor = $request->Store_Color ?? '';
        $reqStorecolour = $request->erp_colour;

        if ($request->keyword != null) {
            $store_colors = $store_colors->where('erp_color', 'like', '%'.$request->keyword.'%');
        }
        if ($request->website_ids != null) {
            $store_colors = $store_colors->WhereIn('store_website_id', $request->website_ids);
        }
        if ($request->erp_colour != null) {
            $store_colors = $store_colors->WhereIn('erp_color', $request->erp_colour);
        }
        if ($request->Store_Color != null) {
            $store_colors = $store_colors->where('store_color', 'LIKE', '%'.$request->Store_Color.'%');
        }

        $store_colors = $store_colors->get();

        return view('storewebsite::color.index', [
            'erp_colors' => (new Colors)->all(),
            'store_websites' => StoreWebsite::pluck('title', 'id')->toArray(),
            'store_colors' => $store_colors,
            'title' => $title,
            'reqsstorecolor' => $reqsstorecolor,
            'reqStorecolour' => $reqStorecolour,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create() {}

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $this->validate($request, [
            'store_website_id' => 'required|integer',
            'store_color' => 'required|string|max:255',
            'erp_color' => 'required|string|max:255',
        ]);

        $data = $request->except('_token');
        $storeWebsiteId = $request->get('store_website_id');
        $websites = StoreWebsite::where('parent_id', '=', $storeWebsiteId)->orWhere('id', '=', $storeWebsiteId)->get();
        $dataArray = [];
        foreach ($websites as $key => $website) {
            $data['store_website_id'] = $request->get('store_website_id');
            $data['store_color'] = $request->get('store_color');
            $data['erp_color'] = $request->get('erp_color');
            $data['store_website_id'] = $website->id;
            $dataArray[] = $data;
        }
        StoreWebsiteColor::insert($dataArray);

        return redirect()->route('store-website.color.list')->withSuccess('New Color(s) added successfully.');
    }

    /**
     * Display the specified resource.
     *
     *
     * @return \Illuminate\Http\Response
     */
    public function show(int $id) {}

    /**
     * Show the form for editing the specified resource.
     *
     *
     * @return \Illuminate\Http\Response
     */
    public function edit(int $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, int $id): RedirectResponse
    {
        $this->validate($request, [
            'store_website_id' => 'required|integer',
            'store_color' => 'required|string|max:255',
            'erp_color' => 'required|string|max:255',
        ]);

        $data = $request->except('_token');
        Log::debug(print_r($data, true));
        StoreWebsiteColor::find($id)->update($data);

        return redirect()->route('store-website.color.list')->withSuccess('You have successfully updated a store color!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(int $id): RedirectResponse
    {
        $storeColor = StoreWebsiteColor::where('id', $id)->first();
        if ($storeColor) {
            $storeColor->delete();

            return redirect()->route('store-website.color.list')->withSuccess('You have successfully deleted a store color');
        }

        return redirect()->route('store-website.color.list')->withErrors('Unable to delete a store color');
    }

    public function pushToStore(Request $request): JsonResponse
    {
        $id = $request->get('id', 0);

        if ($id > 0) {
            $color = StoreWebsiteColor::where('id', $id)->first();
            if ($color) {
                $website = StoreWebsite::find($color->store_website_id);
                if ($website) {
                    $colorName = ! empty($color->store_color) ? $color->store_color : $color->erp_color;
                    $id = \seo2websites\MagentoHelper\MagentoHelper::addColor($colorName, $website);
                    if (! empty($id)) {
                        $color->platform_id = $id;
                        $color->save();
                    }
                }

                return response()->json(['code' => 200, 'data' => $color]);
            }
        }

        return response()->json(['code' => 500, 'error' => 'Wrong row id!']);
    }

    public function colorReference()
    {
        $colors = ColorReference::select('original_color')->get();
        $colorArray = [];
        foreach ($colors as $color) {
            $colorArray[] = $color->original_color;
        }

        return json_encode($colorArray);
    }
}
