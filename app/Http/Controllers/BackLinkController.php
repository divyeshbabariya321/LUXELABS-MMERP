<?php

namespace App\Http\Controllers;

use Illuminate\View\View;
use Illuminate\Http\JsonResponse;
use App\BackLinking;
use Illuminate\Http\Request;

class BackLinkController extends Controller
{
    /**
     * Get Broken Links Details
     * Function for display
     *
     * @return json response
     */
    public function displayBackLinkDetails(Request $request): View
    {
        if (! empty($_GET['title'])) {
            $title      = $_GET['title'];
            $details    = BackLinking::where('title', $title)->paginate(50)->setPath('');
            $pagination = $details->appends(
                [
                    'title' => $request->title,
                ]
            );
        } else {
            $details = BackLinking::paginate(50);
        }
        $titles = BackLinking::select('title')->pluck('title')->toArray();

        return View('back-linking.index',
            compact('details', 'titles')
        );
    }

    /**
     * Update title
     *
     * @return json response
     */
    public function updateTitle(Request $request): JsonResponse
    {
        $back_linking        = BackLinking::findOrFail($request['id']);
        $back_linking->title = $request['title'];
        $back_linking->save();

        return response()->json([
            'type'    => 'success',
            'message' => 'Title Updated',
        ]);
    }

    /**
     * Updated Title
     * Function for display
     *
     * @return json response
     */
    public function updateDesc(Request $request): JsonResponse
    {
        $back_linking              = BackLinking::findOrFail($request['id']);
        $back_linking->description = $request['desc'];
        $back_linking->save();

        return response()->json([
            'type'    => 'success',
            'message' => 'Title Updated',
        ]);
    }

    /**
     * Updated Title
     * Function for display
     *
     * @return json response
     */
    public function updateURL(Request $request): JsonResponse
    {
        $back_linking      = BackLinking::findOrFail($request['id']);
        $back_linking->url = $request['url'];
        $back_linking->save();

        return response()->json([
            'type'    => 'success',
            'message' => 'Title Updated',
        ]);
    }
}
