<?php

namespace App\Http\Controllers;

use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\JsonResponse;
use App\CodeShortcut;
use Illuminate\Http\Request;
use App\CodeShortCutPlatform;
use App\Models\MonitorJenkinsBuild;

class MonitorJenkinsBuildController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $keyword   = $request->get('keyword');
        $projectId = $request->get('project_id');
        $workerId  = $request->get('worker_id');

        $monitorJenkinsBuilds = new MonitorJenkinsBuild;

        if (! empty($keyword)) {
            $monitorJenkinsBuilds = $monitorJenkinsBuilds->where(function ($q) use ($keyword) {
                $q->orWhere('monitor_jenkins_builds.project', 'LIKE', '%' . $keyword . '%')
                    ->orWhere('monitor_jenkins_builds.worker', 'LIKE', '%' . $keyword . '%')
                    ->orWhere('monitor_jenkins_builds.store_id', 'LIKE', '%' . $keyword . '%')
                    ->orWhere('monitor_jenkins_builds.error', 'LIKE', '%' . $keyword . '%')
                    ->orWhere('monitor_jenkins_builds.build_number', 'LIKE', '%' . $keyword . '%');
            });
        }

        if ($projectId) {
            $monitorJenkinsBuilds = $monitorJenkinsBuilds->WhereIn('id', $projectId);
        }

        if ($workerId) {
            $monitorJenkinsBuilds = $monitorJenkinsBuilds->WhereIn('id', $workerId);
        }

        if ($request->get('id_sort_by')) {
            $monitorJenkinsBuilds = $monitorJenkinsBuilds->orderBy('id', $request->get('id_sort_by'));
        } else {
            $monitorJenkinsBuilds = $monitorJenkinsBuilds->orderByDesc('created_at');
        }

        $monitorJenkinsBuilds = $monitorJenkinsBuilds->paginate(10);

        return view('monitor.jenkins_build_index', compact('monitorJenkinsBuilds'));
    }

    public function list(Request $request): JsonResponse
    {
        // 1 = Failure, 0 = Success
        $perPage = 10; // Number of records per page

        $monitorJenkinsBuild = MonitorJenkinsBuild::where('clone_repository', 1)
            ->orwhere('lock_build', 1)
            ->orwhere('update_code', 1)
            ->orwhere('composer_install', 1)
            ->orwhere('make_config', 1)
            ->orwhere('setup_upgrade', 1)
            ->orwhere('compile_code', 1)
            ->orwhere('static_content', 1)
            ->orwhere('reindexes', 1)
            ->orwhere('magento_cache_flush', 1)
            ->orwhere('build_status', 1)
            ->orwhere('meta_update', 1);

        $monitorJenkinsBuild = $monitorJenkinsBuild->paginate($perPage);

        return response()->json($monitorJenkinsBuild);
    }

    public function truncateJenkinsbulids(Request $request): RedirectResponse
    {
        MonitorJenkinsBuild::truncate();

        return redirect()->route('monitor-jenkins-build.index')->withSuccess('data Removed succesfully!');
    }

    public function insertCodeShortcut(Request $request): JsonResponse
    {
        $monitorJenkinsBuild = MonitorJenkinsBuild::find($request->id);

        $checkAlredyExist = CodeShortcut::where('jenkins_log_id', $request->id)->first();

        if ($checkAlredyExist) {
            return response()->json(['code' => 200, 'message' => 'Alreday Insert Into CodeShortcut!!!']);
        } else {
            $platform   = CodeShortCutPlatform::firstOrCreate(['name' => 'jenkins']);
            $platformId = $platform->id;

            if ($monitorJenkinsBuild->error == 'NA') {
                $monitorJenkinsBuild->error = null;
            }

            $codeShortcut                             = new CodeShortcut();
            $codeShortcut->code_shortcuts_platform_id = $platformId;
            $codeShortcut->description                = $monitorJenkinsBuild->full_log;
            $codeShortcut->title                      = $monitorJenkinsBuild->error;
            $codeShortcut->website                    = $monitorJenkinsBuild->project;
            $codeShortcut->user_id                    = auth()->user()->id;
            $codeShortcut->jenkins_log_id             = $request->id;
            $codeShortcut->type                       = 'monitor-jenkins-build';
            $codeShortcut->save();

            return response()->json(['code' => 200, 'message' => 'CodeShortcut Insert successfully!!!']);
        }
    }
}
