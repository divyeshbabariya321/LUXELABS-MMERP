<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateRoleRequest;
use App\Http\Requests\StoreRoleRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use App\Role;
use App\Permission;
use Illuminate\Http\Request;

class RoleController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function __construct()
    {
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $query = Role::query();

        if ($request->term != null) {
            $query = $query->whereIn('id', $request->term);
        }

        $roles      = $query->orderByDesc('id')->paginate(25)->appends(request()->except(['page']));
        $permission = Permission::get();
        if ($request->ajax()) {
            return response()->json([
                'tbody' => view('roles.partials.list-roles', compact('roles'))->with('i', ($request->input('page', 1) - 1) * 5)->render(),
                'links' => (string) $roles->render(),
                'count' => $roles->total(),
            ], 200);
        }

        return view('roles.index', compact('roles', 'permission'))->with('i', ($request->input('page', 1) - 1) * 10);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $permission = Permission::get();

        return view('roles.create', compact('permission'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreRoleRequest $request): RedirectResponse
    {
        $role       = new Role();
        $role->name = $request->name;
        $role->save();

        $role->permissions()->sync($request->input('permission'));

        return redirect()->route('roles.index')
            ->with('success', 'Role created successfully');
    }

    /**
     * Display the specified resource.
     *
     *
     * @return \Illuminate\Http\Response
     */
    public function show(int $id)
    {
        $role            = Role::find($id);
        $rolePermissions = $role->permissions;
        $data            = [
            'role'            => $role,
            'rolePermissions' => $rolePermissions,
        ];

        return $data;
    }

    /**
     * Show the form for editing the specified resource.
     *
     *
     * @return \Illuminate\Http\Response
     */
    public function edit(int $id)
    {
        $role            = Role::find($id);
        $permission      = Permission::get();
        $rolePermissions = $role->permissions;

        $data = [
            'role'            => $role,
            'rolePermissions' => $rolePermissions,
            'permission'      => $permission,
        ];

        return $data;
    }

    /**
     * Update the specified resource in storage.
     *
     *
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateRoleRequest $request, int $id)
    {

        $role       = Role::find($id);
        $role->name = $request->input('role_name');
        $role->save();

        $role->permissions()->sync($request->input('permission1'));
        $data = ['success' => 'Role updated successfully'];

        return $data;
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(int $id): RedirectResponse
    {
        Role::delete($id);

        return redirect()->route('roles.index')
            ->with('success', 'Role deleted successfully');
    }

    public function unAuthorized(): View
    {
        return view('errors.401');
    }

    public function search_role(Request $request)
    {
        $permission = Permission::where('name', 'LIKE', '%' . $request->search_role . '%')->get();

        return $permission;
    }
}
