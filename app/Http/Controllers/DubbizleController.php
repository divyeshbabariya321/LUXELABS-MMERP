<?php

namespace App\Http\Controllers;
use App\Http\Controllers\WhatsAppController;

use App\Http\Requests\BulkWhatsappDubbizleRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use App\User;
use App\Helpers;
use App\Dubbizle;
use Carbon\Carbon;
use App\ChatMessage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DubbizleController extends Controller
{
    public function updateReminder(Request $request): JsonResponse
    {
        $supplier                   = Dubbizle::find($request->get('dubbizle_id'));
        $supplier->frequency        = $request->get('frequency');
        $supplier->reminder_message = $request->get('message');
        $supplier->save();

        return response()->json([
            'success',
        ]);
    }

    public function index(): View
    {
        // $posts = DB::select('
        //             SELECT *,
     	// 						 (SELECT mm3.id FROM chat_messages mm3 WHERE mm3.id = message_id) AS message_id,
        //             (SELECT mm1.message FROM chat_messages mm1 WHERE mm1.id = message_id) as message,
        //             (SELECT mm2.status FROM chat_messages mm2 WHERE mm2.id = message_id) AS message_status,
        //             (SELECT mm4.sent FROM chat_messages mm4 WHERE mm4.id = message_id) AS message_type,
        //             (SELECT mm2.created_at FROM chat_messages mm2 WHERE mm2.id = message_id) as last_communicated_at

        //             FROM (
        //               SELECT * FROM dubbizles

        //               LEFT JOIN (SELECT MAX(id) as message_id, dubbizle_id, message, MAX(created_at) as message_created_At FROM chat_messages WHERE chat_messages.status != 7 AND chat_messages.status != 8 AND chat_messages.status != 9 GROUP BY dubbizle_id ORDER BY chat_messages.created_at DESC) AS chat_messages
        //               ON dubbizles.id = chat_messages.dubbizle_id

        //             ) AS dubbizles
        //             WHERE id IS NOT NULL
        //             ORDER BY last_communicated_at DESC;
     	// 					');


        $posts = Dubbizle::select(
            'dubbizles.*',
            DB::raw('(SELECT mm3.id FROM chat_messages mm3 WHERE mm3.id = message_id) AS message_id'),
            DB::raw('(SELECT mm1.message FROM chat_messages mm1 WHERE mm1.id = message_id) as message'),
            DB::raw('(SELECT mm2.status FROM chat_messages mm2 WHERE mm2.id = message_id) AS message_status'),
            DB::raw('(SELECT mm4.sent FROM chat_messages mm4 WHERE mm4.id = message_id) AS message_type'),
            DB::raw('(SELECT mm2.created_at FROM chat_messages mm2 WHERE mm2.id = message_id) as last_communicated_at')
        )
            ->leftJoin(DB::raw('(SELECT MAX(id) as message_id, dubbizle_id, message, MAX(created_at) as message_created_At FROM chat_messages WHERE chat_messages.status != 7 AND chat_messages.status != 8 AND chat_messages.status != 9 GROUP BY dubbizle_id ORDER BY chat_messages.created_at DESC) AS chat_messages'), 'dubbizles.id', '=', 'chat_messages.dubbizle_id')
            ->whereNotNull('dubbizles.id')
            ->orderByDesc('last_communicated_at')
            ->get();

        $keywords = Dubbizle::select('keywords')->get()->groupBy('keywords');

        return view('dubbizle', [
            'posts'    => $posts,
            'keywords' => $keywords,
        ]);
    }

    public function show($id): View
    {
        $dubbizle    = Dubbizle::find($id);
        $users_array = Helpers::getUserArray(User::all());

        return view('dubbizle-show', [
            'dubbizle'    => $dubbizle,
            'users_array' => $users_array,
        ]);
    }

    public function bulkWhatsapp(BulkWhatsappDubbizleRequest $request): RedirectResponse
    {

        $params = [
            'user_id'  => Auth::id(),
            'number'   => null,
            'message'  => $request->message,
            'approved' => 0,
            'status'   => 1,
        ];

        $dubbizles = Dubbizle::where('keywords', $request->group)->get();

        foreach ($dubbizles as $dubbizle) {
            $params['dubbizle_id'] = $dubbizle->id;

            $chat_message = ChatMessage::create($params);

            app(WhatsAppController::class)->sendWithNewApi($dubbizle->phone_number, '919152731483', $params['message'], null, $chat_message->id);

            $chat_message->update([
                'approved'   => 1,
                'status'     => 2,
                'created_at' => Carbon::now(),
            ]);
        }

        return redirect()->to('/scrap/dubbizle')->withSuccess('You have successfully sent bulk whatsapp messages');
    }

    public function edit($id): View
    {
        $d = Dubbizle::findOrFail($id);

        return view('dubbizle-edit', compact('d'));
    }

    public function update($id, Request $request): RedirectResponse
    {
        $d = Dubbizle::findOrFail($id);

        $d->url          = $request->get('url');
        $d->phone_number = $request->get('phone_number');
        $d->keywords     = $request->get('keywords');
        $d->post_date    = $request->get('post_date');
        $d->requirements = $request->get('requirements');
        $d->body         = $request->get('body');
        $d->phone_number = $request->get('phone_number');
        $d->save();

        return redirect()->back()->with('success', 'Record updated successfully!');
    }
}
