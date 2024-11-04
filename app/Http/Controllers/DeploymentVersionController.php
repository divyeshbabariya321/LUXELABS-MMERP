<?php

namespace App\Http\Controllers;

use App\Models\DeploymentVersion;
use App\Models\DeploymentVersionLog;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\View\View;

class DeploymentVersionController extends Controller
{
    public function listDeploymentVersion(Request $request): View
    {
        $deploymentVersions = new DeploymentVersion;

        if ($request->search_versionNumber) {
            $deploymentVersions = $deploymentVersions->where('version_number', 'LIKE', '%'.$request->search_versionNumber.'%');
        }
        if ($request->search_jobName) {
            $deploymentVersions = $deploymentVersions->where('job_name', 'LIKE', '%'.$request->search_jobName.'%');
        }
        if ($request->search_branchName) {
            $deploymentVersions = $deploymentVersions->where('branch_name', 'LIKE', '%'.$request->search_branchName.'%');
        }
        if ($request->deployment_date) {
            $deploymentVersions = $deploymentVersions->where('deployment_date', 'LIKE', '%'.$request->deployment_date.'%');
        }
        if ($request->pr_date) {
            $deploymentVersions = $deploymentVersions->where('pr_date', 'LIKE', '%'.$request->pr_date.'%');
        }
        $deploymentVersions = $deploymentVersions->latest()->paginate(25);

        $search_versionNumber = $request->search_versionNumber;
        $search_jobName = $request->search_jobName;
        $search_branchName = $request->search_branchName;
        $deployment_date = $request->deployment_date;
        $pr_date = $request->pr_date;

        return view('deployment-versions.deployment-version-listing', compact('deploymentVersions', 'search_versionNumber', 'search_jobName', 'search_branchName', 'deployment_date', 'pr_date'));
    }

    public function deployVersion(Request $request): JsonResponse
    {
        $deploymentVersion = DeploymentVersion::find($request->deployVerId);
        $jobName = $deploymentVersion->job_name;
        $branch_name = $deploymentVersion->branch_name;
        $pullNo = $deploymentVersion->pull_no;
        $serverenv = $request->selectedValue;
        $user_id = auth()->user()->id;
        $revision = $deploymentVersion->revision;

        try {
            $jenkins = new \JenkinsKhan\Jenkins('https://apibuild:117ed14fbbe668b88696baa43d37c6fb48@build.theluxuryunlimited.com:8080');
            $jenkins->launchJob($jobName, ['branch_name' => $branch_name, 'serverenv' => $serverenv, 'revision' => $revision, 'pull_no' => $pullNo]);
            $buildDetail = 'Build Name: '.$jobName.'<br> Brance Name: '.$branch_name.'<br> Revision: '.$revision;
            $record = ['deployement_version_id' => $deploymentVersion->id, 'error_message' => $buildDetail, 'build_number' => $deploymentVersion->build_number, 'user_id' => $user_id];
            DeploymentVersionLog::create($record);

            return response()->json(['code' => 200, 'message' => 'Process builed complete successfully.']);
        } catch (Exception $e) {
            $record = ['deployement_version_id' => $deploymentVersion->id, 'user_id' => $user_id, 'error_message' => $e->getMessage(), 'build_number' => $deploymentVersion->build_number];
            DeploymentVersionLog::create($record);

            return response()->json(['code' => 500, 'message' => 'Please try again, Jenkins job not created']);
        }
    }

    public function deployVersionHistory($id): JsonResponse
    {
        $deployementVersionLogs = DeploymentVersionLog::with(['user', 'deployversion'])->where('deployement_version_id', $id)->latest()->get();

        return response()->json([
            'status' => true,
            'data' => $deployementVersionLogs,
            'message' => 'Logs show successfully',
            'status_name' => 'success',
        ], 200);
    }

    public function restoreRevision(Request $request): JsonResponse
    {
        $deploymentVersion = DeploymentVersion::find($request->deployVersionId);
        $jobName = $deploymentVersion->job_name;
        $branch_name = $deploymentVersion->branch_name;
        $user_id = auth()->user()->id;
        $revision = $deploymentVersion->revision;

        try {
            // Jenkins API URL
            $jenkinsApiUrl = 'https://build.theluxuryunlimited.com:8080';

            // Authentication credentials
            $username = 'apibuild';
            $apiToken = '117ed14fbbe668b88696baa43d37c6fb48';

            $parameters = [
                'revision' => $revision,
                'branch' => $branch_name,
            ];
            // Construct the URL for triggering the build
            $buildUrl = "$jenkinsApiUrl/job/$jobName/buildWithParameters";

            // Send POST request with authentication
            $response = Http::withBasicAuth($username, $apiToken)
                ->post($buildUrl, $parameters);

            if ($response->successful()) {
                return response()->json(['code' => 200, 'message' => 'Restore completed successfully.']);
            } else {
                $buildDetail = 'Build Name: '.$jobName.'<br> Brance Name: '.$branch_name.'<br> Revision: '.$revision;
                $record = ['deployement_version_id' => $deploymentVersion->id, 'error_message' => $buildDetail, 'build_number' => $deploymentVersion->build_number, 'user_id' => $user_id];
                DeploymentVersionLog::create($record);

                return response()->json(['code' => 500, 'message' => 'Failed To Restore']);
            }
        } catch (Exception $e) {
            $record = ['deployement_version_id' => $deploymentVersion->id, 'user_id' => $user_id, 'error_message' => $e->getMessage(), 'build_number' => $deploymentVersion->build_number];
            DeploymentVersionLog::create($record);

            return response()->json(['code' => 500, 'message' => 'Please try again, restore not Updated']);
        }
    }
}
