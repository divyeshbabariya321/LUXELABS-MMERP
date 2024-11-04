<?php

namespace Modules\ChatBot\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\JsonResponse;
use App\ChatbotKeyword;
use App\ChatbotQuestion;
use App\ChatbotKeywordValue;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\ChatbotKeywordValueTypes;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Validator;
use App\Library\Watson\Model as WatsonManager;

class KeywordController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        dd('We are not using this page anymore');
        $chatKeywords = ChatbotKeyword::leftJoin('chatbot_keyword_values as ckv', 'ckv.chatbot_keyword_id', 'chatbot_keywords.id')
            ->select('chatbot_keywords.*', DB::raw('group_concat(ckv.value) as `values`'))
            ->groupBy('chatbot_keywords.id')
            ->orderByDesc('chatbot_keywords.id')
            ->paginate(10);

        return view('chatbot::keyword.index', compact('chatKeywords'));
    }

    public function create(): View
    {
        dd('We are not using this page anymore');

        return view('chatbot::keyword.create');
    }

    public function save(Request $request): JsonResponse
    {
        dd('We are not using this page anymore');
        $params            = $request->all();
        $params['keyword'] = str_replace(' ', '_', preg_replace('/\s+/', ' ', $params['keyword']));

        $validator = Validator::make($params, [
            'keyword' => 'required|unique:chatbot_keywords|max:255',
        ]);

        if ($validator->fails()) {
            $errors = human_error_array($validator->messages()->get('*'));

            return response()->json(['code' => 500, 'error' => $errors]);
        }

        $chatbotKeyword = ChatbotKeyword::create($params);

        if (array_key_exists('value', $params) && $params['value'] != null) {
            $params['chatbot_keyword_id'] = $chatbotKeyword->id;
            $chatbotKeywordValue          = new ChatbotKeywordValue;
            $chatbotKeywordValue->fill($params);
            $chatbotKeywordValue->save();

            $valueType                             = [];
            $valueType['chatbot_keyword_value_id'] = $chatbotKeywordValue->id;
            if (! empty($params['type'])) {
                foreach ($params['type'] as $value) {
                    if ($value != null) {
                        $valueType['type']        = $value;
                        $chatbotKeywordValueTypes = new ChatbotKeywordValueTypes;
                        $chatbotKeywordValueTypes->fill($valueType);
                        $chatbotKeywordValueTypes->save();
                    }
                }
            }
        }

        $result = json_decode(WatsonManager::pushKeyword($chatbotKeyword->id));

        if (property_exists($result, 'error')) {
            ChatbotKeyword::where('id', $chatbotKeyword->id)->delete();

            return response()->json(['code' => $result->code, 'error' => $result->error]);
        }

        return response()->json(['code' => 200, 'data' => $chatbotKeyword, 'redirect' => route('chatbot.keyword.edit', [$chatbotKeyword->id])]);
    }

    public function destroy(Request $request, $id): RedirectResponse
    {
        if ($id > 0) {
            // $chatbotKeyword = ChatbotKeyword::where("id", $id)->first();
            $chatbotKeyword = ChatbotQuestion::where('id', $id)->first();

            if ($chatbotKeyword) {
                ChatbotKeywordValue::where('chatbot_keyword_id', $id)->delete();
                $chatbotKeyword->delete();
                WatsonManager::deleteKeyword($chatbotKeyword->id);

                return redirect()->back();
            }
        }

        return redirect()->back();
    }

    public function edit(Request $request, $id): View
    {
        // $chatbotKeyword = ChatbotKeyword::where("id", $id)->first();
        $chatbotKeyword = ChatbotQuestion::where('id', $id)->first();

        return view('chatbot::keyword.edit', compact('chatbotKeyword'));
    }

    public function update(Request $request, $id): RedirectResponse
    {
        // dd($request->all());
        $params = $request->all();
        // $params["keyword"]            = str_replace(" ", "_", $params["keyword"]);
        $params['value']              = str_replace(' ', '_', $params['value']);
        $params['chatbot_keyword_id'] = $id;

        // $chatbotKeyword = ChatbotKeyword::where("id", $id)->first();
        $chatbotKeyword = ChatbotQuestion::where('id', $id)->first();

        if ($chatbotKeyword) {
            $chatbotKeyword->fill($params);
            $chatbotKeyword->save();
            if ($params['value_name'] != null) {
                $chatbotKeywordValue                     = new ChatbotKeywordValue;
                $chatbotKeywordValue->value              = $params['value_name'];
                $chatbotKeywordValue->chatbot_keyword_id = $params['chatbot_keyword_id'];
                $chatbotKeywordValue->types              = $params['types'];
                $chatbotKeywordValue->save();

                $valueType                             = [];
                $valueType['chatbot_keyword_value_id'] = $chatbotKeywordValue->id;
                foreach ($params['type'] as $value) {
                    if ($value != null) {
                        $valueType['type']        = $value;
                        $chatbotKeywordValueTypes = new ChatbotKeywordValueTypes;
                        $chatbotKeywordValueTypes->fill($valueType);
                        $chatbotKeywordValueTypes->save();
                    }
                }
            }
            WatsonManager::pushKeyword($chatbotKeyword->id);
        }

        return redirect()->back();
    }

    public function destroyValue(Request $request, $id, $valueId): RedirectResponse
    {
        $cbValue = ChatbotKeywordValue::where('chatbot_keyword_id', $id)->where('id', $valueId)->first();
        if ($cbValue) {
            $cbValue->delete();
            WatsonManager::pushKeyword($id);
        }

        return redirect()->back();
    }

    public function saveAjax(): JsonResponse
    {
        dd('We are not using this page anymore');
        $params            = $request->all();
        $params['keyword'] = str_replace(' ', '_', preg_replace('/\s+/', ' ', $params['keyword']));
        $values            = $request->get('values');

        $validator = Validator::make($params, [
            'keyword' => 'required|unique:chatbot_keywords|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json(['code' => 500, 'error' => []]);
        }

        $chatbotKeyword = ChatbotKeyword::create($params);
        if (! empty($values)) {
            if (is_array($values)) {
                foreach ($values as $value) {
                    ChatbotKeywordValue::create(['chatbot_keyword_id' => $chatbotKeyword->id, 'value' => $value]);
                }
            }
        }

        WatsonManager::pushKeyword($chatbotKeyword->id);

        return response()->json(['code' => 200]);
    }

    public function search(Request $request): JsonResponse
    {
        dd('We are not using this page anymore');
        $keyword    = request('term', '');
        $allKeyword = ChatbotKeyword::where('keyword', 'like', '%' . $keyword . '%')->limit(10)->get();

        $allKeywordList = [];
        if (! $allKeyword->isEmpty()) {
            foreach ($allKeyword as $all) {
                $allKeywordList[] = ['id' => $all->id, 'text' => $all->keyword];
            }
        }

        return response()->json(['incomplete_results' => false, 'items' => $allKeywordList, 'total_count' => count($allKeywordList)]);
    }
}
