<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Response;
use App\BackLinkChecker;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Contracts\View\View;
use Illuminate\Contracts\Filesystem\FileNotFoundException;

class BrokenLinkCheckerController extends Controller
{
    /**
     * @SWG\Get(
     *   path="/broken-link-details",
     *   tags={"Scraper"},
     *   summary="Get broken link details",
     *   operationId="scraper-get-broken-link-details",
     *
     *   @SWG\Response(response=200, description="successful operation"),
     *   @SWG\Response(response=406, description="not acceptable"),
     *   @SWG\Response(response=500, description="internal server error"),
     * )
     */
    /**
     * Get Broken Links Details
     * Function for API
     *
     * @return JsonResponse response
     *
     * @throws FileNotFoundException
     */
    public function getBrokenLinkDetails(): JsonResponse
    {
        $json_file = Storage::get('local/files/broken-link-checker.json');
        if ($json_file) {
            $json['type']    = 'success';
            $json['message'] = 'Data Received Successfully';

            return response()->json($json, 200);
        } else {
            $json['type']    = 'error';
            $json['message'] = 'File Not Found';

            return response()->json($json, 203);
        }
    }

    /**
     * Get Broken Links Details
     * Function for display
     *
     * @return View response
     */
    public function displayBrokenLinkDetails(Request $request): \Illuminate\View\View
    {
        if (! empty($_GET['domain'])) {
            $domain     = $_GET['domain'];
            $details    = BackLinkChecker::where('domains', $domain)->paginate(100)->setPath('');
            $pagination = $details->appends(
                [
                    'domain' => $request->domain,
                ]
            );
        } elseif (! empty($_GET['ranking'])) {
            $ranking    = $_GET['ranking'];
            $details    = BackLinkChecker::where('rank', $ranking)->paginate(100)->setPath('');
            $pagination = $details->appends(
                [
                    'ranking' => $request->ranking,
                ]
            );
        } else {
            $details = BackLinkChecker::paginate(100);
        }
        $domains  = BackLinkChecker::select('domains')->pluck('domains')->toArray();
        $rankings = BackLinkChecker::select('rank')->pluck('rank')->toArray();

        return view('broken-link-checker.index',
            compact('details', 'domains', 'rankings')
        );
    }

    /**
     * Get Broken Links Details
     * Function for display
     *
     * @return JsonResponse response
     */
    public function updateDomain(Request $request): JsonResponse
    {
        $checker          = BackLinkChecker::findOrFail($request['id']);
        $checker->domains = $request['domain_name'];
        $checker->save();

        return response()->json([
            'type'    => 'success',
            'message' => 'Domain Updated',
        ]);
    }

    /**
     * Updated Title
     * Function for display
     *
     * @return JsonResponse response
     */
    public function updateTitle(Request $request): JsonResponse
    {
        $checker        = BackLinkChecker::findOrFail($request['id']);
        $checker->title = $request['title'];
        $checker->save();

        return response()->json([
            'type'    => 'success',
            'message' => 'Title Updated',
        ]);
    }
}
