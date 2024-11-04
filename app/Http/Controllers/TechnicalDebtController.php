<?php

namespace App\Http\Controllers;
use App\Setting;

use App\Models\TechnicalDebt;
use App\Models\TechnicalFrameWork;
use App\Models\TechnicalRemark;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class TechnicalDebtController extends Controller
{
    public function index(Request $request): View
    {
        $data['frameworks'] = TechnicalFrameWork::select('id', 'name')->get();
        $technicaldebt = new TechnicalDebt;

        if ($request->frameworks_ids) {
            $technicaldebt = $technicaldebt->WhereIn('technical_framework_id', $request->frameworks_ids);
        }
        if ($request->usernames) {
            $technicaldebt = $technicaldebt->WhereIn('user_id', $request->usernames);
        }

        if ($request->problem) {
            $technicaldebt = $technicaldebt->where('problem', 'LIKE', '%'.$request->problem.'%');
        }
        if ($request->description) {
            $technicaldebt = $technicaldebt->where('description', 'LIKE', '%'.$request->description.'%');
        }
        if ($request->estimate) {
            $technicaldebt = $technicaldebt->where('estimate_investigation', 'LIKE', '%'.$request->estimate.'%');
        }
        if ($request->approximate) {
            $technicaldebt = $technicaldebt->where('approximate_estimate', 'LIKE', '%'.$request->approximate.'%');
        }
        if ($request->status) {
            $technicaldebt = $technicaldebt->where('status', $request->status);
        }
        if ($request->priority) {
            $technicaldebt = $technicaldebt->where('priority', $request->priority);
        }

        $data['technicaldebts'] = $technicaldebt->latest()->paginate(Setting::get('pagination', 10));

        return view('technical-debt.index', $data);
    }

    public function frameWorkStore(Request $request): RedirectResponse
    {
        $platform = new TechnicalFrameWork;
        $platform->name = $request->framework_name;
        $platform->save();

        return redirect()->back()->with('success', 'Platform successfully created.');
    }

    public function technicalDeptStore(Request $request): RedirectResponse
    {
        $validated = new TechnicalDebt;
        $validated->user_id = auth()->user()->id;
        $validated->problem = $request->problem;
        $validated->description = $request->description;
        $validated->estimate_investigation = $request->estimate_investigation;
        $validated->approximate_estimate = $request->approximate_estimate;
        $validated->status = $request->status;
        $validated->technical_framework_id = $request->framework_id;
        $validated->priority = $request->priority;
        $validated->save();

        return redirect()->back()->with('success', 'Code Shortcuts successfully saved.');
    }

    public function technicalDebtGetRemark(Request $request): JsonResponse
    {
        try {
            $msg = '';
            if ($request->remark != '') {
                TechnicalRemark::create(
                    [
                        'technical_debt_id' => $request->technical_id,
                        'updated_by' => Auth::id(),
                        'remark' => $request->remark,
                    ]
                );
                $msg = ' Created and ';
            }

            $technicalRemarkDatas = TechnicalRemark::where('technical_debt_id', $request->technical_id)->get();

            $html = '';
            foreach ($technicalRemarkDatas as $technicalRemarkData) {
                $html .= '<tr>';
                $html .= '<td>'.$technicalRemarkData->id.'</td>';
                $html .= '<td>'.$technicalRemarkData->users->name.'</td>';
                $html .= '<td>'.$technicalRemarkData->remark.'</td>';
                $html .= '<td>'.$technicalRemarkData->created_at.'</td>';
                $html .= "<td><i class='fa fa-copy copy_remark' data-remark_text='".$technicalRemarkData->remark."'></i></td>";
            }

            $input_html = '';
            $i = 1;
            foreach ($technicalRemarkDatas as $technicalRemarkData) {
                $input_html .= '<span class="td-password-remark" style="margin:0px;"> '.$i.'.'.$technicalRemarkData->remark.'</span>';
                $i++;
            }

            return response()->json(['code' => 200, 'data' => $html, 'remark_data' => $input_html, 'message' => 'Remark '.$msg.' listed Successfully']);
        } catch (Exception $e) {
            return response()->json(['code' => 500, 'data' => '', 'remark_data' => '', 'message' => $e->getMessage()]);
        }
    }
}
