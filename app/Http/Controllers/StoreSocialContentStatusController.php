<?php

namespace App\Http\Controllers;

use App\SiteDevelopment;
use App\SiteDevelopmentStatus;
use App\StoreSocialContent;
use App\StoreSocialContentStatus;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Validator;

class StoreSocialContentStatusController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        $records = StoreSocialContentStatus::query();

        $keyword = request('keyword');
        if (! empty($keyword)) {
            $records = $records->where(function ($q) use ($keyword) {
                $q->where('name', 'LIKE', "%$keyword%");
            });
        }

        $statuses = $records->get();

        return response()->json(['statuses' => $statuses]);
    }

    public function save(Request $request)
    {
        $post = $request->except('_token');

        $validator = Validator::make($post, [
            'name' => 'required',
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
        if ($id) {
            $isExtst = StoreSocialContentStatus::where('name', $request->name)->first();
            if ($isExtst) {
                return redirect()->back();
            }
        }

        $records = StoreSocialContentStatus::find($id);

        if (! $records) {
            $records = new StoreSocialContentStatus;
        }

        $records->fill($post);
        $records->save();

        return redirect()->back();
    }

    public function statusEdit(Request $request): JsonResponse
    {
        $id = $request->get('id', 0);
        if ($id) {
            $isExtst = StoreSocialContentStatus::where('name', $request->name)->first();
            if ($isExtst) {
                return response()->json(['message' => 'Already exists'], 500);
            }
        }
        $records = StoreSocialContentStatus::find($id);
        $records->update(['name' => $request->name]);

        return response()->json(['message' => 'Successfull'], 200);
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
    public function store(Request $request): RedirectResponse
    {
        if (! $request->name) {
            return redirect()->back()->with('error', 'Name required');
        }
        $name = $request->name;
        $name = ucfirst($name);
        $isExtst = StoreSocialContentStatus::where('name', $name)->first();
        if (! $isExtst) {
            $status = new StoreSocialContentStatus;
            $status->name = $name;
            $status->save();
        }

        return redirect()->back()->with('success', 'You have successfully created a content management status!');
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
        $modal = StoreSocialContentStatus::where('id', $id)->first();

        if ($modal) {
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
        $status = StoreSocialContentStatus::where('id', $id)->first();

        $isExist = StoreSocialContent::where('store_social_content_status_id', $id)->first();
        if ($isExist) {
            return response()->json(['code' => 500, 'error' => 'Status is attached to store social contents , Please update content before delete.']);
        }

        if ($status) {
            $status->delete();

            return response()->json(['code' => 200]);
        }

        return response()->json(['code' => 500, 'error' => 'Wrong id!']);
    }

    public function mergeStatus(Request $request): JsonResponse
    {
        $toStatus = $request->get('to_status');
        $fromStatus = $request->get('from_status');

        if (empty($toStatus)) {
            return response()->json(['code' => 500, 'error' => 'Merge status is missing']);
        }

        if (empty($fromStatus)) {
            return response()->json(['code' => 500, 'error' => 'Please select status before select merge status']);
        }

        if (in_array($toStatus, $fromStatus)) {
            return response()->json(['code' => 500, 'error' => 'Merge status can not be same']);
        }

        $status = SiteDevelopmentStatus::where('id', $toStatus)->first();
        $allMergeStatus = SiteDevelopment::whereIn('status', $fromStatus)->get();

        if ($status) {
            // start to merge first
            if (! $allMergeStatus->isEmpty()) {
                foreach ($allMergeStatus as $amc) {
                    $amc->status_id = $status->id;
                    $amc->save();
                }
            }
            // once all merged category store then delete that category from table
            SiteDevelopmentStatus::whereIn('id', $fromStatus)->delete();
        }

        return response()->json(['code' => 200, 'data' => [], 'messages' => 'Status has been merged successfully']);
    }
}
