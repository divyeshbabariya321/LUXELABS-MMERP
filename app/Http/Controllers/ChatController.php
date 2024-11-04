<?php

namespace App\Http\Controllers;

use App\Chat;
use App\Http\Requests\AddmessagesChatRequest;
use App\Http\Requests\StoreChatRequest;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Pusher\Laravel\Facades\Pusher;

class ChatController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $userid = $request->input('userid');
        $chat = new Chat;
        $chats = $chat->where('sourceid', '=', Auth::id())->orWhere('userid', '=', Auth::id())->get()->toArray();

        $chat_converstaion = [];

        $chat_converstaion[] = '<table>';
        foreach ($chats as $message) {
            $msg = htmlentities($message['messages'], ENT_NOQUOTES);
            $users = User::find($message['sourceid']);
            if (Auth::id() == $message['sourceid']) {
                $style = 'selfs';
            } else {
                $style = 'noselfs';
            }
            $user_name = $users['name'];
            $sent = date('F j, Y, g:i a', strtotime($message['created_at']));
            if ((Auth::id() == $message['sourceid'] && $userid == $message['userid']) || ($message['sourceid'] == $userid && $message['userid'] == Auth::id())) {
                $chat_converstaion[] = '
                  <tr class="msg-row-container '.$style.'" >
                    <td>
                      <div class="msg-row">
                        <div class="avatar"><img src="https://ui-avatars.com/api/?name='.$user_name.'" width="32px"/></div>
                        <div class="message">
                          <span class="user-label"><a href="#" style="color: #6D84B4;">'.$user_name.'</a> <span class="msg-time">'.$sent.'</span></span><br/>'.$msg.'
                        </div>
                      </div>
                    </td>
                  </tr>';
            }
        }
        $chat_converstaion[] = '</table>';
        echo implode('', $chat_converstaion);
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
    public function store(StoreChatRequest $request)
    {
        //
        $request->merge(['sourceid' => Auth::id()]);
        $chat = $request->validated();
        $messages = $request->input('messages');
        $userid = $request->input('userid');
        $sourceid = $request->input('sourceid');
        Chat::create($chat);
        Pusher::trigger('solo-chat-channel', 'chat', ['message' => $messages, 'userid' => $userid, 'sourceid' => $sourceid]);

        return 1;
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
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy(int $id)
    {
        //
    }

    // this is custom controller to add messages.
    public function addmessages(AddmessagesChatRequest $request)
    {
        $request->merge(['userid' => Auth::id()]);
        $chat = $request->validated();
    }

    public function checkfornew(Request $request)
    {
        $users = new User;
        $loggedinuser = $users->find(Auth::id());
        $lastcheck = $loggedinuser['last_checked'];
        $allusers = $users->all();
        $newmessage = [];
        $chat = new Chat;
        foreach ($allusers as $user) {
            $chats = '';

            $userid = $user['id'];
            $chats = $chat->where('sourceid', '=', $userid)->where('userid', '=', Auth::id())->where('created_at', '>', $lastcheck)->get()->toArray();
            if (! empty($chats)) {
                $newmessage[] = ['userid' => $userid, 'new' => 'true'];
            } else {
                $newmessage[] = ['userid' => $userid, 'new' => 'false'];
            }
        }

        echo json_encode($newmessage);
    }

    public function updatefornew(Request $request)
    {
        $user = Auth::User();
        $user->last_checked = date('Y-m-d H:i:s');
        $user->save();
    }
}
