<?php

namespace App\Http\Controllers;

use App\GoogleFiletranslatorFile;
use App\Http\Requests\StoreGoogleFileTranslatorRequest;
use App\Language;
use App\Models\GoogleFileStausHistory;
use App\Models\GoogleFileTranslateHistory;
use App\Models\GoogleTranslateCsvData;
use App\Models\GoogleTranslateUserPermission;
use App\Models\StoreWebsiteCsvFile;
use App\Services\CommonGoogleTranslateService;
use App\Translations;
use App\User;
use DB;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Plank\Mediable\Facades\MediaUploader as MediaUploader;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class GoogleFileTranslator extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(request $request)
    {
        $query = GoogleFiletranslatorFile::query();
        if ($request->term) {
            $query = $query->where('name', 'LIKE', '%'.$request->term.'%');
        }

        $data = $query->orderBy('id')->paginate(25)->appends(request()->except(['page']));
        if ($request->ajax()) {
            return response()->json([
                'tbody' => view('googlefiletranslator.partials.list-files', compact('data'))->with('i', ($request->input('page', 1) - 1) * 5)->render(),
                'links' => (string) $data->render(),
                'count' => $data->total(),
            ], 200);
        }

        return view('googlefiletranslator.index', compact('data'))
            ->with('i', ($request->input('page', 1) - 1) * 5);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $Language = Language::all();

        return view('googlefiletranslator.create', compact('Language'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function store(StoreGoogleFileTranslatorRequest $request)
    {
        ini_set('max_execution_time', -1);
        DB::beginTransaction();
        try {
            $this->getMediaPathSave();
            $filename = $request->file('file');
            $filenameNew = null;
            $media = MediaUploader::fromSource($request->file('file'))
                ->toDestination('uploads', 'google-file-translator')
                ->upload();

            if (isset($media) && isset($media->filename) && isset($media->extension)) {
                $filenameNew = $media->filename.'.'.$media->extension;
            } else {
                throw new Exception('Error while uploading file.');
            }
            $input = $request->all();
            $input['name'] = $filenameNew;
            $insert = GoogleFiletranslatorFile::create($input);

            $path = public_path().'/uploads/google-file-translator/';
            $languageData = Language::where('id', $insert->tolanguage)->first();

            if (file_exists($path.$insert->name)) {
                try {
                    $result = $this->translateFile($path.$insert->name, $languageData->locale, ',');
                    foreach ($result as $translationSet) {
                        try {
                            $translationDataStored = new GoogleTranslateCsvData;
                            $translationDataStored->key = $translationSet[0];
                            $translationDataStored->value = $translationSet[1];
                            $translationDataStored->standard_value = $translationSet[2];
                            $translationDataStored->google_file_translate_id = $insert->id;
                            $translationDataStored->save();
                        } catch (Exception $e) {
                            return 'Upload failed: '.$e->getMessage();
                        }
                    }
                } catch (Exception $e) {
                    return redirect()->route('googlefiletranslator.list')->with('error', $e->getMessage());
                }
            } else {
                throw new Exception('File not found');
            }
            DB::commit();

            return redirect()->route('googlefiletranslator.list')->with('success', 'Translation created successfully');
        } catch (Exception $e) {
            Log::error($e);

            return redirect()->route('googlefiletranslator.list')->with('error', 'Error while uploading file. Please try again.');
        }
    }

    /**
     * Display the specified resource.
     *
     *
     * @return \Illuminate\Http\Response
     */
    public function show(int $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     *
     * @return \Illuminate\Http\Response
     */

    /**
     * Update the specified resource in storage.
     *
     * @param  int  $id
     */
    public function update(Request $request): JsonResponse
    {
        $record = GoogleTranslateCsvData::find($request->record_id);
        $oldRecord = $record->standard_value;
        $oldStatus = $record->status;

        $record->updated_by_user_id = $request->update_by_user_id;
        $record->standard_value = $request->update_record;
        $record->status = 2;
        $record->save();

        $history = new GoogleFileTranslateHistory;
        $history->old_value = $oldRecord;
        $history->new_value = $request->update_record;
        $history->updated_by = $request->update_by_user_id;
        $history->status = $oldStatus;
        $history->google_file_translate_csv_data_id = $request->record_id;
        $history->save();

        return response()->json(['status' => 200, 'data' => $record, 'message' => 'Value edited Successfully']);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(int $id): RedirectResponse
    {
        $GoogleFiletranslatorFile = GoogleFiletranslatorFile::find($id);
        $path = public_path().'/uploads/google-file-translator/';
        if (file_exists($path.$GoogleFiletranslatorFile->name)) {
            unlink($path.$GoogleFiletranslatorFile->name);
        }
        $GoogleFiletranslatorFile->delete();

        return redirect()->route('googlefiletranslator.list')
            ->with('success', 'Translation deleted successfully');
    }

    public function download($file): BinaryFileResponse
    {
        $path = public_path().'/uploads/google-file-translator/';
        if (file_exists($path.$file)) {
            $headers = [
                'Content-Type: text/csv',
            ];

            return response()->download($path.$file, $file, $headers);
        }
    }

    public function getMediaPathSave()
    {
        $path = public_path().'/uploads/google-file-translator/';
        File::isDirectory($path) || File::makeDirectory($path, 0777, true, true);

        return $path;
    }

    public function translateFile($path, $language, $delimiter = ',')
    {
        if (! file_exists($path) || ! is_readable($path)) {
            return false;
        }
        $newCsvData = [];
        $keywordToTranslate = [];
        if (($handle = fopen($path, 'r')) !== false) {
            while (($data = fgetcsv($handle, 1000, ',')) !== false) {
                // Check translation SEPARATE LINE exists or not
                $checkTranslationTable = Translations::select('text')->where('to', $language)->where('text_original', $data[0])->first();
                if ($checkTranslationTable) {
                    $data[] = htmlspecialchars_decode($checkTranslationTable->text, ENT_QUOTES);
                } else {
                    $keywordToTranslate[] = $data[0];
                    $data[] = $data[0];
                }
                $newCsvData[] = $data;
            }
            fclose($handle);
        }

        $translateKeyPair = [];
        if (isset($keywordToTranslate) && count($keywordToTranslate) > 0) {
            // Max 128 lines supports for translation per request
            // $keywordToTranslateChunk = array_chunk($keywordToTranslate, 100);
            // $translationString       = [];
            // foreach ($keywordToTranslateChunk as $key => $chunk) {
            //     try {
            //         $googleTranslate = new CommonGoogleTranslateService();
            //         $result          = $googleTranslate->translate($language, $chunk, true);
            //     } catch (Exception $e) {
            //         Log::channel('errorlog')->error($e);
            //         throw new Exception($e->getMessage());
            //     }
            //     array_push($translationString, ...$result);
            // }
            $translationString = [];
            $googleTranslate = new CommonGoogleTranslateService;
            foreach ($keywordToTranslate as $keyword) {
                try {
                    $result = $googleTranslate->translate($language, $keyword);
                } catch (Exception $e) {
                    Log::channel('errorlog')->error($e);
                    throw new Exception($e->getMessage());
                }
                $resultArray = ['input' => $keyword, 'text' => $result];
                array_push($translationString, $resultArray);
            }

            $insertData = [];
            if (isset($translationString) && count($translationString) > 0) {
                foreach ($translationString as $value) {
                    $translateKeyPair[$value['input']] = $value['text'];
                    $insertData[] = [
                        'text_original' => $value['input'],
                        'text' => $value['text'],
                        'from' => 'en',
                        'to' => $language,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                }
            }

            if (! empty($insertData)) {
                Translations::insert($insertData);
            }
        }

        // Update the csv with Translated data
        if (isset($newCsvData) && count($newCsvData) > 0) {
            for ($i = 0; $i < count($newCsvData); $i++) {
                $last = array_pop($newCsvData[$i]);
                array_push($newCsvData[$i], htmlspecialchars_decode($translateKeyPair[$last] ?? $last));
            }

            $handle = fopen($path, 'r+');
            foreach ($newCsvData as $line) {
                fputcsv($handle, $line, $delimiter);
            }
            fclose($handle);
        }

        return $newCsvData;
    }

    public function dataViewPage($id, $type, Request $request): View
    {
        $query = new GoogleTranslateCsvData;

        if ($type == 'googletranslate') {
            $query = $query->where('google_file_translate_id', $id)->latest();
        } else {
            $lang = explode('-', $type);
            $lang = end($lang);

            if (preg_match('/-([a-zA-Z]{2})\.csv$/', $type, $matches)) {
                $lang = $matches[1];
            }
            $getLang = Language::where('locale', $lang)->first();

            $query = $query->where('storewebsite_id', $id)
                ->where('lang_id', $getLang->id)
                ->latest();
        }

        if (! empty($request->date)) {
            $query = $query->where('created_at', 'LIKE', '%'.$request->date.'%');
        }
        if (! empty($request->search_msg)) {
            $query = $query->where('value', 'LIKE', '%'.$request->search_msg.'%');
        }

        if ($request->search_keyword != '') {
            $query = $query->where('key', 'LIKE', '%'.$request->search_keyword.'%');
        }

        if (! empty($request->search_stand_value)) {
            $query = $query->where('standard_value', 'LIKE', '%'.$request->search_stand_value.'%');
        }

        $googleTranslateDatas = $query->paginate(25); // Change 25 to the number of items per page you want to display.
        $activeUsers = User::where('is_active', '1')->get();
        $userPermission = GoogleTranslateUserPermission::where('user_id', auth()->user()->id)->where('action', 'edit')->first();

        return view('googlefiletranslator.googlefiletranlate-list', ['id' => $id, 'googleTranslateDatas' => $googleTranslateDatas, 'activeUsers' => $activeUsers, 'userPermission' => $userPermission]);
    }

    public function downloadPermission(Request $request): JsonResponse
    {
        $parts = explode('-', $request->type);
        if ($request->type == 'googletranslate') {
            $googleTranslate = GoogleFiletranslatorFile::find($request->id);
        } else {
            $filename = $parts[1];

            $googleTranslate = StoreWebsiteCsvFile::Where('storewebsite_id', $request->id)->where('filename', 'like', '%'.$filename.'%')
                ->first();
        }

        $googleTranslate->download_status = 1;
        $googleTranslate->save();

        return response()->json(['status' => 200, 'data' => $googleTranslate, 'message' => 'download permision allowed']);
    }

    public function userViewPermission(Request $request): JsonResponse
    {
        $googleFiletranslatorPermission = new GoogleTranslateUserPermission;
        $googleFiletranslatorPermission->google_translate_id = $request->user_id;
        $googleFiletranslatorPermission->user_id = $request->user_id;
        $googleFiletranslatorPermission->lang_id = $request->lang_id;
        $googleFiletranslatorPermission->action = $request->action;
        $googleFiletranslatorPermission->type = $request->type;
        $googleFiletranslatorPermission->save();

        return response()->json(['status' => 200, 'data' => $googleFiletranslatorPermission, 'message' => 'download permision allowed']);
    }

    public function tranalteHistoryShow($id): JsonResponse
    {
        try {
            $google_file_translate_csv_data_id = [];
            if (isset($id)) {
                $google_file_translate_csv_data_id = GoogleFileTranslateHistory::with(['user'])->Where('google_file_translate_csv_data_id', $id)->latest()->get();

                return response()->json([
                    'status' => true,
                    'data' => $google_file_translate_csv_data_id,
                    'message' => 'history added successfully',
                    'status_name' => 'success',
                ], 200);
            } else {
                throw new Exception('Task not found');
            }
        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'data' => $google_file_translate_csv_data_id,
                'message' => 'history added successfully',
                'status_name' => 'failed',
            ], 500);
        }
    }

    public function tranalteStatusHistoryShow($id): JsonResponse
    {
        try {
            $google_file_translate_csv_data_id = [];
            if (isset($id)) {
                $google_file_translate_csv_data_id = GoogleFileStausHistory::with(['user'])->Where('google_file_translate_id', $id)->latest()->get();

                return response()->json([
                    'status' => true,
                    'data' => $google_file_translate_csv_data_id,
                    'message' => 'Status history added successfully',
                    'status_name' => 'success',
                ], 200);
            } else {
                throw new Exception('Task not found');
            }
        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'data' => $google_file_translate_csv_data_id,
                'message' => 'Status history added successfully',
                'status_name' => 'failed',
            ], 500);
        }
    }

    public function statusChange(Request $request): JsonResponse
    {
        $googleTranslateDatas = GoogleTranslateCsvData::find(($request->id));
        $google_file_translate_csv_data_id = GoogleFileTranslateHistory::with(['user'])->Where('google_file_translate_csv_data_id', $request->id)->latest('updated_at')->first();

        if ($request->status == 'accept') {
            $history = new GoogleFileStausHistory;
            $history->google_file_translate_id = $request->id;
            $history->updated_by_user_id = Auth::id();
            $history->old_status = $googleTranslateDatas->status;
            $history->status = 1;
            $history->save();

            $googleTranslateDatas->status = 1;
            $googleTranslateDatas->approved_by_user_id = Auth::id();
            $googleTranslateDatas->save();

            return response()->json([
                'status' => true,
                'data' => $googleTranslateDatas,
                'message' => 'Update successfully',
                'status_name' => 'success',
            ], 200);
        }

        if ($request->status == 'reject') {
            $history = new GoogleFileStausHistory;
            $history->google_file_translate_id = $request->id;
            $history->updated_by_user_id = Auth::id();
            $history->old_status = $googleTranslateDatas->status;
            $history->status = 3;
            $history->save();

            $googleTranslateDatas->status = 3;
            $googleTranslateDatas->save();

            return response()->json([
                'status' => false,
                'data' => $googleTranslateDatas,
                'message' => 'Rejected successfully',
                'status_name' => 'failed',
            ], 500);
        }
    }

    public function downloadCsv($id, $type): \Illuminate\Http\Response
    {
        $getLang = null; // Initialize $getLang variable

        $lang = explode('-', $type);
        $lang = end($lang);

        if (preg_match('/-([a-zA-Z]{2})\.csv$/', $type, $matches)) {
            $lang = $matches[1];
        }
        $getLang = Language::where('locale', $lang)->first();

        if ($type == 'googletranslate') {
            $googleTranslateDatas = GoogleTranslateCsvData::where('google_file_translate_id', $id)->latest()->get();
        } else {
            $googleTranslateDatas = GoogleTranslateCsvData::where('storewebsite_id', $id)
                ->where('lang_id', $getLang->id)
                ->latest()
                ->get();
        }

        $fileName = 'google_translate_data_'.($getLang ? $getLang->locale : '').'.csv';

        $csvContent = '"Value","StanardValue"'."\n";

        foreach ($googleTranslateDatas as $data) {
            // Ensure that values are enclosed in double quotes and separated by commas
            $csvContent .= '"'.$this->formatForCSV($data->value).'","'.
                $this->formatForCSV($data->standard_value).'"'."\n";
        }

        // Specify the storage disk (e.g., 'public' or 'local') and the directory path
        $disk = 'public';
        $directory = 'csv/';

        // Store the CSV file
        Storage::disk($disk)->put($directory.$fileName, $csvContent);
        // Set the content type and disposition for download
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="'.$fileName.'"',
        ];

        // Optionally, you can set other headers like cache control if needed
        $headers['Cache-Control'] = 'must-revalidate, post-check=0, pre-check=0';
        $headers['Expires'] = '0';
        $headers['Pragma'] = 'public';

        // Send the file as a download response
        return response($csvContent)->withHeaders($headers);
    }

    public function formatForCSV($value)
    {
        // If the value contains a comma or a double quote, enclose it in double quotes and escape existing double quotes.
        if (strpos($value, ',') !== false || strpos($value, '"') !== false) {
            return '"'.str_replace('"', '""', $value).'"';
        }

        return $value;
    }

    public function getTranslatedTextScore(Request $request): JsonResponse
    {
        $googleTranslateCsvData = GoogleTranslateCsvData::where('id', $request->id)->first();
        if ($googleTranslateCsvData) {
            $originalText = $googleTranslateCsvData->key;
            if ($originalText != '') {
                $textScore = app('translation-lambda-helper')->getTranslateScore($originalText, $googleTranslateCsvData->standard_value);

                $googleTranslateCsvData->translate_text_score = ($textScore != 0) ? $textScore : 0;
                $googleTranslateCsvData->save();
            }

            return response()->json(['code' => 200, 'success' => 'Success', 'message' => 'Get translated text score successfully']);
        } else {
            return response()->json(['code' => 500, 'message' => 'Something went wrong']);
        }
    }
}
