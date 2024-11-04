<?php

namespace App\Http\Controllers\Seo;

use App\Http\Controllers\Controller;
use App\Models\DataTableColumn;
use App\Models\Seo\SeoHistory;
use App\Models\Seo\SeoProcess;
use App\Models\Seo\SeoProcessChecklist;
use App\Models\Seo\SeoProcessChecklistHistory;
use App\Models\Seo\SeoProcessKeyword;
use App\Models\Seo\SeoProcessStatus;
use App\Models\Seo\SeoProcessStatusHistory;
use App\Models\SeoStatus;
use App\StoreWebsite;
use App\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ContentController extends Controller
{
    private $route = 'seo/content';

    private $view = 'seo/content';

    private $moduleName = 'SEO Content';

    public function index(Request $request)
    {
        if ($request->ajax()) {
            if ($request->type == 'GET_HISTORY') {
                return $this->renderPriceHistory();
            }

            return $this->getAjaxSeoProcess();
        }
        $data['websites'] = StoreWebsite::select('id', 'website')->get();
        $data['users'] = User::select('id', 'name')->get();
        $data['moduleName'] = $this->moduleName;
        $data['status'] = SeoStatus::get();

        $datatableModel = DataTableColumn::select('column_name')->where('user_id', auth()->user()->id)->where('section_name', 'seo-content')->first();

        $data['dynamicColumnsToShowSeo'] = [];
        if (! empty($datatableModel->column_name)) {
            $hideColumns = $datatableModel->column_name ?? '';
            $data['dynamicColumnsToShowSeo'] = json_decode($hideColumns, true);
        }

        $data['total_seo'] = SeoProcess::count();

        return view("{$this->view}/index", $data);
    }

    public function columnVisbilityUpdate(Request $request): RedirectResponse
    {
        $userCheck = DataTableColumn::where('user_id', auth()->user()->id)->where('section_name', 'seo-content')->first();

        if ($userCheck) {
            $column = DataTableColumn::find($userCheck->id);
            $column->section_name = 'seo-content';
            $column->column_name = json_encode($request->column_seo);
            $column->save();
        } else {
            $column = new DataTableColumn;
            $column->section_name = 'seo-content';
            $column->column_name = json_encode($request->column_seo);
            $column->user_id = auth()->user()->id;
            $column->save();
        }

        return redirect()->back()->with('success', 'column visiblity Added Successfully!');
    }

    public function statuscolor(Request $request): RedirectResponse
    {
        $status_color = $request->all();
        foreach ($status_color['color_name'] as $key => $value) {
            $bugstatus = SeoStatus::find($key);
            $bugstatus->status_color = $value;
            $bugstatus->save();
        }

        return redirect()->back()->with('success', 'The status color updated successfully.');
    }

    public function create(Request $request): JsonResponse
    {
        $data['storeWebsites'] = StoreWebsite::select('id', 'website')->get();
        $data['seoProcessStatus'] = SeoProcessStatus::all();
        $data['users'] = User::select('id', 'name')->get();
        $data['moduleName'] = $this->moduleName;
        $html = view("$this->route/ajax/create", $data)->render();

        return response()->json([
            'success' => true,
            'data' => $html,
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        DB::beginTransaction();
        $seoProcess = SeoProcess::create([
            'website_id' => $request->website_id,
            'user_id' => $request->user_id,
            'word_count' => $request->word_count,
            'suggestion' => $request->suggestion,
            'price' => $request->price,
            'is_price_approved' => $request->is_price_approved ?? 0,
            'google_doc_link' => $request->google_doc_link,
            'seo_process_status_id' => $request->seo_process_status_id,
            'live_status_link' => $request->live_status_link,
            'published_at' => $request->published_at,
            'status' => $request->status,
        ]);
        $this->addPriceHistory($seoProcess, true);
        $this->addUserHistory($seoProcess, true);
        $this->addKeywordData($seoProcess);
        DB::commit();

        return response()->json([
            'success' => true,
            'message' => 'SEO Content Added Successfully.',
        ]);
    }

    public function show(Request $request, int $id)
    {
        if ($request->ajax()) {
            if ($request->type == 'GET_HISTORY') {
                return $this->renderPriceHistory();
            }
        }
        $data['seoProcess'] = SeoProcess::find($id);
        $data['storeWebsites'] = StoreWebsite::select('id', 'website')->get();
        $data['seoProcessStatus'] = SeoProcessStatus::all();
        $data['users'] = User::select('id', 'name')->get();
        $data['moduleName'] = $this->moduleName;

        return view("{$this->view}/view", $data);
    }

    public function edit(Request $request, int $id)
    {
        $seoProcess = SeoProcess::find($id);
        if ($request->ajax()) {
            if ($request->type == 'CHECKLIST') {
                return $this->checklistForm($seoProcess);
            }

            if ($request->type == 'CHECKLIST_HISTORY') {
                return $this->checklistHistory($seoProcess);
            }

            if ($request->type == 'STATUS_HISTORY') {
                return $this->statusHistory($seoProcess);
            }
        }
        $data['seoProcess'] = $seoProcess;
        $data['storeWebsites'] = StoreWebsite::select('id', 'website')->get();
        $data['seoProcessStatus'] = SeoProcessStatus::all();
        $data['users'] = User::select('id', 'name')->get();
        $data['moduleName'] = $this->moduleName;
        $html = view("{$this->view}/ajax/edit", $data)->render();

        return response()->json([
            'success' => true,
            'data' => $html,
        ]);
    }

    public function update(Request $request, int $id)
    {
        $seoProcess = SeoProcess::find($id);
        if ($request->type == 'CHECKLIST') {
            return $this->addChecklistData($seoProcess);
        } elseif ($request->type == 'STATUS') {
            return $this->changeStatus($seoProcess);
        }

        DB::beginTransaction();
        $this->addPriceHistory($seoProcess, false);
        $this->addUserHistory($seoProcess, false);
        $seoProcess->update([
            'website_id' => $request->website_id,
            'user_id' => $request->user_id,
            'word_count' => $request->word_count,
            'suggestion' => $request->suggestion,
            'price' => $request->price,
            'is_price_approved' => $request->is_price_approved ?? 0,
            'google_doc_link' => $request->google_doc_link,
            'seo_process_status_id' => $request->seo_process_status_id,
            'live_status_link' => $request->live_status_link,
            'published_at' => $request->published_at,
            'status' => $request->status,
        ]);
        $this->addKeywordData($seoProcess);
        DB::commit();

        return response()->json([
            'success' => true,
            'message' => 'SEO Content Updated Successfully.',
        ]);
    }

    private function addKeywordData(SeoProcess $seoProcess)
    {
        $request = request();
        if (! empty($request->keyword)) {
            $seoKeyword = [];
            $seoProcess->keywords()->delete();
            foreach ($request->keyword as $ky => $keyword) {
                $seoKeyword[] = new SeoProcessKeyword([
                    'name' => $keyword,
                    'index' => $ky + 1,
                ]);
            }
            $seoProcess->keywords()->saveMany($seoKeyword);
        }
    }

    private function addChecklistData(SeoProcess $seoProcess)
    {
        $request = request();
        if ($request->checklistType == 'seo') {
            $checkList = [];
            $checkListHistory = [];
            $seoProcess->seoChecklist()->delete();
            foreach ($request->label as $ky => $item) {
                $checkList[] = new SeoProcessChecklist([
                    'field_name' => $item,
                    'type' => 'seo',
                    'is_checked' => $request->is_checked[$ky] ?? '',
                    'value' => $request->value[$ky] ?? '',
                    'date' => $request->date[$ky] ?? '',
                ]);
                $checkListHistory[] = new SeoProcessChecklistHistory([
                    'user_id' => auth()->id(),
                    'field_name' => $item,
                    'type' => 'seo',
                    'is_checked' => $request->is_checked[$ky] ?? '',
                    'value' => $request->value[$ky] ?? '',
                    'date' => $request->date[$ky] ?? '',
                ]);
            }
            $seoProcess->seoChecklist()->saveMany($checkList);
            $seoProcess->seoChecklistHistory()->saveMany($checkListHistory);
        }

        if ($request->checklistType == 'publish') {
            $checkList = [];
            $checkListHistory = [];
            $seoProcess->publishChecklist()->delete();
            foreach ($request->label as $ky => $item) {
                $checkList[] = new SeoProcessChecklist([
                    'field_name' => $item,
                    'type' => 'publish',
                    'is_checked' => $request->is_checked[$ky] ?? '',
                    'value' => $request->value[$ky] ?? '',
                    'date' => $request->date[$ky] ?? '',
                ]);
                $checkListHistory[] = new SeoProcessChecklistHistory([
                    'user_id' => auth()->id(),
                    'field_name' => $item,
                    'type' => 'publish',
                    'is_checked' => $request->is_checked[$ky] ?? '',
                    'value' => $request->value[$ky] ?? '',
                    'date' => $request->date[$ky] ?? '',
                ]);
            }
            $seoProcess->publishChecklist()->saveMany($checkList);
            $seoProcess->publishChecklistHistory()->saveMany($checkListHistory);
        }

        return response()->json([
            'success' => true,
        ]);
    }

    private function getAjaxSeoProcess()
    {
        $auth = auth()->user();
        $request = request();
        $seoStatus = SeoProcessStatus::all();
        $seoProcess = SeoProcess::with(['website:id,website', 'user:id,name'])->orderByDesc('id');
        $filter = (object) $request->filter;
        if (! empty($filter->website_id)) {
            $seoProcess = $seoProcess->whereIn('website_id', explode(',', $filter->website_id));
        }

        if (! empty($filter->price_status)) {
            if ($filter->price_status == 1) {
                $seoProcess = $seoProcess->where('is_price_approved', $filter->price_status);
            } else {
                $seoProcess = $seoProcess->where('is_price_approved', 0);
            }
        }

        if (! empty($filter->user_id)) {
            $seoProcess = $seoProcess->whereIn('user_id', explode(',', $filter->user_id));
        }

        if (! empty($filter->status)) {
            $seoProcess = $seoProcess->where('status', $filter->status);
        }

        if ($auth->hasRole(['user'])) {
            $seoProcess = $seoProcess->where('user_id', $auth->id);
        }
        $datatable = datatables()->eloquent($seoProcess)
            ->addColumn('actions', function ($val) use ($auth) {
                $editUrl = route('seo.content.edit', $val->id);
                $actions = '';
                if ($auth->hasRole(['Admin', 'user', 'Seo Head'])) {
                    $actions .= "<a href='javascript:;' data-url='{$editUrl}' class='btn btn-secondary btn-sm editBtn'>Edit</a>";
                }

                return $actions;
            })
            ->addColumn('user_id', function ($val) use ($auth) {
                $user = ($val->user->name ?? '-');
                if ($auth->hasRole(['Admin'])) {
                    $historyImg = asset('images/history.png');
                    $user .= "<button class='btn btn-image search ui-autocomplete-input userHistoryBtn' data-type='user' data-id='{$val->id}' style='cursor: default'>";
                    $user .= "<img src='{$historyImg}' style='width:30px !important' />";
                    $user .= '</button>';
                }

                return $user;
            })
            ->addColumn('website_id', function ($val) {
                return $val->website->website ?? '-';
            })
            ->editColumn('status', function ($val) {
                $status = '';
                if ($val->status == 'planned') {
                    $status = "<span class='badge btn'>Planned</span>";
                } else {
                    $status = "<span class='badge btn'>Admin approve</span>";
                }

                return $status;
            })
            ->editColumn('website_id', function ($val) {
                return $val->website->website ?? '-';
            })
            ->addColumn('keywords', function ($val) {
                if (! empty($val->keywords)) {
                    $keywordArray = [];
                    foreach ($val->keywords as $item) {
                        $keywordArray[] = $item->name;
                    }

                    return implode(', ', $keywordArray);
                }

                return '-';
            })
            ->addColumn('seoChecklist', function ($val) use ($seoStatus, $auth) {
                $historyImg = asset('images/history.png');
                $editUrl = route('seo.content.edit', $val->id);
                $updateUrl = route('seo.content.update', $val->id);
                $iconUrl = asset('images/new.png');
                $checkList = "<select data-id='{$val->id}' data-url='{$updateUrl}' data-type='seo' class='form-control statusSelect'>";
                $checkList .= "<option value=''>-- SELECT --</opiton>";
                foreach ($seoStatus as $status) {
                    if ($status->type == 'seo_approval') {
                        $checkList .= "<option value='{$status->id}'".($val->seo_status_id == $status->id ? 'selected' : '')." >{$status->label}</option>";
                    }
                }
                $checkList .= '</select>';
                $checkList .= "<div class='d-flex mt-2'>";
                $checkList .= '<span>';
                $checkList .= "<button type='button' data-type='seo' data-url='{$editUrl}' class='btn btn-image search ui-autocomplete-input checkListBtn'>";
                $checkList .= "<img src='$iconUrl' style='width:20px !important' />";
                $checkList .= '</button>';
                $checkList .= '</span>';
                if ($auth->hasRole(['Admin'])) {
                    $checkList .= '<span>';
                    $checkList .= "<button type='button' data-type='seo' data-url='{$editUrl}' class='btn btn-image search ui-autocomplete-input statusHistoryBtn'>";
                    $checkList .= "<img src='$historyImg' style='width:30px !important' />";
                    $checkList .= '</button>';
                    $checkList .= '</span>';
                }
                $checkList .= '</div>';

                return $checkList;
            })
            ->addColumn('publishChecklist', function ($val) use ($seoStatus, $auth) {
                $historyImg = asset('images/history.png');
                $editUrl = route('seo.content.edit', $val->id);
                $updateUrl = route('seo.content.update', $val->id);
                $iconUrl = asset('images/new.png');
                $checkList = "<select data-id='{$val->id}' data-url='{$updateUrl}' data-type='publish' class='form-control statusSelect'>";
                $checkList .= "<option value=''>-- SELECT --</opiton>";
                foreach ($seoStatus as $status) {
                    if ($status->type == 'publish') {
                        $checkList .= "<option value='{$status->id}'".($val->publish_status_id == $status->id ? 'selected' : '')." >{$status->label}</option>";
                    }
                }
                $checkList .= '</select>';
                $checkList .= "<div class='d-flex mt-2'>";
                $checkList .= '<span>';
                $checkList .= "<button type='button' data-type='publish' data-url='{$editUrl}' class='btn btn-image search ui-autocomplete-input checkListBtn'>";
                $checkList .= "<img src='$iconUrl' style='width:20px !important' />";
                $checkList .= '</button>';
                $checkList .= '</span>';
                if ($auth->hasRole(['Admin'])) {
                    $checkList .= '<span>';
                    $checkList .= "<button type='button' data-type='publish' data-url='{$editUrl}' class='btn btn-image search ui-autocomplete-input statusHistoryBtn'>";
                    $checkList .= "<img src='$historyImg' style='width:30px !important' />";
                    $checkList .= '</button>';
                    $checkList .= '</span>';
                }
                $checkList .= '</div>';

                return $checkList;
            })
            ->addColumn('documentLink', function ($val) {
                return "<a target='_blank' href='{$val->google_doc_link}'>{$val->google_doc_link}</a>";
            })
            ->addColumn('liveStatusLink', function ($val) {
                return "<a target='_blank' href='{$val->live_status_link}'>{$val->live_status_link}</a>";
            })
            ->editColumn('price', function ($val) use ($auth) {
                $price = "<b>{$val->price}</b>";
                if ($auth->hasRole(['Admin'])) {
                    if ($val->is_price_approved) {
                        $price .= '<br> <i>Approved</i>';
                    }
                    $historyImg = asset('images/history.png');
                    $price .= "<button class='btn btn-image search ui-autocomplete-input priceHistoryBtn' data-type='price' data-id='{$val->id}' style='cursor: default'>";
                    $price .= "<img src='{$historyImg}' style='width:30px !important' />";
                    $price .= '</button>';
                }

                return $price;
            })
            ->addColumn('seoStatus', function ($val) {
                return $val->seoStatus->label ?? '-';
            })
            ->addColumn('status_color', function ($val) {
                $statusColor = SeoStatus::where('status_alias', $val->status)->first();

                if (! empty($statusColor)) {
                    return $statusColor['status_color'];
                } else {
                    return '';
                }
            })
            ->rawColumns(['actions', 'status', 'keywords', 'seoChecklist', 'publishChecklist', 'documentLink', 'liveStatusLink', 'price', 'user_id'])
            ->addIndexColumn()
            ->make();

        return $datatable;
    }

    private function addPriceHistory(SeoProcess $seoProcess, bool $isUpdate)
    {
        $request = request();
        if ($request->price != $seoProcess->price || $isUpdate) {
            SeoHistory::create([
                'user_id' => auth()->id(),
                'type' => 'price',
                'seo_process_id' => $seoProcess->id,
                'message' => $request->price,
            ]);
        }
    }

    private function addUserHistory(SeoProcess $seoProcess, bool $isUpdate)
    {
        $request = request();
        if ($request->user_id != $seoProcess->user_id || $isUpdate) {
            SeoHistory::create([
                'user_id' => auth()->id(),
                'type' => 'user',
                'seo_process_id' => $seoProcess->id,
                'message' => $request->user_id,
            ]);
        }
    }

    private function renderPriceHistory()
    {
        $request = request();
        $data['seoHistory'] = SeoHistory::query()->where('seo_process_id', $request->seoProcessId)
            ->where('type', $request->seoType)->get();
        $data['seoType'] = $request->seoType;
        $html = view("$this->view/ajax/history", $data)->render();

        return response()->json([
            'success' => true,
            'data' => $html,
            'title' => ucfirst($request->seoType).' History',
        ]);
    }

    private function checklistForm(SeoProcess $seoProcess)
    {
        $request = request();
        $data['seoProcess'] = $seoProcess;
        $data['actionUrl'] = route('seo.content.update', $seoProcess->id);
        $data['statusType'] = $request->checklistType;
        $data['checkList'] = [];
        $data['checkListLabels'] = [];
        $title = '';
        if ($request->checklistType == 'seo') {
            $data['checkList'] = $seoProcess->seoChecklist;
            $data['checkListLabels'] = config('site.seo_content.seo_checklist');
            $title = 'SEO Checklist';
        } elseif ($request->checklistType == 'publish') {
            $data['checkList'] = $seoProcess->publishChecklist;
            $data['checkListLabels'] = config('site.seo_content.publish_checklist');
            $title = 'Publish Checklist';
        }

        $html = view("$this->view/ajax/checklist-form", $data)->render();

        return response()->json([
            'success' => true,
            'data' => $html,
            'title' => $title,
        ]);
    }

    private function changeStatus(SeoProcess $seoProcess)
    {
        $request = request();
        if ($request->statusType == 'seo') {
            $seoProcess->update([
                'seo_status_id' => $request->statusId,
            ]);
            $this->addStatusHistroy($seoProcess);
        } elseif ($request->statusType == 'publish') {
            $seoProcess->update([
                'publish_status_id' => $request->statusId,
            ]);
            $this->addStatusHistroy($seoProcess);
        }

        return response()->json([
            'success' => true,
        ]);
    }

    private function checklistHistory(SeoProcess $seoProcess)
    {
        $request = request();
        $data['seoProcess'] = $seoProcess;
        $data['checklistHistory'] = SeoProcessChecklistHistory::where('seo_process_id', $seoProcess->id)
            ->where('field_name', $request->field_name)->get();
        $html = view('seo.content.ajax.checklist-history', $data)->render();

        return response()->json([
            'success' => true,
            'data' => $html,
        ]);
    }

    private function addStatusHistroy(SeoProcess $seoProcess)
    {
        $request = request();
        SeoProcessStatusHistory::create([
            'user_id' => auth()->id(),
            'seo_process_id' => $seoProcess->id,
            'type' => $request->statusType,
            'seo_process_status_id' => $request->statusId,
        ]);
    }

    private function statusHistory(SeoProcess $seoProcess)
    {
        $request = request();
        $data['statusHistory'] = SeoProcessStatusHistory::where('seo_process_id', $seoProcess->id)
            ->where('type', $request->statusType)->get();
        $html = view('seo.content.ajax.status-history', $data)->render();

        return response()->json([
            'success' => true,
            'data' => $html,
        ]);
    }
}
