<?php

namespace App\Http\Controllers;

use App\Category;
use App\Exports\SystemSizeExport;
use App\Http\Requests\StoreSystemSizeRequest;
use App\Http\Requests\UpdateSystemSizeRequest;
use App\Jobs\ProceesPushSystemSize;
use App\Setting;
use App\SystemSize;
use App\SystemSizeManager;
use App\SystemSizeRelation;
use Excel;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SystemSizeController extends Controller
{
    public function index(Request $request): View
    {
        $systemSizesManagers = SystemSizeManager::orderBy('id');

        if ($request->main_category_id) {
            $systemSizesManagers = $systemSizesManagers->whereIn('category_id', $request->main_category_id);
        }
        if ($request->size) {
            $systemSizesManagers = $systemSizesManagers->where('erp_size', 'Like', '%'.$request->size.'%');
        }

        $systemSizesManagers = $systemSizesManagers->select(
            'system_size_managers.id',
            'categories.title as category',
            'categories.parent_id as category_parent_id',
            'system_size_managers.erp_size',
            'system_size_managers.created_at',
            'system_size_managers.updated_at'
        )
            ->leftjoin('categories', 'categories.id', 'system_size_managers.category_id')
            ->where('system_size_managers.status', 1)
            ->paginate(Setting::get('pagination'));
        $managers = [];
        foreach ($systemSizesManagers as $value) {
            $related = SystemSizeRelation::select('system_size_relations.size', 'system_sizes.name')
                ->leftjoin('system_sizes', 'system_sizes.id', 'system_size_relations.system_size')
                ->where('system_size_manager_id', $value->id)->get();
            $value->sizes = '';
            foreach ($related as $v) {
                $string = $v->name.' => '.$v->size;
                $value->sizes .= $value->sizes == '' ? $string : ', '.$string;
            }
            $managers[] = $value;
        }

        $systemSizes = SystemSize::where('status', 1)->get();
        $parentCategories = Category::where('parent_id', 0)->get();
        $categories = [];

        foreach ($parentCategories as $value) {
            $tempCat['parentcategory'] = $value->title;
            $tempCat['subcategories'] = Category::where('parent_id', $value->id)->get();
            $categories[] = $tempCat;
        }

        return view('system-size.index', compact('systemSizes', 'systemSizesManagers', 'categories', 'managers'));
    }

    public function store(StoreSystemSizeRequest $request): JsonResponse
    {

        SystemSize::create(['name' => $request->input('name')]);

        return response()->json(['success' => true, 'message' => 'System size created successfully']);
    }

    public function update(UpdateSystemSizeRequest $request): JsonResponse
    {

        $systemsize = SystemSize::find($request->input('id'));
        $systemsize->name = $request->input('code');
        if ($systemsize->save()) {
            return response()->json(['success' => true, 'message' => 'System size update successfully']);
        }

        return response()->json(['success' => false, 'message' => 'Something went wrong!']);
    }

    public function delete(Request $request): JsonResponse
    {
        $systemsize = SystemSize::find($request->input('id'));
        $systemsize->status = 0;
        if ($systemsize->save()) {
            return response()->json(['success' => true, 'message' => 'System size delete successfully']);
        }

        return response()->json(['success' => false, 'message' => 'Something went wrong!']);
    }

    public function managerstore(Request $request): JsonResponse
    {
        $check = SystemSizeManager::where('category_id', $request->category)->where('erp_size', $request->erp_size)->where('status', 1)->first();
        if (! empty($check)) {
            return response()->json(['success' => false, 'message' => 'ERP Size already exist!']);
        }
        $manager = SystemSizeManager::create(['category_id' => $request->category, 'erp_size' => $request->erp_size]);
        if (! empty($manager)) {
            if (isset($request->sizes)) {
                foreach ($request->sizes as $value) {
                    if (! empty($value['size'])) {
                        SystemSizeRelation::create([
                            'system_size_manager_id' => $manager->id,
                            'system_size' => $value['system_size_id'],
                            'size' => $value['size'],
                        ]);
                    }
                }
            }

            return response()->json(['success' => true, 'message' => 'System size saved successfully']);
        }

        return response()->json(['success' => false, 'message' => 'Something went wrong!']);
    }

    public function manageredit(Request $request): JsonResponse
    {
        $sm = SystemSizeManager::find($request->input('id'));
        $systemSizeRelated = SystemSizeRelation::select(
            'system_size_relations.id',
            'system_sizes.name',
            'system_size_relations.system_size',
            'system_size_relations.size'
        )
            ->leftjoin('system_sizes', 'system_sizes.id', 'system_size_relations.system_size')
            ->where('system_size_manager_id', $sm->id);
        $exitIds = $systemSizeRelated->pluck('system_size')->toArray();
        $systemSizeRelated = $systemSizeRelated->get();
        $html = '<div class="col-md-12 mt-3 sizevarintinput1">
                    <div class="row">
                        <input type="hidden" name="manager_id" value="'.$sm->id.'">
                        <div class="col-md-4">
                            <span>ERP Size (IT)</span>
                        </div>
                        <div class="col-md-8">
                            <input type="text" class="form-control" placeholder="Enter ERP size" name="erp_size" value="'.$sm->erp_size.'">
                        </div>
                    </div>
                </div>';
        $index = 0;
        foreach ($systemSizeRelated as $key => $value) {
            $html .= '<div class="col-md-12 mt-3 sizevarintinput1">
                        <div class="row">
                            <div class="col-md-4">
                                <span>'.$value->name.'</span>
                            </div>
                            <div class="col-md-8">
                                <input type="text" class="form-control" placeholder="Enter size" name="sizes['.$key.'][size]" required="" value="'.$value->size.'">
                                <input type="hidden" name="sizes['.$key.'][id]" value="'.$value->id.'">
                            </div>
                        </div>
                    </div>';
            $index = $key;
        }
        $systemSizes = SystemSize::where('status', 1)->get();

        foreach ($systemSizes as $key => $value) {
            if (! in_array($value->id, $exitIds)) {
                $index++;
                $html .= '<div class="col-md-12 mt-3 sizevarintinput1">
                        <div class="row">
                            <div class="col-md-4">
                                <span>'.$value->name.'</span>
                            </div>
                            <div class="col-md-8">
                                <input type="text" class="form-control" placeholder="Enter size" name="sizes['.$index.'][size]" required="">
                                <input type="hidden" class="form-control" placeholder="Enter size" name="sizes['.$index.'][system_size_id]" value="'.$value->id.'">
                            </div>
                        </div>
                    </div>';
            }
        }

        return response()->json(['success' => true, 'message' => 'successful!', 'data' => $html, 'category_id' => $sm->category_id]);
    }

    public function managerupdate(Request $request): JsonResponse
    {
        $check = SystemSizeManager::where('id', '!=', $request->manager_id)->where('category_id', $request->category)->where('erp_size', $request->erp_size)->where('status', 1)->first();
        if (! empty($check)) {
            return response()->json(['success' => false, 'message' => 'ERP Size already exist!']);
        }

        SystemSizeManager::where('id', $request->manager_id)->update(['category_id' => $request->category, 'erp_size' => $request->erp_size]);
        if (isset($request->sizes)) {
            foreach ($request->sizes as $key => $value) {
                if (! empty($value['size'])) {
                    if (isset($value['id'])) {
                        SystemSizeRelation::where('id', $value['id'])->update(['size' => $value['size']]);
                    } else {
                        SystemSizeRelation::create([
                            'system_size_manager_id' => $request->manager_id,
                            'system_size' => $value['system_size_id'],
                            'size' => $value['size'],
                        ]);
                    }
                }
            }
        }

        return response()->json(['success' => true, 'message' => 'Update successfully!']);
    }

    public function managerdelete(Request $request): JsonResponse
    {
        $sm = SystemSizeManager::find($request->input('id'));
        $sm->status = 0;
        if ($sm->save()) {
            return response()->json(['success' => true, 'message' => 'Delete successfully!']);
        }

        return response()->json(['success' => false, 'message' => 'Something went wrong!']);
    }

    public function managercheckexistvalue(Request $request): JsonResponse
    {
        $sm = SystemSizeManager::where('category_id', $request->id)->where('status', 1)->get();
        $systemSizes = SystemSize::where('status', 1)->get();
        $html = '';

        foreach ($sm as $s) {
            if ($s->system_size_id == 0) {
                $html .= '<div class="col-md-12 mt-3 sizevarintinput"><div class="row"><div class="col-md-4"><span>ERP Size (IT)</span></div><div class="col-md-8"><input type="text" class="form-control" placeholder="Enter size" name="sizes[0][size]" value="'.$s->size.'"><input type="hidden" name="sizes[0][system_size_id]" value="0"><input type="hidden" name="sizes[0][id]" value="0"></div></div></div>';
            }
        }
        if ($html == '') {
            $html .= '<div class="col-md-12 mt-3 sizevarintinput"><div class="row"><div class="col-md-4"><span>ERP Size (IT)</span></div><div class="col-md-8"><input type="text" class="form-control" placeholder="Enter size" name="sizes[0][size]"><input type="hidden" name="sizes[0][system_size_id]" value="0"></div></div></div>';
        }

        foreach ($systemSizes as $systemSize) {
            $sizeValue = '';
            $id = '';
            foreach ($sm as $s) {
                if ($systemSize->id == $s->system_size_id) {
                    $sizeValue = $s->size;
                    $id = '<input type="hidden" name="sizes['.$systemSize->id.'][id]" value="'.$s->id.'">';
                }
            }
            $html .= '<div class="col-md-12 mt-3 sizevarintinput"><div class="row"><div class="col-md-4"><span>'.$systemSize->name.'</span></div><div class="col-md-8"><input type="text" class="form-control" placeholder="Enter size" name="sizes['.$systemSize->id.'][size]" value="'.$sizeValue.'"><input type="hidden" name="sizes['.$systemSize->id.'][system_size_id]" value="'.$systemSize->id.'">'.$id.'</div></div></div>';
        }

        return response()->json(['success' => true, 'message' => 'successful!', 'data' => $html]);
    }

    public function exports()
    {
        return Excel::download(new SystemSizeExport, 'systemsize.xlsx');
    }

    //This API will put the records in Queue
    public function pushSystemSize(Request $request): JsonResponse
    {
        $data = $request->all();

        if (empty($data['systemSizeManagerId'])) {
            return response()->json(['code' => 400, 'data' => [], 'message' => 'One of the api parameter is missing']);
        }

        try {
            //Add the data for queue
            ProceesPushSystemSize::dispatch($data['systemSizeManagerId'])->onQueue('systemsize');

            return response()->json(['code' => 200, 'data' => [], 'message' => 'System size added in queue']);
        } catch (Exception $e) {
            return response()->json(['code' => 400, 'data' => [], 'message' => $e->getMessage()]);
        }
    }
}
