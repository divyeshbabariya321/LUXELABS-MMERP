<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateGoogleServerRequest;
use App\Http\Requests\StoreGoogleServerRequest;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use App\GoogleServer;
use App\LogGoogleCse;
use Illuminate\Http\Request;

class GoogleServerController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $googleServer = GoogleServer::paginate(15);

        return view('google-server.index', [
            'googleServer' => $googleServer,
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
    public function store(StoreGoogleServerRequest $request): RedirectResponse
    {

        $data = $request->except('_token');

        GoogleServer::create($data);

        return redirect()->route('google-server.index')->withSuccess('You have successfully saved a Google Server!');
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
     */
    public function update(UpdateGoogleServerRequest $request, int $id): RedirectResponse
    {

        $data = $request->except('_token');

        GoogleServer::find($id)->update($data);

        return redirect()->back()->withSuccess('You have successfully updated a Google Server!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(int $id): RedirectResponse
    {
        $googleServer = GoogleServer::find($id);

        $googleServer->delete();

        return redirect()->route('google-server.index')->withSuccess('You have successfully deleted a Google Server');
    }

    public function logGoogleCse(Request $request)
    {
        $url      = $request->url;
        $keyword  = $request->keyword;
        $response = $request->response;
        $count    = $request->count;

        $responseString = 'Link: ' . $response[$count]['link'] . '\n Display Link: ' . $response[$count]['displayLink'] . '\n Title : ' . $response[$count]['title'] . '\n Image Details: ' . $response[$count]['image']['contextLink'] . ' Height:' . $response[$count]['image']['height'] . ' Width : ' . $response[$count]['image']['width'] . '\n ThumbnailLink ' . $response[$count]['image']['thumbnailLink'];

        $log            = new LogGoogleCse();
        $log->image_url = $url;
        $log->keyword   = $keyword;
        $log->response  = $responseString;
        $log->save();
    }
}
