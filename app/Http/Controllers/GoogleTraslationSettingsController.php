<?php

namespace App\Http\Controllers;

use App\googleTraslationSettings;
use App\Http\Requests\GoogleTraslationSettingsStoreRequest;
use App\Http\Requests\UpdateGoogleTraslationSettingRequest;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class GoogleTraslationSettingsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $settings = googleTraslationSettings::query();

        if ($request->term) {
            $settings->where(function ($q) use ($request) {
                $q = $q->orWhere('email', 'LIKE', '%'.$request->term.'%')
                    ->orWhere('account_json', 'LIKE', '%'.$request->term.'%')
                    ->orWhere('last_note', 'LIKE', '%'.$request->term.'%')
                    ->orWhere('project_id', 'LIKE', '%'.$request->term.'%');
            });
        }

        $settings = $settings->get();
        $is_free = $settings->first()->is_free;

        if ($request->ajax()) {
            return response()->json([
                'tbody' => view('googleTraslationSettings.list', compact('settings'))->render(),
            ], 200);
        }

        return view('googleTraslationSettings.index', compact('settings', 'is_free'));
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
    public function store(GoogleTraslationSettingsStoreRequest $request): RedirectResponse
    {
        try {
            $email = $request->email;
            $account_json = $request->account_json;
            $status = $request->status;
            $last_note = $request->last_note;
            $project_id = $request->project_id;

            $googleTraslationSettings = new googleTraslationSettings;

            $googleTraslationSettings->email = $email;
            $googleTraslationSettings->account_json = $account_json;
            $googleTraslationSettings->status = $status;
            $googleTraslationSettings->last_note = $last_note;
            $googleTraslationSettings->project_id = $project_id;
            $googleTraslationSettings->save();

            $msg = 'Setting Add Successfully';

            return redirect()->route('google-traslation-settings.index')->with('success', $msg);
        } catch (Exception $e) {
            return redirect()->route('google-traslation-settings.index')->with('error', $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function show(googleTraslationSettings $googleTraslationSettings)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  mixed  $id
     */
    public function edit($id, googleTraslationSettings $googleTraslationSettings): View
    {
        $data = googleTraslationSettings::where('id', $id)->first();

        return view('googleTraslationSettings.edit', compact('data'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateGoogleTraslationSettingRequest $request, googleTraslationSettings $googleTraslationSettings): RedirectResponse
    {
        try {
            $id = $request->id;
            $email = $request->email;
            $account_json = $request->account_json;
            $status = $request->status;
            $last_note = $request->last_note;
            $project_id = $request->project_id;

            $googleTraslationSettings = new googleTraslationSettings;
            $googleTraslationSettings->where('id', $id)
                ->limit(1)
                ->update([
                    'email' => $email,
                    'account_json' => $account_json,
                    'status' => $status,
                    'last_note' => $last_note,
                    'project_id' => $project_id,
                ]);

            return redirect()->route('google-traslation-settings.index')->with('success', 'Setting Update Successfully');
        } catch (Exception $e) {
            return redirect()->route('google-traslation-settings.index')->with('error', $e->getMessage());
        }
    }

    public function updateTranslationPlan(Request $request): JsonResponse
    {
        try {

            $is_free = ($request->is_free == 'Paid') ? '1' : '0';

            googleTraslationSettings::query()->update(['is_free' => $is_free]);

            return response()->json(['code' => 200, 'message' => 'Translation Plan Updated Successfully!']);

        } catch (Exception $e) {

            return response()->json(['code' => 200, 'message' => $e->getMessage()]);

        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $googleTraslationSettings): RedirectResponse
    {
        googleTraslationSettings::where('id', $googleTraslationSettings->setting)->delete();

        $msg = 'Setting Delete Successfully';

        return redirect()->route('google-traslation-settings.index')->with('success', $msg);
    }
}
