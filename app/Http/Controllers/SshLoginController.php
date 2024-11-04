<?php

namespace App\Http\Controllers;

use Illuminate\View\View;
use App\Models\SshLogin;
use Illuminate\Http\Request;

class SshLoginController extends Controller
{
    public function getSshLogins(Request $request): View
    {
        $logs = new SshLogin();

        if ($request->user) {
            $logs = $logs->where('user', 'LIKE', '%' . $request->user . '%');
        }
        if ($request->search_message) {
            $logs = $logs->where('message', 'LIKE', '%' . $request->search_message . '%');
        }
        if ($request->ip_ids) {
            $logs = $logs->WhereIn('ip', $request->ip_ids);
        }
        if ($request->search_status) {
            $logs = $logs->where('status', 'LIKE', '%' . $request->search_status . '%');
        }
        if ($request->date) {
            $logs = $logs->where('logintime', 'LIKE', '%' . $request->date . '%');
        }

        $logs = $logs->latest()->paginate(25);

        return view('ssh-logins.ssh-logins-list', compact('logs'));
    }
}
