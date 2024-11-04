<?php

namespace App\Http\Controllers\GlobalComponants;

use App\Http\Controllers\Controller;
use App\Http\Requests\GlobalComponants\StoreDataFilesAndAttachmentRequest;
use App\Models\GlobalFilesAndAttachments;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class FilesAndAttachmentsController extends Controller
{
    public function get_data(Request $request): View
    {
        $data = GlobalFilesAndAttachments::where('module_id', $request->module_id)->where('module', $request->module)->with('user')->get();

        return view('global_componants.files_and_attachments.files_and_attachments_list', compact('data'));
    }

    public function store_data(StoreDataFilesAndAttachmentRequest $request)
    {
        $store = $request->all();
        if (! empty($store)) {
            $file = $request->file('filename');
            if (isset($file)) {
                $file->move(base_path('/storage/app/global_files_and_attachments_file'), time().'_'.$file->getClientOriginalName());
            }

            if (isset($store['filename'])) {
                $filename = time().'_'.$store['filename']->getClientOriginalName();
            } else {
                $filename = '';
            }

            $storeHistory = GlobalFilesAndAttachments::create([
                'module_id' => $store['module_id'],
                'module' => $store['module'],
                'title' => $store['title'],
                'filename' => $filename,
                'created_by' => ! empty(Auth::user()->id) ? Auth::user()->id : null,
            ]);

            return response()->json(['storeHistory' => $storeHistory]);
        } else {
            return redirect()->back()->with('error', 'Something Went Wrong!');
        }
    }

    public function download($filename)
    {
        return Storage::download('global_files_and_attachments_file/'.$filename);
    }
}
