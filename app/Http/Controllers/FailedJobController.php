<?php

namespace App\Http\Controllers;

use App\FailedJob;
use App\Http\Requests\FailedJobIndexRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class FailedJobController extends Controller
{
    public function index(FailedJobIndexRequest $request)
    {

        $jobs = FailedJob::whereNotNull('id')->orderByDesc('id');

        $filters = $request->except('page');
        if ($request->exception != '') {
            $jobs->where('exception', '=', $request->exception);
        }
        if ($request->payload != '') {
            $jobs->Where('payload', 'LIKE', '%'.$request->payload.'%');
        }

        if ($request->failed_at != '') {
            $jobs->where('failed_at', '>=', $request->failed_at);
            $jobs->where('failed_at', '<=', $request->failed_at);
        }
        $checkbox = $jobs->pluck('id');
        $jobs = $jobs->paginate();
        $count = $jobs->total();

        return view('failedjob.list', compact('jobs', 'filters', 'count', 'checkbox'))
            ->withInput();
    }

    public function delete(Request $request, $id): RedirectResponse
    {
        $jobs = FailedJob::find($id);

        if (! empty($jobs)) {
            $jobs->delete();
        }

        return redirect()->back()->withInput();
    }

    public function deleteMultiple(Request $request): JsonResponse
    {
        FailedJob::whereIn('id', $request->get('jobIds'))->delete();

        return response()->json(['code' => 200, 'data' => []]);
    }

    public function alldelete(Request $request, $id): JsonResponse
    {
        $trim = trim($id, '[]');
        $myArray = explode(',', $trim);
        FailedJob::whereIn('id', $myArray)->delete();

        return response()->json(['code' => 200, 'data' => []]);
    }
}
