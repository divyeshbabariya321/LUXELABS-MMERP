<?php

namespace App\Http\Controllers\ChatGPT;

use App\ChatGptResponses;
use App\Http\Controllers\Controller;
use App\Setting;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Validator;
use Illuminate\View\View;
use Exception;

class ChatGPTController extends Controller
{
    public function index(Request $request): View
    {
        $responses = ChatGptResponses::where(function ($query) use ($request) {
            if ($request->has('prompt') && $request->prompt) {
                $query->where('prompt', 'like', '%'.$request->prompt.'%');
            }
            if ($request->has('response') && $request->response) {
                $query->where('response', 'like', '%'.$request->response.'%');
            }
        })->orderByDesc('id')->paginate(Setting::get('pagination'), ['*'], 'chat_gpt_responses');

        return view('chat-gpt.index', compact('responses'));
    }

    public function requestApi(Request $request)
    {
        try {
            $modelsList = $this->modelsList();

            return view('chat-gpt.request', compact('modelsList'));
        } catch (Exception $e) {
            return Redirect::route('chatgpt.index')->with('error', $e->getMessage());
        }
    }

    public function modelsList($request = null)
    {
        if (! empty($request)) {
            $regenerate = filter_var($request->get('regenerate'), FILTER_VALIDATE_BOOLEAN);
            if ($regenerate == false) {
                $modelResponses = ChatGptResponses::where('prompt', 'Models List')->first();
                if ($modelResponses) {
                    return response()->json(['status' => true, 'message' => 'Response found', 'data' => ['response' => unserialize($modelResponses->response), 'getting_type' => 'database']]);
                }
            }
        } else {
            $modelResponses = ChatGptResponses::where('prompt', 'Models List')->first();
            if ($modelResponses) {
                return unserialize($modelResponses->response);
            } else {
                return [];
            }
        }

        $chatGpt = new ChatGPTService();
        $modelResponses = $chatGpt->getModels();
        if (! $modelResponses['status']) {
            return Redirect::route('chatgpt.index')->with('error', $modelResponses['message']);
        }
        $modelsList = [];
        foreach ($modelResponses['data']['data'] as $list) {
            $modelsList[] = $list['id'];
        }
        $insertData = [
            'prompt' => 'Models List',
            'response' => serialize($modelsList),
            'response_data' => serialize($modelResponses['data']),
        ];
        ChatGptResponses::create($insertData);

        return response()->json(['status' => true, 'message' => 'Response found', 'data' => ['response' => $modelsList, 'getting_type' => 'chatGPT']]);
    }

    public function getResponse(Request $request)
    {
        if ($request->type === 'models') {
            return $this->modelsList($request);
        }
        if ($request->type === 'completions') {
            return $this->getCompletions($request);
        }
        if ($request->type === 'edits') {
            return $this->getEdits($request);
        }
        if ($request->type === 'image_generate') {
            return $this->getImageGenerate($request);
        }
        if ($request->type === 'image_edit') {
            return $this->getEditImage($request);
        }
        if ($request->type === 'image_variation') {
            return $this->getImageVariation($request);
        }
        if ($request->type === 'moderations') {
            return $this->getModerations($request);
        }

        return response()->json(['status' => false, 'message' => 'Invalid Input']);
    }

    /**
     * Get completion from chat GPT.
     */
    public function getCompletions(Request $request): JsonResponse
    {
        try {
            $regenerate = filter_var($request->get('regenerate'), FILTER_VALIDATE_BOOLEAN);
            $chatGpt = new ChatGPTService();
            if ($regenerate == false) {
                $prompt = ChatGptResponses::where('prompt', 'like', '%'.$request->get('prompt').'%')->first();
                if ($prompt) {
                    $res = $chatGpt->dataUnserialize($prompt->response);

                    return response()->json(['status' => true, 'message' => 'Response found', 'data' => ['response' => $res, 'getting_type' => 'database']]);
                }
            }

            $response = $chatGpt->getCompletions($request->get('prompt'), (float) $request->get('temperature'), $request->get('max_tokens'), $request->get('n'), $request->get('completions_model') ? $request->get('completions_model') : 'text-davinci-003');
            if (! $response['status']) {
                return response()->json(['status' => false, 'message' => $response['message']]);
            } else {
                $choices = [];
                foreach ($response['data']['choices'] as $key => $value) {
                    $choices['Response:'.$key + 1] = $value['text'];
                }
                $insertData = [
                    'prompt' => $request->get('prompt'),
                    'response' => serialize($choices),
                    'response_data' => serialize($response['data']),
                ];
                ChatGptResponses::create($insertData);

                return response()->json(['status' => true, 'message' => 'Response found', 'data' => ['response' => $choices, 'getting_type' => 'chatGPT']]);
            }
        } catch (Exception $e) {
            return response()->json(['status' => false, 'message' => $e->getMessage()]);
        }
    }

    /**
     * Get edits from chat GPT.
     */
    public function getEdits(Request $request): JsonResponse
    {
        try {
            $regenerate = filter_var($request->get('regenerate'), FILTER_VALIDATE_BOOLEAN);
            $chatGpt = new ChatGPTService();
            if ($regenerate == false) {
                $prompt = ChatGptResponses::where('prompt', 'like', '%'.$request->get('input').'%')->first();
                if ($prompt) {
                    $res = $chatGpt->dataUnserialize($prompt->response);

                    return response()->json(['status' => true, 'message' => 'Response found', 'data' => ['response' => $res, 'getting_type' => 'database']]);
                }
            }

            $response = $chatGpt->performEdit($request->get('input'), $request->get('instruction'), (int) $request->get('n'), (float) $request->get('temperature'), $request->get('model') ? $request->get('model') : 'text-davinci-edit-001');
            if (! $response['status']) {
                return response()->json(['status' => false, 'message' => $response['message']]);
            } else {
                $choices = [];
                foreach ($response['data']['choices'] as $key => $value) {
                    $choices['Response:'.$key + 1] = $value['text'];
                }
                $insertData = [
                    'prompt' => $request->get('input'),
                    'response' => serialize($choices),
                    'response_data' => serialize($response['data']),
                ];
                ChatGptResponses::create($insertData);

                return response()->json(['status' => true, 'message' => 'Response found', 'data' => ['response' => $choices, 'getting_type' => 'chatGPT']]);
            }
        } catch (Exception $e) {
            return response()->json(['status' => false, 'message' => $e->getMessage()]);
        }
    }

    /**
     * Generate images from input
     */
    public function getImageGenerate(Request $request): JsonResponse
    {
        try {
            $regenerate = filter_var($request->get('regenerate'), FILTER_VALIDATE_BOOLEAN);
            $chatGpt = new ChatGPTService();
            if ($regenerate == false) {
                $prompt = ChatGptResponses::where('prompt', 'like', '%'.$request->get('prompt').'%')->first();
                if ($prompt) {
                    $response = $chatGpt->dataUnserialize($prompt->response);

                    return response()->json(['status' => true, 'message' => 'Response found', 'data' => ['response' => $response, 'getting_type' => 'database', 'type' => 'image_generate']]);
                }
            }

            $response = $chatGpt->generateImage($request->get('prompt'), $request->get('n'), $request->get('size'));
            if (! $response['status']) {
                return response()->json(['status' => false, 'message' => $response['message']]);
            } else {
                $url = [];
                foreach ($response['data']['data'] as $key => $value) {
                    $url['Response:'.$key + 1] = $value['url'];
                }
                $insertData = [
                    'prompt' => $request->get('prompt'),
                    'response' => serialize($url),
                    'response_data' => serialize($response['data']),
                ];
                ChatGptResponses::create($insertData);

                return response()->json(['status' => true, 'message' => 'Response found', 'data' => ['response' => $url, 'getting_type' => 'chatGPT', 'type' => 'image_generate']]);
            }
        } catch (Exception $e) {
            return response()->json(['status' => false, 'message' => $e->getMessage()]);
        }
    }

    /**
     * Edit image and generate response
     */
    public function getEditImage(Request $request): JsonResponse
    {
        try {
            $regenerate = filter_var($request->get('regenerate'), FILTER_VALIDATE_BOOLEAN);
            $image = $_FILES['image'];
            $mask = $_FILES['mask'];
            $chatGpt = new ChatGPTService();
            if ($regenerate == false) {
                $prompt = ChatGptResponses::where('prompt', 'like', '%'.$request->get('prompt').'%')->first();
                if ($prompt) {
                    $res = $chatGpt->dataUnserialize($prompt->response);

                    return response()->json(['status' => true, 'message' => 'Response found', 'data' => ['response' => $res, 'getting_type' => 'database', 'type' => 'image_edit']]);
                }
            }

            $response = $chatGpt->editGeneratedImage($image, $mask, $request->get('prompt'), $request->get('n'), $request->get('size'));

            if (! $response['status']) {
                return response()->json(['status' => false, 'message' => $response['message']]);
            } else {
                $url = [];
                foreach ($response['data']['data'] as $key => $value) {
                    $url['Response:'.$key + 1] = $value['url'];
                }
                $insertData = [
                    'prompt' => $request->get('prompt'),
                    'response' => serialize($url),
                    'response_data' => serialize($response['data']),
                ];

                ChatGptResponses::create($insertData);

                return response()->json(['status' => true, 'message' => 'Response found', 'data' => ['response' => $url, 'getting_type' => 'chatGPT', 'type' => 'image_generate']]);
            }
        } catch (Exception $e) {
            return response()->json(['status' => false, 'message' => $e->getMessage()]);
        }
    }

    /**
     * Get image variations
     */
    public function getImageVariation(Request $request): JsonResponse
    {
        try {
            $image = $_FILES['image'];
            $chatGpt = new ChatGPTService();
            $response = $chatGpt->generateImageVariation($image, $request->get('n'), $request->get('size'));
            if (! $response['status']) {
                return response()->json(['status' => false, 'message' => $response['message']]);
            } else {
                $url = [];
                foreach ($response['data']['data'] as $key => $value) {
                    $url['Response:'.$key + 1] = $value['url'];
                }

                return response()->json(['status' => true, 'message' => 'Response found', 'data' => ['response' => $url, 'getting_type' => 'chatGPT', 'type' => 'image_variation']]);
            }
        } catch (Exception $e) {
            return response()->json(['status' => false, 'message' => $e->getMessage()]);
        }
    }

    /**
     * Get moderations
     */
    public function getModerations(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'model' => 'required',
                'input' => 'required',
            ]);
            if ($validator->fails()) {
                return response()->json(['status' => false, 'message' => $validator->errors()->first()]);
            }
            $prompt = ChatGptResponses::where('prompt', 'like', '%'.$request->get('input').'%')->first();
            if ($prompt && $request->get('')) {
                if ($prompt) {
                    return response()->json(['status' => true, 'message' => 'Response found', 'data' => ['response' => $prompt->response, 'getting_type' => 'database', 'type' => 'moderations']]);
                }
            }
            $chatGpt = new ChatGPTService();
            $response = $chatGpt->identifyModeration($request->get('input'), $request->get('model') ? $request->get('model') : 'text-moderation-stable');
            if (! $response['status']) {
                return response()->json(['status' => false, 'message' => $response['message']]);
            } else {
                $insertData = [
                    'prompt' => $request->get('input'),
                    'response' => serialize($response['data']['results'][0]),
                    'response_data' => serialize($response['data']),
                ];
                ChatGptResponses::create($insertData);

                return response()->json(['status' => true, 'message' => 'Response found', 'data' => ['response' => $response['data']['results'][0], 'getting_type' => 'chatGPT', 'type' => 'moderations']]);
            }
        } catch (Exception $e) {
            return response()->json(['status' => false, 'message' => $e->getMessage()]);
        }
    }
}
