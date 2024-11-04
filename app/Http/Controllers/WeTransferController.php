<?php

namespace App\Http\Controllers;

use App\LogRequest;
use App\Setting;
use App\Wetransfer;
use App\WeTransferLog;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\View\View;

class WeTransferController extends Controller
{
    public function index(): View
    {
        $wetransfers = Wetransfer::orderBy('id')->paginate(Setting::get('pagination'));

        return view('wetransfer.index', ['wetransfers' => $wetransfers]);
    }

    /**
     * @SWG\Get(
     *   path="/wetransfer",
     *   tags={"Wetransfer"},
     *   summary="Get wetransfer link",
     *   operationId="get-wetransfer-link",
     *
     *   @SWG\Response(response=200, description="successful operation"),
     *   @SWG\Response(response=406, description="not acceptable"),
     *   @SWG\Response(response=500, description="internal server error"),
     *
     *      @SWG\Parameter(
     *          name="mytest",
     *          in="path",
     *          required=true,
     *          type="string"
     *      ),
     * )
     */
    public function getLink()
    {
        $wetransfer = Wetransfer::where('is_processed', 0)->first();
        if ($wetransfer == null) {
            return json_encode(['error' => 'Nothing to process now']);
        }

        return json_encode($wetransfer);
    }

    /**
     * @SWG\Post(
     *   path="/wetransfer-file-store",
     *   tags={"Wetransfer"},
     *   summary="store wetransfer file",
     *   operationId="store-wetransfer-file",
     *
     *   @SWG\Response(response=200, description="successful operation"),
     *   @SWG\Response(response=406, description="not acceptable"),
     *   @SWG\Response(response=500, description="internal server error"),
     *
     *      @SWG\Parameter(
     *          name="mytest",
     *          in="path",
     *          required=true,
     *          type="string"
     *      ),
     * )
     */
    public function storeFile(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), ['file' => 'required', 'id' => 'required', 'filename' => 'required']);
        if ($validator->fails()) {
            return response()->json(['status' => 400, 'errors' => $validator->errors(), 'success' => false], 400);
        }

        WeTransferLog::create(['link' => '', 'log_description' => json_encode($request->all())]);
        $wetransfer = Wetransfer::find($request->id);
        if (! $wetransfer) {
            WeTransferLog::create(['link' => '', 'log_description' => 'we transfer item not found']);

            return response()->json(['status' => 400, 'message' => 'Wetransfer item not found', 'success' => false]);
        }
        WeTransferLog::create(['link' => '', 'log_description' => 'we transfer item found']);
        if ($request->file) {
            $file = $request->file('file');
            $fileN = time().$file->getClientOriginalName();
            $path = public_path().'/wetransfer/'.$request->id;
            $file->move($path, $fileN);

            $wetransfer->is_processed = 2;

            if ($wetransfer->files_list == null || $wetransfer->files_list == '') {
                $wetransfer->files_list = $fileN;
            } else {
                $wetransfer->files_list = $wetransfer->files_list.','.$fileN;
            }
            $wetransfer->update();

            $attachments_array = [];
            WeTransferLog::create(['link' => '', 'log_description' => 'Wetransfer has been stored']);

            return response()->json(['status' => 200, 'message' => 'Wetransfer has been stored', 'success' => true]);
        }

        return response()->json(['status' => 400, 'message' => 'File not found', 'success' => false]);
    }

    public function logs(): View
    {
        $logs = WeTransferLog::orderByDesc('id')->paginate(30);

        return view('wetransfer.logs', compact('logs'));
    }

    public function reDownloadFiles(Request $request): JsonResponse
    {
        $id = $request->id;
        $list = Wetransfer::where('id', $id)->first();

        if (! empty($list)) {
            $response = $this->downloadFromURL($list->id, $list->url, $list->supplier);
            WeTransferLog::create(['link' => $list->url, 'log_description' => $response ? 'Download request submitted' : 'Failed to send download request']);
            $list->update([
                'is_processed' => $response ? 3 : 0,
            ]);

            return response()->json([
                'status' => true,
                'message' => $response ? 'Download completed' : 'Download failed',
            ], 200);
        }

        return response()->json([
            'status' => true,
            'message' => 'Something went wrong, Please check if URL is correct!',
        ], 200);
    }

    public static function downloadFromURL($id, $url, $supplier)
    {

        $payload   = sprintf('{"id":%u,"url":"%s"}', $id, $url);
        $startTime = date('Y-m-d H:i:s', LARAVEL_START);
        $curl      = curl_init();
        curl_setopt_array($curl, [
            CURLOPT_URL            => 'http://75.119.154.85:100/download',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING       => '',
            CURLOPT_MAXREDIRS      => 10,
            CURLOPT_TIMEOUT        => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION   => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST  => 'POST',
            CURLOPT_POSTFIELDS     => $payload,
            CURLOPT_HTTPHEADER     => [
                'Content-Type: text/plain',
            ],
        ]);
        $httpcode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        $response = curl_exec($curl);

        curl_close($curl);

        LogRequest::log($startTime, $url, 'POST', json_encode($payload), json_decode($response), $httpcode, WeTransferController::class, 'downloadWetransferFiles');
        if ($response == 'Request Submitted!') {
            return true;
        } else {
            return false;
        }
    }
}
