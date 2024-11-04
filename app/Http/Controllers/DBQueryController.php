<?php

namespace App\Http\Controllers;

use App\CommandExecutionHistory;
use App\Jobs\CommandExecution;
use App\Setting;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class DBQueryController extends Controller
{
    public function index(): View
    {
        $user = Auth::user();
        $tables = DB::select('show tables');
        $table_array = [];
        foreach ($tables as $tab) {
            $table_array[] = array_values((array) $tab)[0];
        }

        //START - Purpose : Get Command List - DEVTASK-19941
        $command_list_arr = [];
        $i = 0;
        foreach (Artisan::all() as $command) {
            $command_list_arr[$i]['Name'] = $command->getName();
            $command_list_arr[$i]['Description'] = $command->getDescription();
            $i++;
        }

        //END - DEVTASK-19941

        return view('admin-menu.database-menu.db-query.index', compact('table_array', 'user', 'command_list_arr'));
    }

    //START - Purpose : Exicute Command - DEVTASK-19941
    public function command_execution(Request $request): JsonResponse
    {
        try {
            $manual_command_name = '';
            $command_name = '';

            if ($request->manual_command_name != '') {
                $manual_command_name = $request->manual_command_name;

                $params = [
                    'command_name' => $manual_command_name,
                    'user_id' => Auth::id(),
                    'status' => 0,
                ];
            } else {
                $command_name = $request->command_name;

                $params = [
                    'command_name' => $command_name,
                    'user_id' => Auth::id(),
                    'status' => 0,
                ];
            }

            $store = CommandExecutionHistory::create($params);

            $store_user_id = $store->user_id;
            $store_id = $store->id;

            CommandExecution::dispatch($command_name, $manual_command_name, $store_user_id, $store_id)->onQueue('command_execution');

            return response()->json(['code' => 200, 'data' => true]);
        } catch (Exception $e) {
            return response()->json(['code' => 400, 'error' => 'Opps! Something went wrong, Please try again.', 'data' => false], 400);

        }
    }

    public function command_execution_history(Request $request): View
    {
        try {
            $command_history = CommandExecutionHistory::join('users', 'command_execution_historys.user_id', 'users.id')
                ->orderByDesc('id')
                ->select('command_execution_historys.*', 'users.name as user_name')
                ->paginate(Setting::get('pagination'));

            return view('admin-menu.database-menu.db-query.command_history', compact('command_history', 'request'));
        } catch (Exception $e) {
            return response()->json(['code' => 400, 'error' => 'Opps! Something went wrong, Please try again.', 'data' => false], 400);
        }
    }

    public function ReportDownload(Request $request): BinaryFileResponse
    {
        $file_path = storage_path($request->file);

        return response()->download($file_path);
    }

    //END - DEVTASK-19941

    public function columns(Request $request): JsonResponse
    {
        $column_array = [];
        $columns = DB::select('DESCRIBE '.array_keys($request->all())[0].';');
        foreach ($columns as $col) {
            $column_array[] = $col;
        }

        return response()->json([
            'status' => true,
            'data' => $column_array,
        ]);
    }

    public function confirm(Request $request): JsonResponse
    {
        $sql_query = 'UPDATE '.$request->table_name.' SET ';

        $data = $request->all();
        $where_query_exist = 0;
        foreach ($data as $key => $val) {
            if (strpos($key, 'update_') !== false && in_array(str_replace('update_', '', $key), $request->columns)) {
                $sql_query .= str_replace('update_', '', $key).' = "'.$val.'", ';
            }
        }
        $sql_query .= ' WHERE ';
        $sql_query = str_replace(',  WHERE', ' WHERE', $sql_query);
        foreach ($data as $key => $val) {
            if (strpos($key, 'where_') !== false && $val !== null) {
                $key = str_replace('where_', '', $key);
                $sql_query .= $where_query_exist ? ' AND ' : '';
                $sql_query .= $key.' '.$request->criteriaColumnOperators["'".$key."'"].' "'.$val.'"';
                $where_query_exist = 1;
            }
        }
        ! $where_query_exist ? $sql_query .= ' = 1 ;' : $sql_query .= ' ;';

        return response()->json([
            'status' => true,
            'sql' => $sql_query,
            'data' => $request->all(),
        ]);
    }

    public function deleteConfirm(Request $request): JsonResponse
    {
        $sql_query = 'DELETE from '.$request->table_name;

        $data = $request->all();
        $where_query_exist = 0;
        $sql_query .= ' WHERE ';
        $sql_query = str_replace(',  WHERE', ' WHERE', $sql_query);
        foreach ($data as $key => $val) {
            if (strpos($key, 'where_') !== false && $val !== null) {
                $key = str_replace('where_', '', $key);
                $sql_query .= $where_query_exist ? ' AND ' : '';
                $sql_query .= $key.' '.$request->criteriaColumnOperators["'".$key."'"].' "'.$val.'"';
                $where_query_exist = 1;
            }
        }
        ! $where_query_exist ? $sql_query .= '1 ;' : $sql_query .= ' ;';

        return response()->json([
            'status' => true,
            'sql' => $sql_query,
            'data' => $request->all(),
        ]);
    }

    public function updateDBQuery(Request $request): JsonResponse
    {
        try {
            DB::select($request->sql);
        } catch (\Exception $e) {
            $error = $e;
        }

        return response()->json([
            'status' => isset($error) ? false : true,
            'error' => $error ?? '',
        ]);
    }

    public function deleteDBQuery(Request $request): JsonResponse
    {
        try {
            DB::select($request->sql);
        } catch (\Exception $e) {
            $error = $e;
        }

        return response()->json([
            'status' => isset($error) ? false : true,
            'error' => $error ?? '',
        ]);
    }
}
