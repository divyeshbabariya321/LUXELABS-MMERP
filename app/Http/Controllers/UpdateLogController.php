<?php

namespace App\Http\Controllers;

use App\Setting;
use App\UpdateLog;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Exception;

class UpdateLogController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try {
            $q = UpdateLog::query();
            if ($s = request('api_url')) {
                $q->where('api_url', 'like', '%' . $s . '%');
            }
            if ($s = request('device')) {
                $q->where('device', $s);
            }
            if ($s = request('api_type')) {
                $q->where('api_type', $s);
            }
            if ($s = request('response_code')) {
                $q->where('response_code', $s);
            }

            $updateLog = $q->orderByDesc('id')->paginate(Setting::get('pagination'));

            $listApiUrls       = UpdateLog::orderBy('api_url')->select('api_url')->distinct()->pluck('api_url', 'api_url');
            $listDevices       = UpdateLog::orderBy('device')->select('device')->distinct()->pluck('device', 'device');
            $listApiMethods    = UpdateLog::orderBy('api_type')->select('api_type')->distinct()->pluck('api_type', 'api_type');
            $listResponseCodes = UpdateLog::orderBy('response_code')->select('response_code')->distinct()->pluck('response_code', 'response_code');

            return view('update-log.index', compact(
                'updateLog',
                'listApiUrls',
                'listApiMethods',
                'listDevices',
                'listResponseCodes'
            ));
        } catch (Exception $e) {
            return redirect()->back()->withErrors($e->getMessage());
        }
    }

    public function search(Request $request)
    {
        return $this->index();
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $updateLog                 = new UpdateLog();
            $updateLog->api_url        = $request->api_url;
            $updateLog->device         = $request->device;
            $updateLog->api_type       = $request->api_type;
            $updateLog->user_id        = $request->user_id;
            $updateLog->request_header = is_array($request->header) ? json_encode($request->header) : $request->header;
            $updateLog->email          = $request->email ?: null;
            $updateLog->request_body   = is_array($request->request_body) ? json_encode($request->request_body) : $request->request_body;
            $updateLog->response_code  = $request->response_code ?: null;
            $updateLog->response_body  = is_array($request->response_body) ? json_encode($request->response_body) : $request->response_body;
            $updateLog->app_version    = $request->app_version;
            $updateLog->other          = $request->other;
            if ($updateLog->save()) {
                return response()->json(['status' => true, 'response_code' => 200, 'data' => $updateLog], JsonResponse::HTTP_OK);
            }

            return response()->json(['status' => false, 'response_code' => 404, 'message' => 'Data not found'], JsonResponse::HTTP_NOT_FOUND);
        } catch (Exception $e) {
            return response()->json(['status' => false, 'response_code' => 500, 'message' => $e->getMessage()], JsonResponse::HTTP_BAD_REQUEST);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(int $id): JsonResponse
    {
        try {
            $updateLog = UpdateLog::all();

            if (! empty($updateLog)) {
                return response()->json([$updateLog]);
            }

            return response()->json(['message' => 'Data not found'], JsonResponse::HTTP_NOT_FOUND);
        } catch (Exception $e) {
            return response()->json(['message' => $e->getMessage()], JsonResponse::HTTP_BAD_REQUEST);
        }
    }

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
     * @param int $id
     */
    public function destroy(Request $request): JsonResponse
    {
        try {
            $updateLog = UpdateLog::where('id', '=', $request->id)->delete();

            return response()->json(['code' => 200, 'data' => $updateLog, 'message' => 'Deleted successfully!!!']);
        } catch (Exception $e) {
            $msg = $e->getMessage();

            return response()->json(['code' => 500, 'message' => $msg]);
        }
    }

    public function requestHeaderShow(Request $request)
    {
        $id            = $request->input('id');
        $requestHeader = UpdateLog::where('id', $id)->value('request_header');
        $htmlContent   = '<tr><td>' . $requestHeader . '</td></tr>';

        return $htmlContent;
    }
}
