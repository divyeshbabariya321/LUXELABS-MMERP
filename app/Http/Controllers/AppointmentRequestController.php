<?php

namespace App\Http\Controllers;

use Illuminate\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Models\AppointmentRequest;
use Illuminate\Support\Facades\Auth;

class AppointmentRequestController extends Controller
{
    public function index(Request $request): View
    {
        $title = 'Appointment Request';

        $records       = AppointmentRequest::select('*')->where('user_id', Auth::user()->id)->orWhere('requested_user_id', Auth::user()->id)->orderByDesc('id')->get();
        $records_count = $records->count();

        return view(
            'appointment-request.index', [
                'title'         => $title,
                'records_count' => $records_count,
            ]
        );
    }

    public function records(Request $request)
    {
        $records = AppointmentRequest::with('user', 'userrequest')->where('user_id', Auth::user()->id)->orWhere('requested_user_id', Auth::user()->id)->select('*')->orderByDesc('id');

        $records       = $records->take(25)->get();
        $records_count = $records->count();

        $records = $records->map(
            function ($script_document) {
                $script_document->created_at_date = \Carbon\Carbon::parse($script_document->created_at)->format('d-m-Y');

                return $script_document;
            }
        );

        return response()->json(
            [
                'code'  => 200,
                'data'  => $records,
                'total' => $records_count,
            ]
        );
    }

    public function recordAppointmentRequestAjax(Request $request)
    {
        $title = 'Appointment Request';
        $page  = $_REQUEST['page'];
        $page  = $page * 25;

        $records = AppointmentRequest::with('user', 'userrequest')->where('user_id', Auth::user()->id)->orWhere('requested_user_id', Auth::user()->id)->select('*')->orderByDesc('id')->offset($page)->limit(25);

        $records = $records->get();

        $records = $records->map(
            function ($script_document) {
                $script_document->created_at_date = \Carbon\Carbon::parse($script_document->created_at)->format('d-m-Y');

                return $script_document;
            }
        );

        return view(
            'appointment-request.index-ajax', [
                'title' => $title,
                'data'  => $records,
                'total' => count($records),
            ]
        );
    }

    public function AppointmentRequestRemarks($id): JsonResponse
    {
        $AppointmentRequest = AppointmentRequest::findorFail($id);

        return response()->json([
            'status'      => true,
            'data'        => $AppointmentRequest,
            'message'     => 'Data get successfully',
            'status_name' => 'success',
        ], 200);
    }
}
