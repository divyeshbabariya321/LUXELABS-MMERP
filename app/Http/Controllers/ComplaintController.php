<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateComplaintRequest;
use App\Http\Requests\StoreComplaintRequest;
use Illuminate\View\View;
use Illuminate\Http\Response;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\User;
use App\Account;
use App\Helpers;
use App\Setting;
use App\Customer;
use App\Complaint;
use App\StatusChange;
use App\ComplaintThread;
use Illuminate\Http\Request;
use Plank\Mediable\Facades\MediaUploader as MediaUploader;

class ComplaintController extends Controller
{
    public function index(Request $request): View
    {
        $filter_platform    = $request->platform ?? '';
        $filter_posted_date = $request->posted_date ?? '';
        $users_array        = Helpers::getUserArray(User::all());

        if ($request->platform != null) {
            $complaints = Complaint::where('platform', $request->platform);
        }

        if ($request->posted_date != null) {
            if ($request->platform != null) {
                $complaints = $complaints->where('date', $request->posted_date);
            } else {
                $complaints = Complaint::where('date', $request->posted_date);
            }
        }

        if ($request->platform == null && $request->posted_date == null) {
            $complaints = (new Complaint)->newQuery();
        }

        $complaints     = $complaints->where('thread_type', 'complaint')->latest()->paginate(Setting::get('pagination'), ['*'], 'complaints-page');
        $customers      = Customer::select(['id', 'name', 'email', 'instahandler', 'phone'])->get();
        $accounts_array = Account::select(['id', 'first_name', 'last_name', 'email'])->get();

        $mediaTags =  config('constants.media_tags'); // Use config variable

        return view('complaints.index', [
            'complaints'         => $complaints,
            'filter_platform'    => $filter_platform,
            'filter_posted_date' => $filter_posted_date,
            'users_array'        => $users_array,
            'customers'          => $customers,
            'accounts_array'     => $accounts_array,
            'mediaTags'          => $mediaTags    
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreComplaintRequest $request): RedirectResponse
    {

        $data = $request->except('_token');

        $complaint = Complaint::create($data);

        if ($request->thread[0] != null) {
            foreach ($request->thread as $key => $thread) {
                ComplaintThread::create([
                    'complaint_id' => $complaint->id,
                    'account_id'   => array_key_exists($key, $request->account_id) ? $request->account_id[$key] : '',
                    'thread'       => $thread,
                ]);
            }
        }

        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $media = MediaUploader::fromSource($image)->toDirectory('reviews-images')->upload();
                $complaint->attachMedia($media, config('constants.media_tags'));
            }
        }

        return redirect()->route('complaint.index')->withSuccess('You have successfully added complaint');
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
     *
     *
     * @return \Illuminate\Http\Response
     */
    public function edit(int $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateComplaintRequest $request, int $id): RedirectResponse
    {

        $data = $request->except('_token');

        $complaint = Complaint::find($id);
        $complaint->update($data);

        if ($request->thread[0] != null) {
            $complaint->threads()->delete();

            foreach ($request->thread as $key => $thread) {
                ComplaintThread::create([
                    'complaint_id' => $complaint->id,
                    'account_id'   => array_key_exists($key, $request->account_id) ? $request->account_id[$key] : '',
                    'thread'       => $thread,
                ]);
            }
        }

        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $media = MediaUploader::fromSource($image)->toDirectory('reviews-images')->upload();
                $complaint->attachMedia($media, config('constants.media_tags'));
            }
        }

        return redirect()->route('complaint.index')->withSuccess('You have successfully updated complaint');
    }

    public function updateStatus(Request $request, $id): Response
    {
        $complaint = Complaint::find($id);

        StatusChange::create([
            'model_id'    => $complaint->id,
            'model_type'  => Complaint::class,
            'user_id'     => Auth::id(),
            'from_status' => $complaint->status,
            'to_status'   => $request->status,
        ]);

        $complaint->status = $request->status;
        $complaint->save();

        return response('success');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(int $id): RedirectResponse
    {
        $complaint = Complaint::find($id);
        $complaint->threads()->delete();
        $complaint->internal_messages()->delete();
        $complaint->plan_messages()->delete();
        $complaint->remarks()->delete();
        if ($complaint->hasMedia(config('constants.media_tags'))) {
            foreach ($complaint->getMedia(config('constants.media_tags')) as $image) {
                Storage::delete($image->getDiskPath());
            }

            $complaint->detachMediaTags(config('constants.media_tags'));
        }

        $complaint->delete();

        return redirect()->route('complaint.index')->withSuccess('You have successfully deleted complaint');
    }
}
