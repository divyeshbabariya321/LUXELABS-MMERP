<?php

namespace App\Http\Controllers;

use App\Complaint;
use App\ComplaintThread;
use App\Http\Requests\StoreThreadRequest;
use App\Http\Requests\UpdateThreadRequest;
use App\StatusChange;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Plank\Mediable\Facades\MediaUploader as MediaUploader;

class ThreadController extends Controller
{
    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreThreadRequest $request): RedirectResponse
    {

        $data = $request->except('_token');

        $complaint = Complaint::create($data);

        if ($request->thread[0] != null) {
            foreach ($request->thread as $key => $thread) {
                ComplaintThread::create([
                    'complaint_id' => $complaint->id,
                    'account_id' => array_key_exists($key, $request->account_id) ? $request->account_id[$key] : '',
                    'thread' => $thread,
                ]);
            }
        }

        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $media = MediaUploader::fromSource($image)->toDirectory('reviews-images')->upload();
                $complaint->attachMedia($media, config('constants.media_tags'));
            }
        }

        return redirect()->route('review.index')->withSuccess('You have successfully added complaint');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateThreadRequest $request, int $id): RedirectResponse
    {

        $data = $request->except('_token');

        $complaint = Complaint::find($id);
        $complaint->update($data);

        if ($request->thread[0] != null) {
            $complaint->threads()->delete();

            foreach ($request->thread as $key => $thread) {
                ComplaintThread::create([
                    'complaint_id' => $complaint->id,
                    'account_id' => array_key_exists($key, $request->account_id) ? $request->account_id[$key] : '',
                    'thread' => $thread,
                ]);
            }
        }

        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $media = MediaUploader::fromSource($image)->toDirectory('reviews-images')->upload();
                $complaint->attachMedia($media, config('constants.media_tags'));
            }
        }

        return redirect()->route('review.index')->withSuccess('You have successfully updated complaint');
    }

    public function updateStatus(Request $request, $id): Response
    {
        $complaint = Complaint::find($id);

        StatusChange::create([
            'model_id' => $complaint->id,
            'model_type' => Complaint::class,
            'user_id' => Auth::id(),
            'from_status' => $complaint->status,
            'to_status' => $request->status,
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

        return redirect()->route('review.index')->withSuccess('You have successfully deleted complaint');
    }
}
