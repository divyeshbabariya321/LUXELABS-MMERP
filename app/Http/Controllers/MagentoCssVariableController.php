<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreMagentoCssVariableRequest;
use App\Http\Requests\UpdateMagentoCssVariableRequest;
use App\MagentoCssVariableJobLog;
use App\MagentoCssVariableValueHistory;
use App\MagentoCssVariableVerifyHistory;
use App\Models\MagentoCssVariable;
use App\Models\Project;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class MagentoCssVariableController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $magentoCssVariables = MagentoCssVariable::latest('id');

        if ($request->keyword) {
            $magentoCssVariables = $magentoCssVariables->where(function ($q) use ($request) {
                $q = $q->orWhere('value', 'LIKE', '%'.$request->keyword.'%')
                    ->orWhere('filename', 'LIKE', '%'.$request->keyword.'%');
            });
        }

        $search_project_id = $request->get('search_project_id');
        if ($search_project_id) {
            $magentoCssVariables = $magentoCssVariables->where('project_id', $search_project_id);
        }

        $search_file_path = $request->get('search_file_path');
        if ($search_file_path) {
            $magentoCssVariables = $magentoCssVariables->where('file_path', 'LIKE', '%'.$search_file_path.'%');
        }

        $search_variable = $request->get('search_variable');
        if ($search_variable) {
            $magentoCssVariables = $magentoCssVariables->where('variable', 'LIKE', '%'.$search_variable.'%');
        }

        $search_verify = $request->get('verify');
        if ($search_verify == 'NotVerify') {
            $magentoCssVariables = $magentoCssVariables->doesntHave('lastLog');
        } elseif ($search_verify == 'Error' || $search_verify == 'Success') {
            $magentoCssVariables = $magentoCssVariables->whereHas('lastLog', function ($magentoCssVariables) use ($search_verify) {
                $magentoCssVariables->where('status', 'LIKE', '%'.$search_verify.'%');
            });
        }

        $magentoCssVariables = $magentoCssVariables->paginate(50);

        $projects = Project::get()->pluck('name', 'id');
        $file_paths = MagentoCssVariable::groupBy('file_path')
            ->orderBy('file_path')
            ->select(DB::raw('TRIM(file_path) as trimmed_file_path'))
            ->pluck('trimmed_file_path')
            ->toArray();

        $variables = MagentoCssVariable::groupBy('variable')
            ->orderBy('variable')
            ->select(DB::raw('TRIM(variable) as trimmed_variable'))
            ->pluck('trimmed_variable')
            ->toArray();

        return view('magento-css-variable.index', compact('magentoCssVariables', 'projects', 'file_paths', 'variables'));
    }

    public function store(StoreMagentoCssVariableRequest $request): JsonResponse
    {
        // Validation Part

        $data = $request->except('_token');

        // Save
        $magentoCssVariable = new MagentoCssVariable;
        $magentoCssVariable->project_id = $data['project_id'];
        $magentoCssVariable->filename = $data['filename'];
        $magentoCssVariable->file_path = $data['file_path'];
        $magentoCssVariable->variable = $data['variable'];
        $magentoCssVariable->value = $data['value'];
        $magentoCssVariable->create_by = Auth::user()->id;
        $magentoCssVariable->save();

        $action = 'Add';
        $projectName = $magentoCssVariable->project->name;
        $filepath = $data['file_path'];
        $magentoCssVariableId = $magentoCssVariable->id;

        $command = 'bash '.getenv('DEPLOYMENT_SCRIPTS_PATH').'magento-css-variables.sh '.'-a '.$action.' -p '.$projectName.' -f '.$filepath.' -k '.$data['variable'].' -v '.$data['value'];

        Log::info('Start Magento Css Variable Update Vaule');

        exec($command, $output, $return_var);

        Log::info('command:'.$command);
        Log::info('output:'.print_r($output, true));
        Log::info('return_var:'.$return_var);

        if (! isset($output[0])) {
            MagentoCssVariableJobLog::create([
                'command' => $command,
                'message' => json_encode($output),
                'status' => 'Error',
                'csv_file_path' => $filepath,
                'magento_css_variable_id' => $magentoCssVariableId,
            ]);

            return response()->json(['code' => 500, 'message' => 'The script response is not found!']);
        }
        $response = json_decode($output[0]);
        if (isset($response->status) && ($response->status == 'true' || $response->status)) {
            $message = 'Magento CSS variable created successfully';
            if (isset($response->message) && $response->message != '') {
                $message = $response->message;
            }
            MagentoCssVariableJobLog::create([
                'command' => $command,
                'message' => json_encode($output),
                'status' => 'Success',
                'csv_file_path' => $filepath,
                'magento_css_variable_id' => $magentoCssVariableId,
            ]);

            return response()->json(['code' => 200, 'data' => [], 'message' => $message]);
        } else {
            $message = 'Something Went Wrong! Please check Logs for more details';
            if (isset($response->message) && $response->message != '') {
                $message = $response->message;
            }
            MagentoCssVariableJobLog::create([
                'command' => $command,
                'message' => json_encode($output),
                'status' => 'Error',
                'csv_file_path' => $filepath,
                'magento_css_variable_id' => $magentoCssVariableId,
            ]);

            return response()->json(
                [
                    'code' => 500,
                    'data' => [],
                    'message' => $message,
                ]
            );
        }
    }

    public function edit(Request $request, $id): JsonResponse
    {
        $magentoCssVariable = MagentoCssVariable::where('id', $id)->first();

        if ($magentoCssVariable) {
            return response()->json(['code' => 200, 'data' => $magentoCssVariable]);
        }

        return response()->json(['code' => 500, 'error' => 'Id is wrong!']);
    }

    public function update(UpdateMagentoCssVariableRequest $request, $id): JsonResponse
    {
        // Validation Part

        $data = $request->except('_token');

        $magentoCssVariable = MagentoCssVariable::where('id', $id)->firstOrFail();

        $oldValue = $magentoCssVariable->value;

        // Save
        $magentoCssVariable->project_id = $data['project_id'];
        $magentoCssVariable->filename = $data['filename'];
        $magentoCssVariable->file_path = $data['file_path'];
        $magentoCssVariable->variable = $data['variable'];
        $magentoCssVariable->value = $data['value'];
        $magentoCssVariable->save();

        // Maintain history here
        if ($oldValue != $magentoCssVariable->value) {
            // If value change then is_verified shoule be 0
            $magentoCssVariable->is_verified = 0;
            $magentoCssVariable->save();

            $history = new MagentoCssVariableValueHistory;
            $history->magento_css_variable_id = $magentoCssVariable->id;
            $history->old_value = $oldValue;
            $history->new_value = $magentoCssVariable->value;
            $history->user_id = Auth::user()->id;
            $history->save();
        }

        $action = 'update';
        $projectName = $magentoCssVariable->project->name;
        $filepath = $data['file_path'];
        $magentoCssVariableId = $magentoCssVariable->id;

        $command = 'bash '.getenv('DEPLOYMENT_SCRIPTS_PATH').'magento-css-variables.sh '.'-a '.$action.' -p '.$projectName.' -f '.$filepath.' -k '.$data['variable'].' -v '.$data['value'];

        Log::info('Start Magento Css Variable Update Vaule');

        exec($command, $output, $return_var);

        Log::info('command:'.$command);
        Log::info('output:'.print_r($output, true));
        Log::info('return_var:'.$return_var);

        if (! isset($output[0])) {
            MagentoCssVariableJobLog::create([
                'command' => $command,
                'message' => json_encode($output),
                'status' => 'Error',
                'csv_file_path' => $filepath,
                'magento_css_variable_id' => $magentoCssVariableId,
            ]);

            return response()->json(['code' => 500, 'message' => 'The script response is not found!']);
        }
        $response = json_decode($output[0]);
        if (isset($response->status) && ($response->status == 'true' || $response->status)) {
            $message = 'Magento CSS variable updated successfully!';
            if (isset($response->message) && $response->message != '') {
                $message = $response->message;
            }
            MagentoCssVariableJobLog::create([
                'command' => $command,
                'message' => json_encode($output),
                'status' => 'Success',
                'csv_file_path' => $filepath,
                'magento_css_variable_id' => $magentoCssVariableId,
            ]);

            return response()->json(['code' => 200, 'data' => [], 'message' => $message]);
        } else {
            $message = 'Something Went Wrong! Please check Logs for more details';
            if (isset($response->message) && $response->message != '') {
                $message = $response->message;
            }
            MagentoCssVariableJobLog::create([
                'command' => $command,
                'message' => json_encode($output),
                'status' => 'Error',
                'csv_file_path' => $filepath,
                'magento_css_variable_id' => $magentoCssVariableId,
            ]);

            return response()->json(
                [
                    'code' => 500,
                    'data' => [],
                    'message' => $message,
                ]
            );
        }
    }

    public function destroy($id): RedirectResponse
    {
        $magentoCssVariable = MagentoCssVariable::findOrFail($id);

        $action = 'delete';
        $projectName = $magentoCssVariable->project->name;
        $filepath = $magentoCssVariable->file_path;
        $magentoCssVariableId = $magentoCssVariable->id;
        $variable = $magentoCssVariable->variable;
        $value = $magentoCssVariable->value;

        $magentoCssVariable->delete();

        $command = 'bash '.getenv('DEPLOYMENT_SCRIPTS_PATH').'magento-css-variables.sh '.'-a '.$action.' -p '.$projectName.' -f '.$filepath.' -k '.$variable.' -v '.$value;

        Log::info('Start Magento Css Variable Update Vaule');

        exec($command, $output, $return_var);

        Log::info('command:'.$command);
        Log::info('output:'.print_r($output, true));
        Log::info('return_var:'.$return_var);

        if (! isset($output[0])) {
            MagentoCssVariableJobLog::create([
                'command' => $command,
                'message' => json_encode($output),
                'status' => 'Error',
                'csv_file_path' => $filepath,
                'magento_css_variable_id' => $magentoCssVariableId,
            ]);
        }
        if ($output) {
            $response = json_decode($output[0]);
            if (isset($response->status) && ($response->status == 'true' || $response->status)) {
                if (isset($response->message) && $response->message != '') {
                    $message = $response->message;
                }
                MagentoCssVariableJobLog::create([
                    'command' => $command,
                    'message' => json_encode($output),
                    'status' => 'Success',
                    'csv_file_path' => $filepath,
                    'magento_css_variable_id' => $magentoCssVariableId,
                ]);
            } else {
                if (isset($response->message) && $response->message != '') {
                    $message = $response->message;
                }
                MagentoCssVariableJobLog::create([
                    'command' => $command,
                    'message' => json_encode($output),
                    'status' => 'Error',
                    'csv_file_path' => $filepath,
                    'magento_css_variable_id' => $magentoCssVariableId,
                ]);
            }
        }

        return redirect()->route('magento-css-variable.index')
            ->with('success', 'Magento CSS variable deleted successfully');
    }

    public function verify(Request $request, $id): JsonResponse
    {
        $magentoCssVariable = MagentoCssVariable::findOrFail($id);
        $magentoCssVariable->is_verified = $request->verfied;
        $magentoCssVariable->save();

        // Maintain history here
        $history = new MagentoCssVariableVerifyHistory;
        $history->magento_css_variable_id = $magentoCssVariable->id;
        $history->value = $magentoCssVariable->value;
        $history->is_verified = $request->verfied;
        $history->user_id = Auth::user()->id;
        $history->save();

        return response()->json(['code' => 500, 'message' => 'Verifed status Update Successfully!']);
    }

    public function verifyHistories($id): JsonResponse
    {
        $datas = MagentoCssVariableVerifyHistory::with(['user'])
            ->where('magento_css_variable_id', $id)
            ->latest()
            ->get();

        return response()->json([
            'status' => true,
            'data' => $datas,
            'message' => 'History get successfully',
            'status_name' => 'success',
        ], 200);
    }

    public function valueHistories($id): JsonResponse
    {
        $datas = MagentoCssVariableValueHistory::with(['user'])
            ->where('magento_css_variable_id', $id)
            ->latest()
            ->get();

        return response()->json([
            'status' => true,
            'data' => $datas,
            'message' => 'History get successfully',
            'status_name' => 'success',
        ], 200);
    }

    public function jobLogs($id): JsonResponse
    {
        $datas = MagentoCssVariableJobLog::where('magento_css_variable_id', $id)
            ->latest()
            ->get();

        return response()->json([
            'status' => true,
            'data' => $datas,
            'message' => 'History get successfully',
            'status_name' => 'success',
        ], 200);
    }

    public function logs(Request $request): View
    {
        $magentoCssVariableJobLogs = new MagentoCssVariableJobLog;

        $project = $request->search_project;
        $error = $request->search_error;
        $message = $request->search_message;
        $command = $request->search_command;
        $date = $request->date;

        if ($project) {
            $magentoCssVariableJobLogs = MagentoCssVariableJobLog::whereHas('magentoCssVariable.project', function ($query) use ($project) {
                $query->where('name', 'LIKE', '%'.$project.'%');
            });
        }
        if ($error) {
            $magentoCssVariableJobLogs = $magentoCssVariableJobLogs->where('status', 'LIKE', '%'.$error.'%');
        }
        if ($message) {
            $magentoCssVariableJobLogs = $magentoCssVariableJobLogs->where('message', 'LIKE', '%'.$message.'%');
        }
        if ($command) {
            $magentoCssVariableJobLogs = $magentoCssVariableJobLogs->where('command', 'LIKE', '%'.$command.'%');
        }
        if ($date) {
            $magentoCssVariableJobLogs = $magentoCssVariableJobLogs->where('created_at', 'LIKE', '%'.$date.'%');
        }

        $magentoCssVariableJobLogs = $magentoCssVariableJobLogs->latest('id')->paginate(50);

        return view('magento-css-variable.logs', compact('magentoCssVariableJobLogs', 'project', 'error', 'message', 'command', 'date'));
    }

    public function updateValue(Request $request): JsonResponse
    {
        $id = $request->id;
        $magentoCssVariable = MagentoCssVariable::where('id', $id)->first();
        if (! $magentoCssVariable) {
            return response()->json(['code' => 500, 'message' => 'Variable data is not found!']);
        }
        $project_name = $magentoCssVariable->project?->name;
        $filepath = $magentoCssVariable->file_path;
        $key = $magentoCssVariable->variable;
        $oldValue = $magentoCssVariable->value;
        $value = $request->value;
        // Update new value in DB
        $magentoCssVariable->value = $value;
        if ($oldValue != $value) {
            // If value change then is_verified shoule be 0
            $magentoCssVariable->is_verified = 0;
        }
        $magentoCssVariable->save();

        // Maintain history here
        $history = new MagentoCssVariableValueHistory;
        $history->magento_css_variable_id = $magentoCssVariable->id;
        $history->old_value = $oldValue;
        $history->new_value = $value;
        $history->user_id = Auth::user()->id;
        $history->save();

        $cmd = 'bash '.getenv('DEPLOYMENT_SCRIPTS_PATH').'magento-cssvariable-update.sh -p "'.$project_name.'" -f "'.$filepath.'" -k "'.$key.'" -v "'.$value.'" 2>&1';

        Log::info('Start Magento Css Variable Update Vaule');

        exec($cmd, $output, $return_var);

        Log::info('command:'.$cmd);
        Log::info('output:'.print_r($output, true));
        Log::info('return_var:'.$return_var);

        Log::info('End Magento Css Variable Update Vaule');
        if (! isset($output[0])) {
            // Maintain Error Log here in new table.
            MagentoCssVariableJobLog::create([
                'magento_css_variable_id' => $magentoCssVariable->id,
                'command' => $cmd,
                'message' => json_encode($output),
                'status' => 'Error',
            ]);

            return response()->json(['code' => 500, 'message' => 'The response is not found!']);
        }
        $response = json_decode($output[0]);
        if (isset($response->status) && ($response->status == 'true' || $response->status)) {
            $message = 'Variable updated';
            if (isset($response->message) && $response->message != '') {
                $message = $response->message;
            }
            // Maintain Success Log here in new table.
            MagentoCssVariableJobLog::create([
                'magento_css_variable_id' => $magentoCssVariable->id,
                'command' => $cmd,
                'message' => json_encode($output),
                'status' => 'Success',
            ]);

            return response()->json(['code' => 200, 'message' => $message]);
        } else {
            $message = 'Something Went Wrong! Please check Logs for more details';
            if (isset($response->message) && $response->message != '') {
                $message = $response->message;
            }
            // Maintain Error Log here in new table.
            MagentoCssVariableJobLog::create([
                'magento_css_variable_id' => $magentoCssVariable->id,
                'command' => $cmd,
                'message' => json_encode($output),
                'status' => 'Error',
            ]);

            return response()->json(['code' => 500, 'message' => $message]);
        }
    }

    // Function to format a value for CSV, adding quotes if necessary
    public function formatForCSV($value)
    {
        // If the value contains a comma or a double quote, enclose it in double quotes and escape existing double quotes.
        if (strpos($value, ',') !== false || strpos($value, '"') !== false) {
            return '"'.str_replace('"', '""', $value).'"';
        }

        return $value;
    }

    public function updateSelectedValues(Request $request): JsonResponse
    {
        if ($request->has('selectedIds') && $request->selectedIds != '') {
            $selectedIds = $request->selectedIds;
            $magentoCssVariables = MagentoCssVariable::whereIn('id', $selectedIds)->get();
            // Create a new CSV file content
            $csvContent = '"Project","variable","value","filepath"'."\n";
            foreach ($magentoCssVariables as $row) {
                // Adjust this according to your data structure
                $csvContent .= $this->formatForCSV($row->project->name).','
                    .$this->formatForCSV($row->variable).','
                    .$this->formatForCSV($row->value).','
                    .$this->formatForCSV($row->file_path)."\n";
            }

            // Set the file path where the CSV will be stored
            $filePath = 'magento-css-variable-csv/file_'.time().'.csv';

            Storage::disk('public')->put($filePath, $csvContent);

            // Get the path to the stored CSV file
            $fullFilePath = Storage::disk('public')->path($filePath);

            $action = 'bulk';
            $cmd = 'bash '.getenv('DEPLOYMENT_SCRIPTS_PATH').'magento-css-variables.sh '.'-a '.$action.' -f '.$fullFilePath.' -B '.$fullFilePath;

            Log::info('Start Magento Css Variable Update Vaule');

            exec($cmd, $output, $return_var);

            Log::info('command:'.$cmd);
            Log::info('output:'.print_r($output, true));
            Log::info('return_var:'.$return_var);

            Log::info('End Magento Css Variable Update Vaule');
            if (! isset($output[0])) {
                $selectedIds = implode(',', $selectedIds);
                // Maintain Error Log here in new table.
                // ToDo: How to maintain log here ?
                MagentoCssVariableJobLog::create([
                    'command' => $cmd,
                    'message' => json_encode($output),
                    'status' => 'Error',
                    'csv_file_path' => $fullFilePath,
                    'magento_css_variable_id' => $selectedIds,
                ]);

                return response()->json(['code' => 500, 'message' => 'The response is not found!']);
            }
            $response = json_decode($output[0]);
            if (isset($response->status) && ($response->status == 'true' || $response->status)) {
                $message = 'Variable updated';
                if (isset($response->message) && $response->message != '') {
                    $message = $response->message;
                }
                $selectedIds = implode(',', $selectedIds);
                // Maintain Error Log here in new table.
                // ToDo: How to maintain log here ?
                MagentoCssVariableJobLog::create([
                    'command' => $cmd,
                    'message' => json_encode($output),
                    'status' => 'Success',
                    'csv_file_path' => $fullFilePath,
                    'magento_css_variable_id' => $selectedIds,
                ]);

                return response()->json(['code' => 200, 'message' => $message]);
            } else {
                $message = 'Something Went Wrong! Please check Logs for more details';
                if (isset($response->message) && $response->message != '') {
                    $message = $response->message;
                }
                $selectedIds = implode(',', $selectedIds);
                // Maintain Error Log here in new table.
                // ToDo: How to maintain log here ?
                MagentoCssVariableJobLog::create([
                    'command' => $cmd,
                    'message' => json_encode($output),
                    'status' => 'Error',
                    'csv_file_path' => $fullFilePath,
                    'magento_css_variable_id' => $selectedIds,
                ]);

                return response()->json(['code' => 500, 'message' => $message]);
            }
        }

        return response()->json(['code' => 500, 'message' => 'Please select the row']);
    }

    public function updateValuesForProject(Request $request): RedirectResponse
    {
        if ($request->has('project_id') && $request->project_id != '') {
            $projectId = $request->project_id;
            $magentoCssVariables = MagentoCssVariable::where('project_id', $projectId)->get();

            // CSV concept (NEW)
            // Create a new CSV file content
            $csvContent = '"Project","variable","value","filepath"'."\n";
            foreach ($magentoCssVariables as $row) {
                // Adjust this according to your data structure
                $csvContent .= $this->formatForCSV($row->project->name).','
                    .$this->formatForCSV($row->variable).','
                    .$this->formatForCSV($row->value).','
                    .$this->formatForCSV($row->file_path)."\n";
            }

            // Set the file path where the CSV will be stored
            $filePath = 'magento-css-variable-csv/file_'.time().'.csv';

            Storage::disk('public')->put($filePath, $csvContent);

            // Get the path to the stored CSV file
            $fullFilePath = Storage::disk('public')->path($filePath);

            $action = 'signleUpdate';
            $cmd = 'bash '.getenv('DEPLOYMENT_SCRIPTS_PATH').'magento-css-variables.sh '.'-a '.$action.' -f '.$fullFilePath.' -B '.$fullFilePath;

            Log::info('Start Magento Css Variable Update Vaule');

            exec($cmd, $output, $return_var);

            Log::info('command:'.$cmd);
            Log::info('output:'.print_r($output, true));
            Log::info('return_var:'.$return_var);

            Log::info('End Magento Css Variable Update Vaule');
            if (! isset($output[0])) {
                // Maintain Error Log here in new table.
                // ToDo: How to maintain log here ?
                MagentoCssVariableJobLog::create([
                    'command' => $cmd,
                    'message' => json_encode($output),
                    'status' => 'Error',
                    'csv_file_path' => $fullFilePath,
                ]);

                return redirect()->route('magento-css-variable.index')->with('error', 'The response is not found!');
            }
            $response = json_decode($output[0]);
            if (isset($response->status) && ($response->status == 'true' || $response->status)) {
                $message = 'Variable updated';
                if (isset($response->message) && $response->message != '') {
                    $message = $response->message;
                }
                // Maintain Error Log here in new table.
                // ToDo: How to maintain log here ?
                MagentoCssVariableJobLog::create([
                    'command' => $cmd,
                    'message' => json_encode($output),
                    'status' => 'Success',
                    'csv_file_path' => $fullFilePath,
                ]);

                return redirect()->route('magento-css-variable.index')->with('success', $message);
            } else {
                $message = 'Something Went Wrong! Please check Logs for more details';
                if (isset($response->message) && $response->message != '') {
                    $message = $response->message;
                }
                // Maintain Error Log here in new table.
                // ToDo: How to maintain log here ?
                MagentoCssVariableJobLog::create([
                    'command' => $cmd,
                    'message' => json_encode($output),
                    'status' => 'Error',
                    'csv_file_path' => $fullFilePath,
                ]);

                return redirect()->route('magento-css-variable.index')->with('error', $message);
            }
        }

        return redirect()->route('magento-css-variable.index')->with('error', 'Please select the project!');
    }

    public function download($id): BinaryFileResponse
    {
        $fileName = MagentoCssVariableJobLog::find($id);

        $file_name = basename($fileName->csv_file_path);

        $filePath = storage_path('app/public/magento-css-variable-csv/'.$file_name);

        if (file_exists($filePath)) {
            return Response::download($filePath);
        } else {
            abort(404, 'The file you are trying to download does not exist.');
        }
    }

    public function updateSelectedVerified(Request $request): JsonResponse
    {
        if ($request->has('selectedIds') && $request->selectedIds != '') {
            foreach ($request->selectedIds as $selectId) {
                $magentoCssVariable = MagentoCssVariable::findOrFail($selectId);
                $magentoCssVariable->is_verified = 1;
                $magentoCssVariable->save();

                // Maintain history here
                $history = new MagentoCssVariableVerifyHistory;
                $history->magento_css_variable_id = $magentoCssVariable->id;
                $history->value = $magentoCssVariable->value;
                $history->is_verified = 1;
                $history->user_id = Auth::user()->id;
                $history->save();
            }

            return response()->json(['code' => 500, 'message' => 'Verifed status Update Successfully!']);
        }
    }

    public function syncVariables(Request $request): JsonResponse
    {
        $action = $request->sync;
        $command = 'bash '.getenv('DEPLOYMENT_SCRIPTS_PATH').'magento-css-variables.sh '.'-a '.$action;

        Log::info('Start Magento Css Variable Update Vaule');

        $result = exec($command, $output, $return_var);

        Log::info('command:'.$command);
        Log::info('output:'.print_r($output, true));
        Log::info('return_var:'.$return_var);

        Log::info('End Magento Css Variable Update Vaule');
        if (! isset($output[0])) {
            // Maintain Error Log here in new table.
            MagentoCssVariableJobLog::create([
                'command' => $command,
                'message' => json_encode($output),
                'status' => 'Error',
            ]);

            return response()->json(['code' => 500, 'message' => 'The script response is not found!']);
        }
        $response = json_decode($output[0]);
        if (isset($response->status) && ($response->status == 'true' || $response->status)) {
            $message = 'Variable updated';
            if (isset($response->message) && $response->message != '') {
                $message = $response->message;
            }
            // Maintain Success Log here in new table.
            MagentoCssVariableJobLog::create([
                'command' => $command,
                'message' => json_encode($output),
                'status' => 'Success',
            ]);

            return response()->json(['code' => 200, 'message' => $message]);
        } else {
            $message = 'Something Went Wrong! Please check Logs for more details';
            if (isset($response->message) && $response->message != '') {
                $message = $response->message;
            }
            // Maintain Error Log here in new table.
            MagentoCssVariableJobLog::create([
                'command' => $command,
                'message' => json_encode($output),
                'status' => 'Error',
            ]);

            return response()->json(['code' => 500, 'message' => $message]);
        }
    }
}
