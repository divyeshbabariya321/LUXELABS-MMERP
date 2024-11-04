<?php

namespace App\Http\Controllers;

use Illuminate\View\View;
use Illuminate\Http\JsonResponse;
use Carbon\Carbon;
use App\MessageQueueHistory;
use Illuminate\Http\Request;

class MessageQueueHistoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        $title = 'List | Message Queue History';

        return view('message-queue-history.index', compact('title'));
    }

    public function records(Request $request): JsonResponse
    {
        $keyword = $request->get('keyword');

        $records = MessageQueueHistory::with('user');

        if (! empty($keyword)) {
            $records = $records->where(function ($q) use ($keyword) {
                $q->where('number', 'LIKE', "%$keyword%");
            });
        }

        $records = $records->latest('time')->paginate(12);

        $recorsArray = [];

        foreach ($records as $row) {
            $recorsArray[] = [
                'id'      => $row->id,
                'number'  => $row->number,
                'counter' => $row->counter,
                'type'    => $row->type,
                'user_id' => $row->user_id ?? '-',
                'time'    => Carbon::parse($row->time)->format('d-m-y H:i:s'),
            ];
        }

        return response()->json([
            'code'       => 200,
            'data'       => $recorsArray,
            'pagination' => (string) $records->links(),
            'total'      => $records->total(),
            'page'       => $records->currentPage(),
        ]);
    }
}
