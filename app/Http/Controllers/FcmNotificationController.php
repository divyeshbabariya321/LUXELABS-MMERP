<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreFcmNotificationRequest;
use App\Http\Requests\UpdateFcmNotificationRequest;
use App\PushFcmNotification;
use App\PushFcmNotificationHistory;
use App\StoreWebsite;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class FcmNotificationController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(request $request)
    {
        $query = PushFcmNotification::query();

        if ($request->id) {
            $query = $query->where('id', $request->id);
        }
        if ($request->term) {
            $query = $query->where('title', 'LIKE', '%'.$request->term.'%')
                ->orWhere('body', 'LIKE', '%'.$request->term.'%')
                ->orWhere('url', 'LIKE', '%'.$request->term.'%')
                ->orWhere('created_by', 'LIKE', '%'.$request->term.'%');
        }

        $data = $query->leftJoin('users as usr', 'usr.id', 'push_fcm_notifications.created_by')
            ->select('push_fcm_notifications.*', 'usr.name as username')->orderByDesc('id')->paginate(25)->appends(request()->except(['page']));
        if ($request->ajax()) {
            return response()->json([
                'tbody' => view('pushfcmnotification.partials.list-notification', compact('data'))->with('i', ($request->input('page', 1) - 1) * 5)->render(),
                'links' => (string) $data->render(),
                'count' => $data->total(),
            ], 200);
        }
        $StoreWebsite = StoreWebsite::select('id', 'website')->groupBy('website')->get();

        return view('pushfcmnotification.index', compact('data', 'StoreWebsite'))
            ->with('i', ($request->input('page', 1) - 1) * 5);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $StoreWebsite = StoreWebsite::select('id', 'website')->groupBy('website')->get();

        return view('pushfcmnotification.create', compact('StoreWebsite'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreFcmNotificationRequest $request): RedirectResponse
    {
        $StoreWebsiteId = StoreWebsite::where('website', $request->input('url'))->first()->id;
        $input = $request->all();
        $input['sent_at'] = $request->sent_at;
        $input['store_website_id'] = $StoreWebsiteId;
        $input['created_by'] = Auth::id();
        $input['status'] = 'Pending';
        PushFcmNotification::create($input);

        return redirect()->route('pushfcmnotification.list')->with('success', 'Notification created successfully');
    }

    /**
     * Display the specified resource.
     *
     *
     * @return \Illuminate\Http\Response
     */
    public function show(int $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(int $id): View
    {
        $StoreWebsite = StoreWebsite::select('id', 'website')->groupBy('website')->get();
        $Notification = PushFcmNotification::where('id', $id)->first();

        return view('pushfcmnotification.edit', compact('StoreWebsite', 'Notification'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  int  $id
     */
    public function update(UpdateFcmNotificationRequest $request): RedirectResponse
    {
        $StoreWebsiteId = StoreWebsite::where('website', $request->input('url'))->first()->id;
        $input = $request->except(['_token']);
        $input['store_website_id'] = $StoreWebsiteId;
        $input['created_by'] = Auth::id();
        PushFcmNotification::where('id', $request->id)->update($input);

        return redirect()->back()->with('success', 'Notification updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(int $id): RedirectResponse
    {
        $PushFcmNotification = PushFcmNotification::find($id);
        $PushFcmNotification->delete();

        return redirect()->route('pushfcmnotification.list')
            ->with('success', 'Notification deleted successfully');
    }

    public function errorList(Request $request): View
    {
        $errors = PushFcmNotificationHistory::where('notification_id', $request->id)->where('success', 0)->latest()->get();

        return view('pushfcmnotification.partials.error-list', compact('errors'));
    }
}
