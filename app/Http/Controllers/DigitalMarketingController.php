<?php

namespace App\Http\Controllers;

use App\DigitalMarketingPlatform;
use App\DigitalMarketingPlatformComponent;
use App\DigitalMarketingPlatformFile;
use App\DigitalMarketingSolution;
use App\DigitalMarketingSolutionAttribute;
use App\DigitalMarketingSolutionFile;
use App\DigitalMarketingSolutionResearch;
use App\DigitalMarketingUsp;
use App\Email;
use App\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\View\View;

class DigitalMarketingController extends Controller
{
    public function index(Request $request): View
    {
        $title = 'Social-Digital Marketing';
        $status = DigitalMarketingPlatform::STATUS;
        $records = DigitalMarketingPlatform::get();

        return view('digital-marketing.index', compact('records', 'title', 'status'));
    }

    public function records(Request $request): JsonResponse
    {
        $records = DigitalMarketingPlatform::query();

        $keyword = request('keyword');
        if (! empty($keyword)) {
            $records = $records->where(function ($q) use ($keyword) {
                $q->where('platform', 'LIKE', "%$keyword%")
                    ->orWhere('description', 'LIKE', "%$keyword%");
            });
        }

        $records = $records->get();

        foreach ($records as &$rec) {
            $rec->status_name = isset(DigitalMarketingPlatform::STATUS[$rec->status]) ? DigitalMarketingPlatform::STATUS[$rec->status] : $rec->status;
            $rec->components_list = implode(',', $rec->components->pluck('name')->toArray());
        }

        return response()->json(['code' => 200, 'data' => $records, 'total' => count($records)]);
    }

    public function save(Request $request): JsonResponse
    {
        $post = $request->all();

        $validator = Validator::make($post, [
            'platform' => 'required',
            'description' => 'required',
        ]);

        if ($validator->fails()) {
            $outputString = '';
            $messages = $validator->errors()->getMessages();
            foreach ($messages as $k => $errr) {
                foreach ($errr as $er) {
                    $outputString .= "$k : ".$er.'<br>';
                }
            }

            return response()->json(['code' => 500, 'error' => $outputString]);
        }
        $id = $request->get('id', 0);

        $records = DigitalMarketingPlatform::find($id);

        if (! $records) {
            $records = new DigitalMarketingPlatform;
        }

        $records->fill($post);
        $records->save();

        return response()->json(['code' => 200, 'data' => $records]);
    }

    /**
     * Edit Page
     *
     * @param  Request  $request  [description]
     * @param  mixed  $id
     */
    public function edit(Request $request, $id): JsonResponse
    {
        $digitalMarketing = DigitalMarketingPlatform::where('id', $id)->first();

        if ($digitalMarketing) {
            return response()->json(['code' => 200, 'data' => $digitalMarketing]);
        }

        return response()->json(['code' => 500, 'error' => 'Wrong digital marketing id!']);
    }

    public function saveImages(Request $request): JsonResponse
    {
        $files = $request->file('file');
        $fileNameArray = [];
        foreach ($files as $key => $file) {
            $fileName = time().$key.'.'.$file->extension();
            $fileNameArray[] = $fileName;
            if ($request->type == 'marketing') {
                DigitalMarketingPlatformFile::create(['digital_marketing_platform_id' => $request->id, 'file_name' => $fileName, 'user_id' => Auth::id()]);
            } else {
                DigitalMarketingSolutionFile::create(['digital_marketing_solution_id' => $request->id, 'file_name' => $fileName, 'user_id' => Auth::id()]);
            }

            $file->move(public_path('digital_marketing'), $fileName);
        }

        return response()->json(['code' => 200, 'msg' => 'files uploaded successfully', 'data' => $fileNameArray]);
    }

    /**
     * delete Page
     *
     * @param  Request  $request  [description]
     * @param  mixed  $id
     */
    public function delete(Request $request, $id): JsonResponse
    {
        $digitalMarketing = DigitalMarketingPlatform::where('id', $id)->first();

        if ($digitalMarketing) {
            foreach ($digitalMarketing->solutions as $solution) {
                $solution->attributes()->delete();
                $solution->delete();
            }

            $digitalMarketing->delete();

            return response()->json(['code' => 200]);
        }

        return response()->json(['code' => 500, 'error' => 'Wrong digital marketing id!']);
    }

    public function solution(Request $request, $id): View
    {
        $title = 'Social-Digital Marketing Solution';
        $status = DigitalMarketingPlatform::STATUS;

        return view('digital-marketing.solution.index', compact('title', 'status', 'id'));
    }

    public function solutionRecords(Request $request, $id): JsonResponse
    {
        $records = DigitalMarketingSolution::where('digital_marketing_platform_id', $id);

        $keyword = request('keyword');
        if (! empty($keyword)) {
            $records = $records->where(function ($q) use ($keyword) {
                $q->where('provider', 'LIKE', "%$keyword%")
                    ->orWhere('website', 'LIKE', "%$keyword%")
                    ->orWhere('contact', 'LIKE', "%$keyword%");
            });
        }

        $records = $records->get();

        $filledUsp = [];
        foreach ($records as $record) {
            $attributes = $record->attributes;
            if (! $attributes->isEmpty()) {
                foreach ($attributes as $attribute) {
                    $filledUsp[$record->id][$attribute->key] = $attribute->value;
                }
            }
        }

        $usps = DigitalMarketingUsp::where('digital_marketing_platform_id', $id)->get();

        return response()->json([
            'code' => 200,
            'data' => $records,
            'total' => count($records),
            'usps' => $usps,
            'filledUsp' => $filledUsp,
        ]);
    }

    public function solutionSave(Request $request, $id): JsonResponse
    {
        $post = $request->all();

        $validator = Validator::make($post, [
            'provider' => 'required',
            'website' => 'required',
            'contact' => 'required',
        ]);

        if ($validator->fails()) {
            $outputString = '';
            $messages = $validator->errors()->getMessages();
            foreach ($messages as $k => $errr) {
                foreach ($errr as $er) {
                    $outputString .= "$k : ".$er.'<br>';
                }
            }

            return response()->json(['code' => 500, 'error' => $outputString]);
        }

        $solutionId = $request->get('solution_id', 0);

        $records = DigitalMarketingSolution::where('digital_marketing_platform_id', $id)->where('id', $solutionId)->first();

        if (! $records) {
            $records = new DigitalMarketingSolution;
        }

        $records->fill($post);
        $records->digital_marketing_platform_id = $id;
        $records->save();

        return response()->json(['code' => 200, 'data' => $records]);
    }

    public function solutionEdit(Request $request, $id, $solutionId): JsonResponse
    {
        $record = DigitalMarketingSolution::where('digital_marketing_platform_id', $id)->where('id', $solutionId)->first();

        if ($record) {
            return response()->json(['code' => 200, 'data' => $record]);
        }

        return response()->json(['code' => 500, 'error' => 'Wrong digital marketing solution id!']);
    }

    public function solutionDelete(Request $request, $id, $solutionId): JsonResponse
    {
        $record = DigitalMarketingSolution::where('digital_marketing_platform_id', $id)->where('id', $solutionId)->first();

        if ($record) {
            // check all attributes
            $record->attributes()->delete();
            $record->delete();

            return response()->json(['code' => 200]);
        }

        return response()->json(['code' => 500, 'error' => 'Wrong digital marketing id!']);
    }

    public function solutionCreateUsp(Request $request, $id): JsonResponse
    {
        $post = $request->all();

        $validator = Validator::make($post, [
            'name' => 'required',
        ]);

        if ($validator->fails()) {
            $outputString = '';
            $messages = $validator->errors()->getMessages();
            foreach ($messages as $k => $errr) {
                foreach ($errr as $er) {
                    $outputString .= "$k : ".$er.'<br>';
                }
            }

            return response()->json(['code' => 500, 'error' => $outputString]);
        }

        $solutionId = $request->get('solution_id', 0);

        $records = DigitalMarketingUsp::where('digital_marketing_platform_id', $id)->where('id', $solutionId)->first();

        if (! $records) {
            $records = new DigitalMarketingUsp;
        }

        $records->fill($post);
        $records->digital_marketing_platform_id = $id;
        $records->save();

        return response()->json(['code' => 200, 'data' => $records]);
    }

    public function solutionSaveUsp(Request $request, $id, $solutionId): JsonResponse
    {
        $attributes = $request->get('usps');
        if (! empty($attributes)) {
            foreach ($attributes as $key => $value) {
                if (! empty($value)) {
                    DigitalMarketingSolutionAttribute::updateOrCreate([
                        'digital_marketing_solution_id' => $solutionId, 'key' => $key,
                    ], [
                        'digital_marketing_solution_id' => $solutionId, 'key' => $key, 'value' => $value,
                    ]);
                }
            }
        }

        return response()->json(['code' => 200, 'data' => []]);
    }

    public function research(Request $request, $id, $solutionId): View
    {
        $title = 'Social-Digital Marketing Solution Research';

        $priority = DigitalMarketingSolutionResearch::PRIORITY;

        return view('digital-marketing.solution.research.index', compact('title', 'status', 'id', 'solutionId', 'priority'));
    }

    public function researchRecords(Request $request, $id, $solutionId): JsonResponse
    {
        $records = DigitalMarketingSolutionResearch::where('digital_marketing_solution_id', $solutionId);

        $keyword = request('keyword');
        if (! empty($keyword)) {
            $records = $records->where(function ($q) use ($keyword) {
                $q->where('subject', 'LIKE', "%$keyword%")
                    ->orWhere('description', 'LIKE', "%$keyword%")
                    ->orWhere('remarks', 'LIKE', "%$keyword%");
            });
        }

        $records = $records->get();

        foreach ($records as &$rec) {
            $rec->priority = isset(DigitalMarketingSolutionResearch::PRIORITY[$rec->priority]) ? DigitalMarketingSolutionResearch::PRIORITY[$rec->priority] : $rec->priority;
        }

        return response()->json([
            'code' => 200,
            'data' => $records,
            'total' => count($records),
        ]);
    }

    public function researchSave(Request $request, $id, $solutionId): JsonResponse
    {
        $post = $request->all();

        $validator = Validator::make($post, [
            'subject' => 'required',
            'priority' => 'required',
        ]);

        if ($validator->fails()) {
            $outputString = '';
            $messages = $validator->errors()->getMessages();
            foreach ($messages as $k => $errr) {
                foreach ($errr as $er) {
                    $outputString .= "$k : ".$er.'<br>';
                }
            }

            return response()->json(['code' => 500, 'error' => $outputString]);
        }

        $researchId = $request->get('research_id', 0);

        $records = DigitalMarketingSolutionResearch::where('digital_marketing_solution_id', $solutionId)->where('id', $researchId)->first();

        if (! $records) {
            $records = new DigitalMarketingSolutionResearch;
        }

        $records->fill($post);
        $records->digital_marketing_solution_id = $solutionId;
        $records->save();

        return response()->json(['code' => 200, 'data' => $records]);
    }

    public function researchEdit(Request $request, $id, $solutionId, $researchId): JsonResponse
    {
        $record = DigitalMarketingSolutionResearch::where('digital_marketing_solution_id', $solutionId)->where('id', $researchId)->first();

        if ($record) {
            return response()->json(['code' => 200, 'data' => $record]);
        }

        return response()->json(['code' => 500, 'error' => 'Wrong digital marketing solution research id!']);
    }

    public function researchDelete(Request $request, $id, $solutionId, $researchId): JsonResponse
    {
        $record = DigitalMarketingSolutionResearch::where('digital_marketing_solution_id', $solutionId)->where('id', $researchId)->first();

        if ($record) {
            $record->delete();

            return response()->json(['code' => 200, 'data' => []]);
        }

        return response()->json(['code' => 500, 'error' => 'Wrong digital marketing solution research id!']);
    }

    public function components(Request $request, $id): JsonResponse
    {
        $records = [];
        $records['id'] = $id;
        $records['components'] = DigitalMarketingPlatformComponent::where('digital_marketing_platform_id', $id)->get()->pluck('name')->toArray();

        return response()->json(['code' => 200, 'data' => $records]);
    }

    public function files(Request $request, $id)
    {
        $records = [];
        $records['id'] = $id;
        $records['components'] = DigitalMarketingPlatformFile::where('digital_marketing_platform_id', $id)->get()->transform(function ($files) {
            $files->downloadUrl = config('env.APP_URL').'/digital_marketing/'.$files->file_name;
            $files->user = User::find($files->user_id)->name;

            return $files;
        });

        return response()->json(['code' => 200, 'data' => $records]);
    }

    public function filesSolution(Request $request, $id)
    {
        $records = [];
        $records['id'] = $id;
        $records['components'] = DigitalMarketingSolutionFile::where('digital_marketing_solution_id', $id)->get()->transform(function ($files) {
            $files->downloadUrl = config('env.APP_URL').'/digital_marketing/'.$files->file_name;
            $files->user = User::find($files->user_id)->name;

            return $files;
        });

        return response()->json(['code' => 200, 'data' => $records]);
    }

    public function componentStore(Request $request, $id): JsonResponse
    {
        DigitalMarketingPlatformComponent::where('digital_marketing_platform_id', $id)->delete();

        $components = $request->get('components');
        if (! empty($components)) {
            foreach ($components as $component) {
                DigitalMarketingPlatformComponent::create([
                    'digital_marketing_platform_id' => $id,
                    'name' => $component,
                ]);
            }
        }

        return response()->json(['code' => 200, 'data' => []]);
    }

    public function getEmails(Request $request)
    {
        if ($request->id) {
            $emails = Email::where('digital_platfirm', $request->id)->get();

            return $emails;
        }

        return response()->json(['code' => 500, 'data' => '']);
    }
}
