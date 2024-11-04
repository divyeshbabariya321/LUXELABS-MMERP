<?php

namespace Modules\StoreWebsite\Http\Controllers;

use App\CountryGroup;
use App\CountryGroupItem;
use App\SiteDevelopment;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\View\View;

class CountryGroupController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        $title = 'Country Group';

        return view('storewebsite::country-group.index', compact('title'));
    }

    public function records(): JsonResponse
    {
        $records = CountryGroup::leftJoin('country_group_items as cgi', 'cgi.country_group_id', 'country_groups.id');

        $keyword = request('keyword');
        if (! empty($keyword)) {
            $records = $records->where(function ($q) use ($keyword) {
                $q->orWhere('name', 'LIKE', "%$keyword%")->orWhere('cgi.country_code', 'LIKE', "%$keyword%");
            });
        }

        $records = $records->groupBy('country_groups.id');

        $records = $records->select(['country_groups.*'])->get();

        foreach ($records as $i => $record) {
            $records[$i]['items'] = $record->groupItems;
        }

        return response()->json(['code' => 200, 'data' => $records, 'total' => count($records)]);
    }

    public function save(Request $request): JsonResponse
    {
        $post = $request->all();

        $validator = Validator::make($post, [
            'name' => 'required',
            'country_code.*' => 'required',
        ]);

        if ($validator->fails()) {
            $outputString = '';
            $messages = $validator->errors()->getMessages();
            foreach ($messages as $k => $errr) {
                foreach ($errr as $er) {
                    $outputString .= "$k : ".$er.'<br>';
                }
            }

            return response()->json(['code' => 500, 'error' => $outputString]);
        }

        $id = $request->get('id', 0);

        $records = CountryGroup::find($id);

        if (! $records) {
            $records = new CountryGroup;
        } else {
            $records->groupItems()->delete();
        }

        $records->fill($post);

        if ($records->save()) {
            foreach ($request->country_code as $code) {
                $ci = new CountryGroupItem;
                $ci->country_code = $code;
                $ci->country_group_id = $records->id;
                $ci->save();
            }
        }

        return response()->json(['code' => 200, 'data' => $records]);
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
     * Display the specified resource.
     *
     *
     * @return \Illuminate\Http\Response
     */
    public function show(int $id)
    {
        //
    }

    /**
     * Edit Page
     *
     * @param  Request  $request  [description]
     * @param  mixed  $id
     */
    public function edit(Request $request, $id): JsonResponse
    {
        $modal = CountryGroup::where('id', $id)->first();

        if ($modal) {
            $modal->items = $modal->groupItems->pluck('country_code');

            return response()->json(['code' => 200, 'data' => $modal]);
        }

        return response()->json(['code' => 500, 'error' => 'Id is wrong!']);
    }

    /**
     * Update the specified resource in storage.
     *
     *
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, int $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy(int $id)
    {
        //
    }

    /**
     * delete Page
     *
     * @param  Request  $request  [description]
     * @param  mixed  $id
     */
    public function delete(Request $request, $id): JsonResponse
    {
        $coutryGroup = CountryGroup::where('id', $id)->first();

        /*$isExist = SiteDevelopment::where("status", $id)->first();
        if ($isExist) {
        return response()->json(["code" => 500, "error" => "Status is attached to Site Development , Please update site development before delete."]);
        }*/

        if ($coutryGroup) {
            // delete child table
            $coutryGroup->groupItems()->delete();
            $coutryGroup->delete();

            return response()->json(['code' => 200]);
        }

        return response()->json(['code' => 500, 'error' => 'Wrong id!']);
    }
}
