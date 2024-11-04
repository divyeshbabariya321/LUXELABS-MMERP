<?php

namespace App\Services;

use App\Models\Faq;
use Illuminate\Support\Facades\Http;
use Exception;

class ChatbotService
{
    protected $apiKey;

    protected $questionAnswerPairs;

    protected $prompt;

    protected $url;

    protected $faqData;
    
    protected $storeWebsite;

    public $fallbackMessage;

    const STATUS = [true, false];

    public function __construct($storeWebsite)
    {
        $this->storeWebsite = $storeWebsite;

        $geminiAiAccount = $this->storeWebsite->geminiAiAccount;
        $this->apiKey = $geminiAiAccount->api_key;
        $this->fallbackMessage = $geminiAiAccount->fallback_message;
        $this->url = $geminiAiAccount->api_url."?key={$this->apiKey}";
        $this->prompt = $geminiAiAccount->prompt.' Your knowledge is based solely on the following FAQ data:';

        $this->prompt = $geminiAiAccount->prompt.'If the customer query is too vague or not covered in the FAQ, use the fallback response: "'.$geminiAiAccount->fallback_message.'" Your knowledge is based solely on the following FAQ data:';
        $this->faqData = Faq::where('store_website_id', $this->storeWebsite->id)->select(['question as input', 'answer as output'])->get()->toArray();

        foreach ($this->faqData as $faq) {
            $this->prompt .= 'Q: '.$faq['input']."\nA: ".$faq['output']."\n\n";
        }
    }

    public function sendMessageToAI($customerQuery)
    {
        try {
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
            ])->post($this->url, [
                'contents' => [
                    [
                        'role' => 'user',
                        'parts' => [
                            [
                                'text' => $customerQuery,
                            ],
                        ],
                    ],
                ],
                'systemInstruction' => [
                    'role' => 'user',
                    'parts' => [
                        [
                            'text' => $this->prompt,
                        ],
                    ],
                ],
                'generationConfig' => [
                    'temperature' => 1,
                    'topK' => 64,
                    'topP' => 0.95,
                    'maxOutputTokens' => 8192,
                    'responseMimeType' => 'text/plain',
                ],
            ]);

            if ($response->successful()) {
                $content = $response->json();
                $aiResponse = $content['candidates'][0]['content']['parts'][0]['text'];

                return response()->json([
                    'status' => true,
                    'message' => $aiResponse,
                ], $response->status());
            } else {
                return response()->json([
                    'status' => false,
                    'message' => 'Something went wrong, please try again after some time',
                ], $response->status());
            }
        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function getAutomateReply($customerQuery)
    {
        $answer = Faq::where('question', 'like', '%'.$customerQuery.'%')
            ->where('store_website_id', $this->storeWebsite->id)
            ->pluck('answer');

        if ($answer->isNotEmpty()) {
            return response()->json([
                'status' => true,
                'message' => $answer[0],
            ], 200);
        } else {
            $response = $this->sendMessageToAI($customerQuery);
            $replyData = json_decode($response->getContent(), true);

            if ($replyData['status']) {
                Faq::create([
                    'question' => $customerQuery,
                    'answer' => $replyData['message'],
                    'store_website_id' => $this->storeWebsite->id,
                ]);
            }

            return $response;
        }
    }
}
