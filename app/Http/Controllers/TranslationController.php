<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTranslationRequest;
use App\Http\Requests\UpdateTranslationRequest;
use App\Loggers\TranslateLog;
use App\Translations;
use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TranslationController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(request $request)
    {
        $query = Translations::query();

        if ($request->id) {
            $query = $query->where('id', $request->id);
        }
        if ($request->term) {
            $query = $query->where('text_original', 'LIKE', '%'.$request->term.'%')
                ->orWhere('created_at', 'LIKE', '%'.$request->term.'%')
                ->orWhere('updated_at', 'LIKE', '%'.$request->term.'%');
        }

        if ($request->translation_from) {
            $query = $query->where('from', $request->translation_from);
        }

        if ($request->translation_to) {
            $query = $query->where('to', $request->translation_to);
        }

        $data = $query->orderByDesc('id')->paginate(25)->appends(request()->except(['page']));

        $from = Translations::groupBy('from')->get();
        $to = Translations::groupBy('to')->get();

        if ($request->ajax()) {
            return response()->json([
                'tbody' => view('translation.partials.list-translation', compact('data', 'from', 'to'))->with('i', ($request->input('page', 1) - 1) * 5)->render(),
                'links' => (string) $data->render(),
                'count' => $data->total(),
            ], 200);
        }

        return view('translation.index', compact('data', 'from', 'to'))
            ->with('i', ($request->input('page', 1) - 1) * 5);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $translation = Translations::all();
        $from = Translations::groupBy('from')->get();
        $to = Translations::groupBy('to')->get();

        return view('translation.create', compact('translation', 'from', 'to'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreTranslationRequest $request): RedirectResponse
    {
        $insert = Translations::create($request->except('_token'));

        return redirect()->to('/translation/'.$insert->id.'/edit')->with('success', 'Translation created successfully');
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
        $translation = Translations::where('id', $id)->first();
        $from = Translations::groupBy('from')->get();
        $to = Translations::groupBy('to')->get();

        return view('translation.edit', compact('translation', 'from', 'to'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  int  $id
     */
    public function update(UpdateTranslationRequest $request): RedirectResponse
    {
        $id = $request->input('id');
        $insert = Translations::where('id', $id)->update($request->except('_token'));

        return redirect()->back()->with('success', 'Translation updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(int $id): RedirectResponse
    {
        $Translations = Translations::find($id);
        $Translations->delete();

        return redirect()->route('translation.list')
            ->with('success', 'Translation deleted successfully');
    }

    public function translateLogDelete($id): RedirectResponse
    {
        $translateLog = TranslateLog::find($id);
        $translateLog->delete();

        return redirect()->back()
            ->with('success', 'Translation Log deleted successfully');
    }

    public function translateLog(Request $request)
    {
        $query = TranslateLog::query();

        if ($request->id) {
            $query = $query->where('id', $request->id);
        }
        if ($request->account_id) {
            $query = $query->where('google_traslation_settings_id', $request->account_id);
        }

        if ($request->search) {
            $query = $query->where('messages', 'LIKE', '%'.$request->search.'%')
                ->orWhere('created_at', 'LIKE', '%'.$request->search.'%')
                ->orWhere('updated_at', 'LIKE', '%'.$request->search.'%');
        }

        $data = $query->orderByDesc('id')->paginate(25)->appends(request()->except(['page']));
        if ($request->ajax()) {
            return response()->json([
                'tbody' => view('translation.partials.list-translation-logs', compact('data'))->with('i', ($request->input('page', 1) - 1) * 5)->render(),
                'links' => (string) $data->render(),
                'count' => $data->total(),
            ], 200);
        }

        return view('translation.log', compact('data'))
            ->with('i', ($request->input('page', 1) - 1) * 5);
    }

    /**
     * Soft deletes the log
     */
    public function markAsResolve(Request $request)
    {
        try {
            if (isset($request->id)) {
                TranslateLog::find($request->id)->delete();

                return true;
            }
        } catch (Exception $e) {
            return response()->json(['code' => 500, 'message' => 'Opps! Something went wrong, Please try again.']);
        }

        return false;
    }
}
