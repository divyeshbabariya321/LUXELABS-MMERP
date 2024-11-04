<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use App\StoreWebsite;
use App\ChatbotTypeErrorLog;
use Illuminate\Http\Request;

class ChatbotTypeErrorLogController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(request $request)
    {
        $query = ChatbotTypeErrorLog::query();
        $query = $query->select('chatbot_type_error_logs.id AS chatId', 'chatbot_type_error_logs.call_sid', 'chatbot_type_error_logs.type_error', 'store_websites.website', 'chatbot_type_error_logs.phone_number');
        $query = $query->leftJoin('store_websites', 'store_websites.id', '=', 'chatbot_type_error_logs.store_website_id');
        // $query =  $query->leftJoin('chatbot_questions', 'chatbot_questions.id', '=', 'chatbot_type_error_logs.chatbot_id');
        $storeWebsites = StoreWebsite::Select('id', 'website')->get();

        if ($request->id) {
            $query = $query->where('chatbot_type_error_logs.id', $request->id);
        }
        if ($request->type != null) {
            $query = $query->where('chatbot_type_error_logs.type_error', $request->type);
        }
        if ($request->storeweb_id != null) {
            $query = $query->whereIn('chatbot_type_error_logs.store_website_id', $request->storeweb_id);
        }
        if ($request->missiong_word != null) {
            $query = $query->where('chatbot_type_error_logs.type_error', $request->missiong_word);
        }
        if ($request->call_sid != null) {
            $query = $query->where('chatbot_type_error_logs.call_sid', $request->call_sid);
        }
        if ($request->phone_number != null) {
            $query = $query->where('chatbot_type_error_logs.phone_number', $request->phone_number);
        }

        $data = $query->orderBy('chatbot_type_error_logs.id')->paginate(25)->appends(request()->except(['page']));
        if ($request->ajax()) {
            return response()->json([
                'tbody' => view('chatboat-type-error-log.index_ajax', compact('data'))->with('i', ($request->input('page', 1) - 1) * 5)->render(),
                'links' => (string) $data->render(),
                'count' => $data->total(),
            ], 200);
        }

        return view('chatboat-type-error-log.index', compact('data', 'storeWebsites'))->with('i', ($request->input('page', 1) - 1) * 5);
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
     *
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
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
    }

    /**
     * Update the specified resource in storage.
     *
     *
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, int $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     */
    public function destroy(request $request): RedirectResponse
    {
        $id = $request->id;
        if (is_array($id)) {
            ChatbotTypeErrorLog::whereIn('id', $id)->delete();
        } else {
            ChatbotTypeErrorLog::where('id', $id)->delete();
        }

        return redirect()->route('chatboat-type-error-log.index')
            ->with('success', 'Type error log deleted successfully');
    }
}
