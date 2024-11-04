<?php

namespace App\Http\Controllers;

use Illuminate\View\View;
use Illuminate\Support\Facades\Log;
use Exception;
use App\Models\Alert;
use Illuminate\Http\Request;
use App\Http\Requests\UpdateAlertRequest;

class AlertController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $type         = $request->get('type');
        $keyword      = $request->get('keyword');
        $currentPage  = $request->input('page', 1);

        // Retrieve all alerts from the table
        $alertsRecord = Alert::select('*');

        if (! empty($type)) {
            $alertsRecord = $alertsRecord->where('type', 'like', $type);
        }

        if (! empty($keyword)) {
            $alertsRecord = $alertsRecord->where(function ($query) use ($keyword) {
                $query->where('subject', 'like', '%' . $keyword . '%')
                    ->orWhere('message', 'like', '%' . $keyword . '%');
            });
        }

        // Paginate the results
        $alertsRecord = $alertsRecord->paginate(15); // You can adjust the number as needed

        $alertsType = Alert::select('type')->groupBy('type')->get();

        return view('sns-alerts.index', compact('alertsRecord', 'alertsType'));
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
     *
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        try {
            $request->merge(json_decode($request->getContent(), true));

            $data = [
                'type'      => $request->Type,
                'message_id'=> $request->MessageId,
                'subject'   => $request->Subject,
                'message'   => $request->Message,
                'timestamp' => $request->Timestamp,
                'body'      => $request->all(),
            ];
            $data['headers'] = collect($request->header())->transform(function ($item) {
                return $item[0];
            });
            $alert = Alert::create($data);

            if ($request->Type == 'SubscriptionConfirmation') {
                $endpoint = $request->SubscribeURL;
                $client   = new \GuzzleHttp\Client();
                $response = $client->request('GET', $endpoint);

                $statusCode = $response->getStatusCode();
                $content    = $response->getBody();

                $alert->update(['subscription_response'=>$content]);
            }

            return response()->json(['success'=>true, 'message'=>'Success']);
        } catch(Exception $e) {
            if ($alert) {
                $alert->update(['subscription_response'=>$e->getMessage()]);
            } else {
                $data = [
                    'type'                  => $request->Type,
                    'message_id'            => $request->MessageId,
                    'subject'               => $request->Subject,
                    'message'               => $request->Message,
                    'timestamp'             => $request->Timestamp,
                    'body'                  => $request->all(),
                    'subscription_response' => $e->getMessage(),
                ];
                $data['headers'] = collect($request->header())->transform(function ($item) {
                    return $item[0];
                });
                Alert::create($data);
            }
            Log::error('Error while storing sns alert => ' . $e->getMessage() . ' Request Body: ' . json_encode($request->all()));

            return response()->json(['success'=>false, 'message'=>$e->getMessage()], 422);
        }
    }

    /**
     * Display the specified resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function show(Alert $alert)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function edit(Alert $alert)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateAlertRequest $request, Alert $alert)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy(Alert $alert)
    {
        //
    }
}
