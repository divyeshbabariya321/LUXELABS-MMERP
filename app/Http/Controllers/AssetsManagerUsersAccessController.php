<?php

namespace App\Http\Controllers;

use App\Models\AssetManagerUserAccess;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AssetsManagerUsersAccessController extends Controller
{
    public function index(Request $request): View
    {
        $user_accesses = new AssetManagerUserAccess;
        $user_accesses = $user_accesses::with(['user'])->leftJoin('users', 'users.id', 'asset_manager_user_accesses.user_id')->select('asset_manager_user_accesses.*', 'users.name AS selectedUser')->orderByDesc('created_at');

        $keyword = request('keyword', '');
        $created_by = request('created_by');

        if (! empty($keyword)) {
            $user_accesses = $user_accesses->where(function ($q) use ($keyword) {
                $q->where('asset_manager_user_accesses.username', 'LIKE', '%'.$keyword.'%');
            });
        }

        if (! empty($created_by)) {
            $user_accesses = $user_accesses->whereIn('asset_manager_user_accesses.created_by', $created_by);
        }

        $user_accesses = $user_accesses->orderByDesc('created_at')->get();

        return view('assets-manager.user-access-listing', ['user_accesses' => $user_accesses]);
    }

    public function deleteUserAccessList($id): RedirectResponse
    {
        $user_access = AssetManagerUserAccess::where('id', $id)->first();

        // Base URL
        $url = 'https://demo.mio-moda.com:10000/virtual-server/remote.cgi';

        // Parameters
        $params = [
            'program' => 'delete-user',
            'domain' => 'demo.mio-moda.com',
            'user' => $user_access->username,
        ];

        // Append parameters to URL
        $url .= '?'.http_build_query($params);

        $token = 'webapi:W34wVZIGIzf3bjq';

        // Initialize cURL session
        $ch = curl_init($url);

        // Set cURL options
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); // Return response as a string
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true); // Ignore SSL certificate verification (for development purposes only)
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Authorization: Basic '.base64_encode($token),
        ]);

        // Execute cURL session and get the response
        $response = curl_exec($ch);

        // Close cURL session
        curl_close($ch);

        $access = AssetManagerUserAccess::find($id);
        $access->delete();

        return redirect()->route('user-accesses.index')->with('success', 'Assets manager user access deleted successfully');
    }
}
