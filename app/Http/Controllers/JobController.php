<?php

namespace App\Http\Controllers;

use App\Http\Requests\JobIndexRequest;
use App\Job;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class JobController extends Controller
{
    public function index(JobIndexRequest $request)
    {
        $jobs = Job::whereNotNull('id');

        $filters = $request->except('page');
        if ($request->queue != '') {
            $jobs->where('queue', '=', $request->queue);
        }

        if ($request->payload != '') {
            $jobs->Where('payload', 'LIKE', '%'.$request->payload.'%');
        }

        if ($request->reserved_date != '') {
            $reserved_start = \Carbon\Carbon::Parse($request->reserved_date)->startOfDay()->getTimeStamp();
            $reserved_end = \Carbon\Carbon::Parse($request->reserved_date)->endOfDay()->getTimeStamp();

            $jobs->where('reserved_at', '>=', $reserved_start)
                ->where('reserved_at', '<', $reserved_end);
        }

        if ($request->available_date != '') {
            $available_start = \Carbon\Carbon::Parse($request->available_date)->startOfDay()->getTimeStamp();
            $available_end = \Carbon\Carbon::Parse($request->available_date)->endOfDay()->getTimeStamp();

            $jobs->where('available_at', '>=', $available_start)
                ->where('available_at', '<', $available_end);
        }

        $checkbox = $jobs->pluck('id');
        $jobs = $jobs->paginate();
        $count = $jobs->count();
        $listQueues = Job::JOBS_LIST;

        return view('job.list', compact('jobs', 'filters', 'count', 'checkbox', 'listQueues'))
            ->withInput();
    }

    public function delete(Request $request, $id): RedirectResponse
    {
        $jobs = Job::find($id);

        if (! empty($jobs)) {
            $jobs->delete();
        }

        return redirect()->back()->withInput();
    }

    public function deleteMultiple(Request $request): JsonResponse
    {
        Job::whereIn('id', $request->get('jobIds'))->delete();

        return response()->json(['code' => 200, 'data' => []]);
    }

    public function alldelete(Request $request, $id): JsonResponse
    {
        $trim = trim($id, '[]');
        $myArray = explode(',', $trim);
        Job::whereIn('id', $myArray)->delete();

        return response()->json(['code' => 200, 'data' => []]);
    }
}
