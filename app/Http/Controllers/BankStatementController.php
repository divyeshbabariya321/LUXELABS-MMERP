<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\RedirectResponse;
use App\BankStatement;
use App\BankStatementFile;
use Illuminate\Http\Request;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Storage;

class BankStatementController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): \Illuminate\View\View
    {
        $data = BankStatementFile::with('user')->paginate(25);

        return View('bank-statement.index',
            compact('data')
        );
    }

    public function showImportForm(): \Illuminate\View\View
    {
        return view('bank-statement.form');
    }
    
    public function previewFile(Request $request, $id): JsonResponse
    {
        $bankStatement = BankStatementFile::find($id);
        $filePath = storage_path("app/".$bankStatement->path); //read file path
        // $filePath = $bankStatement->path; //read file path

        $data = Excel::toArray([], $filePath);

        $row_count = count($data[0]);

        return response()->json($data[0]);
        //return view('bank-statement.preview', ['data' => $data, 'row_count' => $row_count]);
    }

    public function import(Request $request): RedirectResponse
    {
        $originalName = $request->file('excel_file')->hashName();
        $inputs = $request->all();
        $path = $request->file('excel_file')->storeAs('files/bank_statements', $originalName);

        BankStatementFile::create([
            'filename'       => $originalName,
            'path'           => $path,
            'mapping_fields' => '',
            'name' => !empty($inputs['name']) ? $inputs['name'] : '',
            'status'         => 'uploaded',
            'created_by'     => Auth::id(),
            'created_at'     => date('Y-m-d H:i:s'),
        ]);

        return redirect()->back()->with('success', 'File imported successfully.');
    }

    public function heading_row_number_check(Request $request): RedirectResponse
    {
        $input = $request->all();

        return redirect()->route('bank-statement.import.map', ['id' => $input['id'], 'heading_row_number' => $input['heading_row_number']]);
    }

    public function map(Request $request, $id, $heading_row_number = 1): \Illuminate\View\View
    {
        $bankStatement = BankStatementFile::find($id);

        $filePath = Storage::url($bankStatement->path);

        $data = Excel::toArray([], $filePath);

        // Assuming the first row contains column headers
        $excelHeaders = $data[0][$heading_row_number - 1];

        // Get the columns of the database table
        $dbFields = [
            'bank_name' => 'Bank name', 
            'bank_account' => 'Bank account', 
            'account_type' => 'Account type', 
            'transaction_date'         => 'Transaction Date',
            'transaction_reference_no' => 'Transaction Reference Number',
            'debit_amount'             => 'Debit Amount',
            'credit_amount'            => 'Credit Amount',
            'balance'                  => 'Balance',
            'description' => "Description"
        ];

        $row_count = count($data[0]);
        $first10Rows = array_slice($data[0], 0, 10);

        return view('bank-statement.map', compact('bankStatement','excelHeaders', 'dbFields', 'id', 'row_count', 'heading_row_number', 'data', 'first10Rows'));
    }

    public function map_import(Request $request, $id, $heading_row_number = 1): RedirectResponse
    {
        $bankStatementFile = BankStatementFile::find($id);
        $filePath          = Storage::url('files' . $bankStatementFile->path);

        $data   = Excel::toArray([], $filePath);
        $number = $heading_row_number - 1;
        if ($number <= 0) {
            $number = 0;
        }
        // Assuming the first row contains column headers
        $excelHeaders = $data[0][$number];

        $data_array = [];

        foreach ($data[0] as $k => $v) {
            foreach ($excelHeaders as $k1 => $v1) {
                $data_array[$k][trim($v1)] = $v[trim($k1)];
            }
        }

        $fields_db = [
            "bank_name",
            "bank_account",
            "account_type",
            'transaction_date',
            'transaction_reference_no',
            'debit_amount',
            'credit_amount',
            'balance',
            "description"
        ];

        $data_array_new = [];
        $inputes        = $request->all();
        foreach ($data_array as $k => $v) {
            $data_array_new_1 = [];
            foreach ($fields_db as $k1 => $v1) {
                $data_array_new_1[trim($v1)] = @$v[trim($inputes[$v1])];
            }
            $data_array_new_1['bank_statement_file_id'] = $id;
            $data_array_new_1['created_at']             = date('Y-m-d H:i:s');
            foreach ($data_array_new_1 as $k2 => $v2) {
                if ($v2 == null || trim($v2) == '') {
                    $data_array_new_1[$k2] = '-';
                }
            }

            $bankStatement = BankStatement::create($data_array_new_1);
        }

        //save status of the file
        $bankStatementFile->status = 'mapped';
        $bankStatementFile->save();

        return redirect()->route('bank-statement.index')->with('success', 'File imported data mapped successfully.');
    }

    public function mapped_data($id, Request $request): \Illuminate\View\View
    {
        $data              = BankStatement::where(['bank_statement_file_id' => $id])->with('user')->paginate(25);
        $bankStatementFile = BankStatementFile::find($id);

        return View('bank-statement.mapped',
            compact('data', 'bankStatementFile')
        );
    }
}
