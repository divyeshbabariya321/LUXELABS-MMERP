<?php

namespace App\Http\Controllers;

use Illuminate\View\View;
use Illuminate\Http\Request;
use App\Models\FilePermission;

class FilePermissionController extends Controller
{
    public function getFilePermissions(Request $request): View
    {
        $filePermissions = new FilePermission();

        if ($request->search_instance) {
            $filePermissions = $filePermissions->where('instance', 'LIKE', '%' . $request->search_instance . '%');
        }
        if ($request->search_owner) {
            $filePermissions = $filePermissions->where('owner', 'LIKE', '%' . $request->search_owner . '%');
        }
        if ($request->server_ids) {
            $filePermissions = $filePermissions->WhereIn('server', $request->server_ids);
        }
        if ($request->search_gp_owner) {
            $filePermissions = $filePermissions->where('groupowner', 'LIKE', '%' . $request->search_gp_owner . '%');
        }
        if ($request->search_permission) {
            $filePermissions = $filePermissions->where('permission', 'LIKE', '%' . $request->search_permission . '%');
        }

        $filePermissions = $filePermissions->latest()->paginate(25);

        return view('file-permission.file-permission-list', compact('filePermissions'));
    }
}
