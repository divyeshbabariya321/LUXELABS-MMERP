<?php

namespace App\Http\Controllers;

use App\Models\MagentoSettingRevisionHistory;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MagentoSettingRevisionHistoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $keyword = $request->get('keyword');
        $status = $request->get('status');
        $active = $request->get('active');
        $date = $request->get('date');

        $magentoSettingRevisionHistories = MagentoSettingRevisionHistory::latest();

        if (! empty($keyword)) {
            $magentoSettingRevisionHistories = $magentoSettingRevisionHistories->where(function ($q) use ($keyword) {
                $q->orWhere('setting', 'LIKE', '%'.$keyword.'%')
                    ->orWhere('log', 'LIKE', '%'.$keyword.'%')
                    ->orWhere('config_revision', 'LIKE', '%'.$keyword.'%');
            });
        }

        if (isset($status)) {
            $magentoSettingRevisionHistories = $magentoSettingRevisionHistories->where('status', $status);
        }

        if (isset($active)) {
            $magentoSettingRevisionHistories = $magentoSettingRevisionHistories->where('active', $active);
        }

        if ($date) {
            $magentoSettingRevisionHistories = $magentoSettingRevisionHistories->whereDate('date', $date);
        }

        $magentoSettingRevisionHistories = $magentoSettingRevisionHistories->paginate(25);

        return view('magento-setting-revision-history.index', compact('magentoSettingRevisionHistories'));
    }
}
