<?php

namespace App\Http\Middleware;

use Symfony\Component\HttpFoundation\Response;
use Closure;
use Illuminate\Http\Request;
use App\Permission;
use App\PermissionRequest;
use App\Helpers\PermissionCheck;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

class CheckPermission
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (auth()->check() == true) {
            $currentPath = Route::getFacadeRoot()->current()->uri();
            // $permission  = new PermissionCheck();
            // $per         = $permission->checkUser($currentPath);
            $per         = checkUser($currentPath);
            if ($per == true) {
                return $next($request);
            } else {
                $url     = explode('/', $currentPath);
                $model   = $url[0];
                $actions = end($url);
                if ($model != '') {
                    if ($model == $actions) {
                        $genUrl = $model . '-list';
                    } else {
                        $genUrl = $model . '-' . $actions;
                    }
                }
                if (! isset($genUrl)) {
                    $genUrl = '';
                }
                // dd($genUrl);
                $permission = Permission::where('route', $genUrl)->first();
                
                if (isset($permission->route)) {
                    PermissionRequest::updateOrcreate([
                        'user_id'       => Auth::user()->id,
                        'permission_id' => $permission->id,
                    ],
                        [
                            'user_id'         => Auth::user()->id,
                            'permission_id'   => $permission->id,
                            'request_date'    => date('Y-m-d H:i:s'),
                            'permission_name' => $permission->route,
                        ]);
                        // dd($permission->route);
                    echo 'unauthorized permission name : - ' . $permission->route;
                    exit();
                }
                else{
                    return $next($request);
                }
            }
        } else {
            return redirect()->route('login');
        }
    }
}
