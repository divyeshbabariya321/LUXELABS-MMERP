<?php

namespace App\Http\Controllers;

use App\Models\SopHasCategory;
use App\PurchaseProductOrderLog;
use App\Sop;
use App\SopCategory;
use App\SopPermission;
use App\User;
use Dompdf\Dompdf;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class SopController extends Controller
{
    public function index(Request $request): View
    {
        $users = User::all();
        $usersop = Sop::with(['purchaseProductOrderLogs', 'user', 'sopCategory']);
        if ($request->get('search')) {
            $usersop = $usersop->where('name', 'like', '%'.$request->get('search').'%')
                ->orWhere('content', 'like', '%'.$request->get('search').'%');
        }

        if ($request->get('category')) {
            $sop_ids = SopHasCategory::distinct('sop_id')->whereIn('sop_category_id', $request->get('category'))->select('sop_id')->get()->pluck('sop_id')->toArray();
            $usersop = $usersop->whereIn('id', $sop_ids);
        }

        $usersop = $usersop->orderByDesc('id')->limit(25)->paginate(25);

        $total_record = $usersop->total();
        $category_result = SopCategory::all();
        $request = $request->all();

        return view('products.sop', compact('usersop', 'total_record', 'users', 'category_result', 'request'));
    }

    public function sopnamedata_logs(Request $request): JsonResponse
    {
        $log_data = PurchaseProductOrderLog::with(['updated_by', 'sop', 'sop.user'])->where('purchase_product_order_id', $request->id)
            ->where('header_name', $request->header_name)
            ->orderByDesc('id')
            ->get();

        return response()->json(['log_data' => $log_data, 'code' => 200]);
    }

    public function delete($id): JsonResponse
    {
        $usersop = Sop::findOrFail($id);
        $usersop->delete();

        return response()->json([
            'message' => 'Data deleted Successfully!',
        ]);
    }

    /**
     * Sop category add in table
     */
    public function categoryStore(Request $request): JsonResponse
    {
        $category = SopCategory::where('category_name', $request->category_name)->first();
        if ($category) {
            return response()->json(['success' => false, 'message' => 'Category already existed']);
        }
        try {
            $resp = SopCategory::create(['category_name' => $request->category_name]);

            return response()->json(['success' => true, 'message' => 'Category added successfully', 'data' => $resp]);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    public function categorylist(): JsonResponse
    {
        $category_result = SopCategory::all();

        return response()->json(['success' => true, 'data' => $category_result, 'message' => 'Record found']);
    }

    public function store(Request $request): JsonResponse
    {
        $sopType = $request->get('type');
        $sop = Sop::where('name', $sopType)->first();

        $name = Sop::where('name', $request->get('name'))->first();

        if ($name) {
            return response()->json(['success' => false, 'message' => 'Name already existed']);
        }

        $appendData = '';
        if (! $sop) {
            $category = $request->get('category');
            $sop = new Sop;
            $sop->name = $request->get('name');
            $sop->content = $request->get('content');
            $sop->user_id = Auth::id();
            $sop->save();

            if (! empty($category) && count($category) > 0) {
                $sop->sopCategory()->attach($category);
            }

            $params['purchase_product_order_id'] = $sop->id;
            $params['header_name'] = 'SOP Listing Approve Logs';
            $params['replace_from'] = '-';
            $params['replace_to'] = $request->get('name');
            $params['created_by'] = Auth::id();

            $appendsop = Sop::with(['purchaseProductOrderLogs', 'user', 'sopCategory'])->find($sop->id);
            $users = User::all();
            $appendData = view('products.partials.sop-list-single', compact('appendsop', 'users'))->render();
            PurchaseProductOrderLog::create($params);
        }

        $user_email = User::select('email')->where('id', $sop->user_id)->get();

        $only_date = $sop->created_at->todatestring();

        return response()->json(['only_date' => $only_date, 'sop' => $sop, 'user_email' => $user_email, 'params' => $params, 'appendData' => $appendData]);
    }

    public function edit(Request $request): JsonResponse
    {
        $sopedit = Sop::with('sopCategory')->findOrFail($request->id);

        if (isset($sopedit->sopCategory) && count($sopedit->sopCategory) > 0) {
            $sopedit->sopCategory = $sopedit->sopCategory->pluck('id');
        }

        return response()->json(['sopedit' => $sopedit]);
    }

    public function update(Request $request): JsonResponse
    {
        $category = $request->get('category', '');

        $cat = Sop::where('category', $request->get('category'))->where('id', '!=', $request->id)->first();
        if ($cat) {
            return response()->json(['success' => false, 'message' => 'Category already existed']);
        }

        $sopedit = Sop::where('id', $request->id)->first();
        if ($sopedit) {
            $sopedit->name = $request->get('name', '');
            $sopedit->content = $request->get('content', '');
            $sopedit->save();

            $sopedit->hasSopCategory()->delete();
            if (! empty($category) && count($category) > 0) {
                $sopedit->sopCategory()->attach($category ?? []);
            }
            $params['purchase_product_order_id'] = $request->id;
            $params['header_name'] = 'SOP Listing Approve Logs';
            $params['replace_from'] = $request->get('sop_old_name', '');
            $params['replace_to'] = $request->get('name', '');
            $params['created_by'] = Auth::id();

            if (isset($sopedit->sopCategory) && count($sopedit->sopCategory) > 0) {
                $temp = $sopedit->sopCategory->pluck('category_name')->toArray();
                $sopedit->sopCategory = implode(',', $temp);
            }

            PurchaseProductOrderLog::create($params);

            if ($sopedit) {
                return response()->json([
                    'sopedit' => $sopedit,
                    'params' => $params,
                    'type' => 'edit',
                ]);
            }
        } else {
            $sop = new Sop;
            $sop->name = $request->get('name');
            $sop->content = $request->get('content');
            $sop->user_id = Auth::id();
            $sop->save();

            $sop->hasSopCategory()->delete();
            if (! empty($category) && count($category) > 0) {
                $sop->sopCategory()->attach($category ?? []);
            }

            $params['purchase_product_order_id'] = $sop->id;
            $params['header_name'] = 'SOP Listing Approve Logs';
            $params['replace_from'] = '-';
            $params['replace_to'] = $request->get('name');
            $params['created_by'] = Auth::id();

            if (isset($sopedit->sopCategory) && count($sopedit->sopCategory) > 0) {
                $temp = $sopedit->sopCategory->pluck('category_name')->toArray();
                $sopedit->sopCategory = implode(',', $temp);
            }

            PurchaseProductOrderLog::create($params);

            $user_email = User::select('email')->where('id', $sop->user_id)->get();
            $only_date = $sop->created_at->todatestring();

            if ($sop) {
                return response()->json([
                    'sopedit' => $sop,
                    'params' => $params,
                    'type' => 'new',
                    'only_date' => $only_date,
                    'user_email' => $user_email,
                ]);
            }
        }
    }

    public function updateSopCategory(Request $request): JsonResponse
    {
        $sop = Sop::findOrFail($request->id);

        if ($sop && $request->type == 'attach') {
            $sop->sopCategory()->attach($request->updateCategoryId);
        }

        if ($sop && $request->type == 'detach') {
            $sop->sopCategory()->detach($request->updateCategoryId);
        }

        return response()->json(['success' => true, 'message' => 'Category updated successfully']);
    }

    public function search(Request $request): View
    {
        $searchsop = $request->get('search');
        $usersop = Sop::where('name', 'like', '%'.$searchsop.'%')->paginate(10);

        return view('products.sop', compact('usersop'));
    }

    public function ajaxsearch(Request $request)
    {
        $searchsop = $request->get('search');
        if (! empty($searchsop)) {
            $usersop = Sop::where('name', 'like', '%'.$searchsop.'%')->get();
        } else {
            $usersop = Sop::all();
        }
        $html = view('products.sop-search-modal-content', compact('usersop'))->render();

        return $html;
    }

    public function downloaddata($id)
    {
        $usersop = Sop::where('id', $id)->first();
        if ($usersop) {
            $data['name'] = $usersop->name;
            $data['content'] = $usersop->content;

            $html = view('maileclipse::templates.Viewdownload', [
                'name' => $usersop->name,
                'content' => $usersop->content,
                'usersop' => $usersop = Sop::where('id', $usersop->id)->first(),

            ]);

            $pdf = new Dompdf;
            $pdf->loadHtml($html);
            $pdf->render();
            $pdf->stream(date('Y-m-d H:i:s').'SOPData.pdf');
        }
    }

    public function sopPermissionData(Request $request): JsonResponse
    {
        $user_id = $request->user_id;
        $permission = SopPermission::where('user_id', $user_id)->get();

        return response()->json(['status' => true, 'permissions' => $permission]);
    }

    public function sopPermissionList(Request $request): JsonResponse
    {
        $user_id = $request->user_id;
        $sop = $request->sop;

        $permission = SopPermission::where('user_id', $user_id);
        if ($permission->count() > 0) {
            $permission->delete();
        }
        if ($sop) {
            foreach ($sop as $sp) {
                $sopPermission = new SopPermission;
                $sopPermission->user_id = $user_id;
                $sopPermission->sop_id = $sp;
                $sopPermission->save();
            }
        }

        return response()->json(['status' => true, 'message' => 'Permission Saved successfully']);
    }

    public function sopPermissionUserList(Request $request): JsonResponse
    {
        $sop = Sop::find($request->sop_id);
        $sop_users = SopPermission::where('sop_id', $request->sop_id)->get()->pluck('user_id');
        $user = User::all();

        return response()->json(['user_list' => $sop_users, 'user' => $user, 'sop' => $sop]);
    }

    public function sopRemovePermission(Request $request): JsonResponse
    {

        $user_id = $request->user_id;
        $sop_id = $request->sop_id;

        SopPermission::where('sop_id', $sop_id)->delete();
        if ($user_id) {
            foreach ($user_id as $u_id) {
                $new_permission = new SopPermission;
                $new_permission->sop_id = $sop_id;
                $new_permission->user_id = $u_id;
                $new_permission->save();
            }
        }

        return response()->json(['message' => 'Permission Apply Successfully']);
    }

    /**
     * Delete category
     */
    public function categoryDelete(Request $request): RedirectResponse
    {
        try {
            SopHasCategory::where('sop_category_id', $request->id)->delete();
            SopCategory::destroy($request->id);

            return redirect()->back()->withSuccess('Caregory delete successfully.');
        } catch (Exception $e) {
            return redirect()->back()->withError('Error while deleting category.');
        }
    }

    /**
     * Sop category update
     */
    public function categoryUpdate(Request $request): RedirectResponse
    {
        try {
            if (! isset($request->name) || $request->name == '') {
                throw new Exception('Category name must required.');
            }
            if (! isset($request->id) || $request->id == '') {
                throw new Exception('Category not found.');
            }

            $sopcategory = SopCategory::find($request->id);

            if ($sopcategory) {
                $sopcategory->category_name = $request->name;
                $sopcategory->update();
            } else {
                throw new Exception('Category not found.');
            }

            return redirect()->back()->withSuccess('Category successfully created.');
        } catch (Exception $e) {
            //throw $th;
            return redirect()->back()->withError($e->getMessage());
        }
    }

    public function getSopSearchModal(): JsonResponse
    {
        $usersop = Sop::all();
        $category_result = SopCategory::select('id', 'category_name as text')->get();
        $returnHTML = view('products.partials.modals.sop-search-modal', compact('usersop', 'category_result'))->render();

        return response()->json(['html' => $returnHTML, 'type' => 'success'], 200);
    }

    public function categorylistajax(): JsonResponse
    {
        $category_result = SopCategory::select('id', 'category_name as text')->get();
        $returnHTML = '';
        foreach ($category_result as $valueCategory) {
            $returnHTML .= '<option value="'.$valueCategory->id.'">'.$valueCategory->text.'</option>';
        }

        return response()->json(['success' => true, 'data' => $returnHTML, 'message' => 'Record found']);
    }
}
