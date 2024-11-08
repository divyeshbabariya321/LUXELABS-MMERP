<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePermissionRequest;
use App\Http\Requests\UpdatePermissionRequest;
use App\Permission;

use App\Models\PermissionUser;

use App\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\View\View;

class PermissionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $query = Permission::query();

        if ($request->term) {
            $query = $query->where('route', 'LIKE', '%'.$request->term.'%');
        }

        $users = User::where('is_active', 1)->get();

        $permissions = $query->orderByDesc('id')->paginate(25)->appends(request()->except(['page']));

        if ($request->ajax()) {
            return response()->json([
                'tbody' => view('permissions.partials.list-permission', compact('permissions'))->with('i', ($request->input('page', 1) - 1) * 5)->render(),
                'links' => (string) $permissions->render(),
                'count' => $permissions->total(),
            ], 200);
        }

        return view('permissions.index', compact('users', 'permissions'))->with('i', ($request->input('page', 1) - 1) * 10);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        return view('permissions.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StorePermissionRequest $request): RedirectResponse
    {
        $permission = new Permission;
        $permission->name = $request->name;
        $permission->route = $request->route;
        $permission->save();

        return redirect()->back()
            ->with('success', 'Permission created successfully');
    }

    /**
     * Display the specified resource.
     */
    public function show(int $id): View
    {
        $permissions = Permission::find($id);

        return view('permissions.show', compact('permissions'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(int $id): View
    {
        $permissions = Permission::find($id);

        return view('permissions.edit', compact('permissions'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdatePermissionRequest $request, int $id): RedirectResponse
    {

        $permission = Permission::find($id);
        $permission->name = $request->input('name');
        $permission->route = $request->input('route');
        $permission->save();

        return redirect()->route('permissions.index')
            ->with('success', 'Role updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(int $id): RedirectResponse
    {
        $permission = Permission::find($id);
        $permission->delete();

        return redirect()->route('permissions.index')
            ->with('success', 'Role deleted successfully');
    }

    public function delete_record(Request $request): JsonResponse
    {
        $ids = $request->user_id;
        PermissionUser::whereIn('user_id', explode(',', $ids))->delete();

        return response()->json(['success' => 'Deleted successfully.']);
    }

    public function users(Request $request): View
    {
        $users = User::where('users.is_active', 1)->orderBy('name');
        $permissions = Permission::orderBy('name');

        $permission_datas = Permission::orderBy('name')->get();
        $user_datas = User::where('is_active', 1)->orderBy('name')->get();

        if (! empty($request->assign_permission) && in_array('1', $request->assign_permission) && ! in_array('0', $request->assign_permission)) {
            $users = $users->select('users.*')->join('permission_user', 'permission_user.user_id', '=', 'users.id')->join('permissions', 'permission_user.permission_id', '=', 'permissions.id')->groupBy('permission_user.user_id');

            if ($request->search_row) {
                $users = $users->whereIn('permissions.name', $request->search_row);
            }

            if ($request->search_user) {
                $permissions = Permission::whereIn('permissions.id', function ($query) {
                    $query->select('permissions.id')->from('permissions')->join('permission_user', 'permissions.id', '=', 'permission_user.permission_id')->whereIn('permission_user.user_id', \Illuminate\Support\Facades\Request::input('search_user'));
                })->orderBy('permissions.name');
            } else {
                $permissions = Permission::whereIn('permissions.id', function ($query) {
                    $query->select('permissions.id')->from('permissions')->join('permission_user', 'permissions.id', '=', 'permission_user.permission_id');
                })->orderBy('permissions.name');
            }
        }

        if (! empty($request->assign_permission) && in_array('0', $request->assign_permission) && ! in_array('1', $request->assign_permission)) {
            if (! $request->search_user) {
                $users = $users->whereNotIn(
                    'users.id', function ($query) {
                        $query->select('permission_user.user_id')->from('permission_user');
                    });
            }
            if ($request->search_user) {
                $where       = PermissionUser::whereIn('user_id', $request->search_user)->pluck('permission_id');
                $permissions = Permission::whereIn('permissions.id', function ($query) {

                    $query->select('permissions.id')->from('permissions')->join('permission_user', 'permissions.id', '!=', 'permission_user.permission_id');
                })->whereNotIn('permissions.id', $where)->orderBy('permissions.name');
            } else {
                $permissions = Permission::whereIn('permissions.id', function ($query) {
                    $query->select('permissions.id')->from('permissions')->join('permission_user', 'permissions.id', '!=', 'permission_user.permission_id');
                })->orderBy('permissions.name');
            }
        }

        if ($request->search_row) {
            if (! $request->assign_permission) {
                $users = $users->select('users.*')->join('permission_user', 'permission_user.user_id', '=', 'users.id')->join('permissions', 'permission_user.permission_id', '=', 'permissions.id')->groupBy('permission_user.user_id');
                $users = $users->whereIn('permissions.name', $request->search_row);
            }

            $permissions = $permissions->whereIn('permissions.name', $request->search_row);
        }

        if ($request->search_user) {
            $users = $users->whereIn('users.id', $request->search_user);
        }

        $users = $users->paginate(10);

        $permissions = $permissions->get();

        return view('permissions.users', compact('users', 'permissions', 'user_datas', 'permission_datas'))->with('i', ($request->input('page', 1) - 1) * 10);
    }

    /**
     * @SWG\Get(
     *   path="/users/updatePermission",
     *   tags={"Permission"},
     *   summary="update permission",
     *   operationId="update-permission",
     *
     *   @SWG\Response(response=200, description="successful operation"),
     *   @SWG\Response(response=406, description="not acceptable"),
     *   @SWG\Response(response=500, description="internal server error"),
     *
     *      @SWG\Parameter(
     *          name="mytest",
     *          in="path",
     *          required=true,
     *          type="string"
     *      ),
     * )
     */
    public function updatePermission(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
            'permission_id' => 'required',
            'is_active' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'code' => 400,
                'errors' => $validator->errors(),
            ], 400);
        }
        $user_id = $request->user_id;
        $permission_id = $request->permission_id;
        $is_Active = $request->is_active;
        $user = User::findorfail($user_id);
        //ADD PERMISSION
        if ($is_Active == 0) {
            $user->permissions()->attach($permission_id);
            $message = 'Permission added Successfully';
        }
        //REMOVE PERMISSION
        if ($is_Active == 1) {
            $user->permissions()->detach($permission_id);
            $message = 'Permission removed Successfully';
        }

        $data = [
            'success' => true,
            'message' => $message,
        ];

        return response()->json($data);
    }
}
