<?php

namespace App\Http\Controllers;

use App\UserAvaibility;
use App\UserAvaibilityHistory;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class UserAvaibilityController extends Controller
{
    public function index()
    {
        return respJson(200, '', ['data' => $this->loadIndex(request('id'))]);
    }

    public function loadIndex($userId)
    {
        $q = UserAvaibility::query();
        if ($s = $userId) {
            $q->where('user_id', $s);
        }
        if (! isAdmin()) {
            $q->where('user_id', loginId());
        }
        $list = $q->orderByDesc('id')->get();

        $html = [];
        $html[] = '<table class="table table-bordered">';
        $html[] = '<thead>
            <tr>
                <th width="5%">ID</th>
                <th width="20%" style="word-break: break-all;">From/To Date</th>
                <th width="15%" style="word-break: break-all;">Start/End Time</th>
                <th width="30%" style="word-break: break-all;">Available Days</th>
                <th width="15%" style="word-break: break-all;">Lunch Time</th>
                <th width="15%">Created at</th>
                <th width="35%" >Action</th>
            </tr>
        </thead>';
        if ($list->count()) {
            foreach ($list as $single) {
                $lunch_time = ($single->lunch_time_from && $single->lunch_time_to) ? $single->lunch_time_from.' - '.$single->lunch_time_to : '-';
                $html[] = '<tr>
                    <td>'.$single->id.'</td>
                    <td>'.$single->from.' - '.$single->to.'</td>
                    <td>'.$single->start_time.' - '.$single->end_time.'</td>
                    <td>'.(str_replace(',', ', ', $single->date) ?: '-').'</td>
                    <td>'.$lunch_time.'</td>
                    <td>'.$single->created_at.'</td>
                    <td><a class="btn btn-image" onclick="funUserAvailabilityEdit('.$single->id.')" style="padding: 0px 1px;"><img src="/images/edit.png" style="cursor: nwse-resize;"></a> 
                     <i onclick="UserAvailabilityHistory('.$single->id.')" data-id="'.$single->id.'" class="btn fa fa-info-circle user-avaibility-history" aria-hidden="true" style="padding: 0px 1px;"></i>
                    </td>
                </tr>';
            }
        } else {
            $html[] = '<tr>
                <td colspan="5">No records found.</td>
            </tr>';
        }
        $html[] = '</table>';

        return implode('', $html);
    }

    public function save()
    {
        try {
            Log::info('Request:'.json_encode(request()->all()));

            $user_id = request('user_id');
            if (! isAdmin()) {
                $user_id = loginId();
            }

            $errors = reqValidate(request()->all(), [
                'day' => 'required',
                'from' => 'required|date_format:Y-m-d',
                'to' => 'required|date_format:Y-m-d',
                'lunch_time_from' => 'required|date_format:H:i:s',
                'lunch_time_to' => 'required|date_format:H:i:s',
                'start_time' => 'required|date_format:H:i:s',
                'end_time' => 'required|date_format:H:i:s',
            ], [
                'day.required' => 'Days is required, please select atleast one.',
                'from.required' => 'From date is required.',
                'to.required' => 'To date is required.',
                'start_time.date_format' => 'Start time is invalid.',
                'end_time.date_format' => 'End time is invalid.',
            ]);
            if ($errors) {
                return respJson(400, $errors[0]);
            }

            $to = Carbon::createFromFormat('Y-m-d', request('to'));
            $from = Carbon::createFromFormat('Y-m-d', request('from'));
            if ($to->lte($from)) {
                return respJson(400, 'From date must be grater then To date');
            }

            if (request('start_time') >= request('end_time')) {
                return respJson(400, 'Start time must be greater than end time.');
            }

            if (request('lunch_time_from') && request('lunch_time_to') && request('lunch_time_from') >= request('lunch_time_to')) {
                return respJson(400, 'Lunch time to must be greater than from time.');
            }

            $recData = UserAvaibility::find(request('id'));

            if ($recData) {
                $recData = UserAvaibility::find(request('id'));
                $recData->user_id = $user_id;
                $recData->from = request('from');
                $recData->to = request('to');
                $recData->date = implode(',', request('day'));
                $recData->start_time = request('start_time');
                $recData->end_time = request('end_time');
                $recData->lunch_time = request('lunch_time') ?: null;
                $recData->lunch_time_from = request('lunch_time_from') ?: null;
                $recData->lunch_time_to = request('lunch_time_to') ?: null;
                $recData->save();
                $this->userAvaibilityHistory();
            } else {
                UserAvaibility::where('user_id', $user_id)->update(['is_latest' => 0]);

                UserAvaibility::create([
                    'user_id' => $user_id,
                    'from' => request('from'),
                    'to' => request('to'),
                    'status' => 1,
                    'note' => null,
                    'date' => implode(',', request('day')),
                    'start_time' => request('start_time'),
                    'end_time' => request('end_time'),
                    'lunch_time' => request('lunch_time') ?: null,
                    'lunch_time_from' => request('lunch_time_from') ?: null,
                    'lunch_time_to' => request('lunch_time_to') ?: null,
                    'is_latest' => 1,
                ]);
            }

            return respJson(200, 'Added successfully.', [
                'list' => $this->loadIndex($user_id),
            ]);
        } catch (\Throwable $th) {
            return respException($th);
        }
    }

    public function userAvaibilityHistory()
    {
        UserAvaibilityHistory::create([
            'user_avaibility_id' => request('id'),
            'user_id' => Auth::user()->id ?? request('user_id'),
            'from' => request('from'),
            'to' => request('to'),
            'status' => request('status'),
            'note' => request('note'),
            'date' => implode(',', request('day')),
            'start_time' => request('start_time'),
            'end_time' => request('end_time'),
            'lunch_time' => request('lunch_time') ?: null,
            'lunch_time_from' => request('lunch_time_from') ?: null,
            'lunch_time_to' => request('lunch_time_to') ?: null,
        ]);
    }

    public function userAvaibilityHistoryLog()
    {
        $q = UserAvaibilityHistory::query();
        $q->where('user_avaibility_id', request('id'));
        $list = $q->orderByDesc('id')->get();

        $html = [];
        $html[] = '<table class="table table-bordered">';
        $html[] = '<thead>
            <tr>
                <th width="5%">ID</th>
                <th width="20%" style="word-break: break-all;">From/To Date</th>
                <th width="15%" style="word-break: break-all;">Start/End Time</th>
                <th width="35%" style="word-break: break-all;">Available Days</th>
                <th width="10%" style="word-break: break-all;">Lunch Time</th>
                <th width="15%">Created at</th>
                <th width="15%" >Action</th>
            </tr>
        </thead>';
        if ($list->count()) {
            foreach ($list as $single) {
                $lunch_time = ($single->lunch_time_from && $single->lunch_time_to) ? $single->lunch_time_from.' - '.$single->lunch_time_to : '-';
                $html[] = '<tr>
                    <td>'.$single->id.'</td>
                    <td>'.$single->from.' - '.$single->to.'</td>
                    <td>'.$single->start_time.' - '.$single->end_time.'</td>
                    <td>'.(str_replace(',', ', ', $single->date) ?: '-').'</td>
                    <td>'.$lunch_time.'</td>
                    <td>'.$single->created_at.'</td>
                </tr>';
            }
        } else {
            $html[] = '<tr>
                <td colspan="5">No records found.</td>
            </tr>';
        }
        $html[] = '</table>';

        return implode('', $html);
    }

    /**
     * This function is used to search the user availability from the menu shortcut
     */
    public function search(Request $request): JsonResponse
    {
        if ($request->user_id) {
            $useravaibility = UserAvaibility::where('user_id', $request->user_id)->orderByDesc('id')->get();

            $html = [];
            if ($useravaibility && count($useravaibility) > 0) {
                foreach ($useravaibility as $key => $avaibility) {
                    $lunch_time = ($avaibility->lunch_time_from && $avaibility->lunch_time_to) ? $avaibility->lunch_time_from.' - '.$avaibility->lunch_time_to : '-';
                    $html[] = '<tr>
                        <td>'.($key + 1).'</td>
                        <td>'.$avaibility->from.' - '.$avaibility->to.'</td>
                        <td>'.$avaibility->start_time.' - '.$avaibility->end_time.'</td>
                        <td>'.(str_replace(',', ', ', $avaibility->date) ?: '-').'</td>
                        <td>'.$lunch_time.'</td>
                        <td>'.$avaibility->created_at.'</td>
                    </tr>';
                }
            } else {
                $html[] = '<tr>
                    <td colspan="6">No record found.</td>
                </tr>';
            }

            return response()->json([
                'status' => 200,
                'message' => 'Schedule successfully fetched',
                'data' => implode('', $html),
                'addButton' => '<button type="button" class="btn btn-secondary" onclick="funUserAvailabilityAddShortcut('.$request->user_id.')">Add
                New</button>',
            ]);
        } else {
            return response()->json([
                'status' => 400,
                'data' => '',
                'addButton' => '',
                'message' => 'Something went wrong',
            ]);
        }
    }
}
