<?php

namespace App\Providers;

use App\CustomerLiveChat;
use App\LivechatincSetting;
use App\Models\MonitorServer;
use App\PermissionRequest;
use App\ReplyCategory;
use App\StoreWebsite;
use App\TimeDoctor\TimeDoctorLog;
use App\TodoStatus;
use App\User;
use App\Vendor;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use App\DeveloperModule;

class ViewServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void {}

    /**
     * Bootstrap services.
     */

    public function boot(Request $request): void
    {
        View::composer(['layouts.app'], function ($view) use ($request) {
            $auth_user = $request->user();
            $route_name = request()->route()->getName();
            $isAdmin = $auth_user ? $auth_user->isAdmin() : false;
            
            $view->with('route_name', $route_name)
                ->with('isAdmin', $isAdmin);

            if (!$auth_user) {
                return;
            }

            $status = MonitorServer::where('status', 'off')->count();
            $currentLogs = $this->getCurrentLogs($auth_user);
            $permissionCount = PermissionRequest::count();
            $statuses = TodoStatus::all();
            $vendors = Vendor::select('name', 'id')->get();
            $key_ls = LivechatincSetting::select('key')->first();
            $chatIds = $this->getCachedChatIds();
            $newMessageCount = CustomerLiveChat::select('id')->where('seen', 0)->count();
            $websites = StoreWebsite::all();
            $quickDevUsersArray = User::role('Developer')->pluck('name', 'id')->toArray();
            $quickTasksModules = $this->getCachedDeveloperModules();
            $vendorsArray = $vendors->pluck('name', 'id')->toArray();
            $replyCategories = $this->getCachedReplyCategories();

            $view->with(compact(
                'status',
                'currentLogs',
                'permissionCount',
                'statuses',
                'key_ls',
                'chatIds',
                'newMessageCount',
                'websites',
                'quickDevUsersArray',
                'quickTasksModules',
                'vendorsArray',
                'replyCategories'
            ));
        });
    }

    private function getCurrentLogs($auth_user)
    {
        $logsQuery = TimeDoctorLog::query()->with('user');
        $currentDate = Carbon::now()->format('Y-m-d');

        if (!($auth_user->isAdmin())) {
            $logsQuery->where('user_id', $auth_user->id);
        }

        return $logsQuery->where(DB::raw('DATE(created_at)'), $currentDate)->count();
    }

    private function getCachedChatIds()
    {
        return Cache::remember('CustomerLiveChat::with::customer::orderby::seen_asc', 60 * 60 * 24 * 1, function () {
            return CustomerLiveChat::with('customer')
                ->orderBy('seen')
                ->orderByDesc('status')
                ->get();
        });
    }

    private function getCachedDeveloperModules()
    {
        return Cache::remember('DeveloperModule::all', 60 * 60 * 24 * 1, function () {
            return DeveloperModule::all();
        });
    }

    private function getCachedReplyCategories()
    {
        return Cache::remember('reply_categories', 60 * 60 * 24 * 1, function () {
            return ReplyCategory::select('id', 'name')
                ->with('approval_leads')
                ->orderBy('name', 'ASC')
                ->get();
        });
    }
}
