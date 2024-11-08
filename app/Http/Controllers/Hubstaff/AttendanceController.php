<?php

namespace App\Http\Controllers\Hubstaff;

use App\Http\Controllers\Controller;
use Curl\Curl;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AttendanceController extends Controller
{
    public $appToken;

    public $authToken;

    public $email;

    public $password;

    public function __construct(Request $request)
    {
        $this->appToken = getenv('HUBSTAFF_APP_KEY');
    }

    public function index(): View
    {
        return view('hubstaff.attendance-shifts.attendance-shift-page');
    }

    public function show(Request $request): View
    {
        $url = 'https://api.hubstaff.com/v2/organizations/'.$request->organization_id.'/attendance_shifts';

        $curl = new Curl;

        $curl->setHeader('Auth-Token', $request->auth_token);
        $curl->setHeader('App-Token', $this->appToken);

        $curl->get($url, [
            'page_start_id' => $request->page_start_id,
            'page_limit' => $request->page_limit,
            'start_time' => $request->start_time,
            'stop_time' => $request->stop_time,
            'organization_id' => $request->organization_id,
        ]);

        if ($curl->http_status_code == 401) {
            return view('hubstaff.error-page', compact('curl'));
        }

        if ($curl->error) {
            return view('hubstaff.error-page', compact('curl'));
        } else {
            $response = json_decode($curl->response);
        }

        $curl->close();

        $results = $response;

        return view('hubstaff.attendance', compact('results'));
    }
}
