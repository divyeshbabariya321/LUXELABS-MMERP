<?php

namespace App\Http\Controllers;
use App\Library\Watson\Model;
use App\Customer;

use App\Library\Watson\Language\Workspaces\V1\IntentService;
use App\Library\Watson\Model as WatsonManager;
use Illuminate\Http\Request;

class ChatBotController extends Controller
{
    public function connection(Request $request)
    {
        $customer = Customer::find(2001);
        $watsonManager = WatsonManager::sendMessage($customer, 'weather');

        print_r($watsonManager);
        exit;

        exit;

        $chatSesssion = '60421530-8204-4dad-a342-662b95ab74e7'; //$request->session()->get("chat_session");

        $watson = new IntentService(
            env('WATSON_API_KEY'),
            env('WATSON_API_PASSWORD')
        );
        $result = $watson->create('19cf3225-f007-4332-8013-74443d36a3f7', [
            'intent' => 'hello',
            'examples' => [
                ['text' => 'Good morning'],
                ['text' => 'hi there'],
                ['text' => 'howdy ?'],
            ],
        ]);

        $result = json_decode($result->getContent());

        print_r($result);
        exit;

        /** create chat and session service
         * if(empty($chatSesssion)) {
         *
         * $session = $watson->createSession("28754e1c-6281-42e6-82af-eec6e87618a6");
         * $result = json_decode($session->getContent());
         * if(isset($result->session_id)) {
         * session()->put('chat_session', $result->session_id);
         * $chatSesssion = $result->session_id;
         * }
         * }
         *
         * $chatMessage = $watson->sendMessage("28754e1c-6281-42e6-82af-eec6e87618a6",$chatSesssion, [
         * "input" => [
         * "text" => "gucci"
         * ]
         * ]);
         * $result = json_decode($chatMessage->getContent());
         *
         * print_r([$chatSesssion,$result]); exit;
         **/
    }
}
