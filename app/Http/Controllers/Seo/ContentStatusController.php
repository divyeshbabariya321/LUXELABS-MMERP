<?php

namespace App\Http\Controllers\Seo;

use App\Http\Controllers\Controller;
use App\Models\Seo\SeoProcessStatus;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ContentStatusController extends Controller
{
    private $view = 'seo/content';

    public function index(Request $request)
    {
        $status = SeoProcessStatus::query()->orderByDesc('id');

        if (! empty($request->type)) {
            $status = $status->where('type', $request->type);
        }

        return datatables()->eloquent($status)
            ->addColumn('action', function ($val) {
                $action = '';
                $editUrl = route('seo.content-status.edit', $val->id);
                $action .= "<buttonc type='button' data-url='$editUrl' class='btn btn-secondary btn-sm editStatusBtn'>Edit</button>";

                return $action;
            })
            ->editColumn('created_at', function ($val) {
                return date('Y-m-d h:i A', strtotime($val->created_at));
            })
            ->editColumn('type', function ($val) {
                if ($val->type == 'seo_approval') {
                    return "<span class='badge badge-sm'>SEO</span>";
                } elseif ($val->type == 'publish') {
                    return "<span class='badge badge-sm'>Publish</span>";
                }
            })
            ->addIndexColumn()
            ->rawColumns(['action', 'type'])
            ->make(true);
    }

    public function create(): JsonResponse
    {
        $data['actionUrl'] = route('seo.content-status.store');
        $html = view("{$this->view}/ajax/status-form", $data)->render();

        return response()->json([
            'success' => true,
            'data' => $html,
            'title' => 'Add Status',
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        SeoProcessStatus::create([
            'label' => $request->label,
            'type' => $request->type,
        ]);

        return response()->json([
            'success' => true,
        ]);
    }

    public function edit(int $id): JsonResponse
    {
        $data['status'] = SeoProcessStatus::find($id);
        $data['actionUrl'] = route('seo.content-status.update', $id);
        $html = view("{$this->view}/ajax/status-form", $data)->render();

        return response()->json([
            'success' => true,
            'data' => $html,
            'title' => 'Edit Status',
        ]);
    }

    public function update(Request $request, int $id): JsonResponse
    {
        SeoProcessStatus::findOrFail($id)->update([
            'label' => $request->label,
            'type' => $request->type,
        ]);

        return response()->json([
            'success' => true,
        ]);
    }
}
