<?php

namespace App\Http\Controllers;

use App\ChatbotDialogResponse;
use App\ChatMessage;
use App\Customer;
use App\Http\Requests\StoreMessageRequest;
use App\Leads;
use App\Library\Watson\Model as WatsonManager;
use App\Message;
use App\Order;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\View\View;
use Image;
use Plank\Mediable\Facades\MediaUploader as MediaUploader;
use Plank\Mediable\Media;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class MessageController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        $messages = Message::with(['user'])->get();

        return response()->json($messages);
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
    public function store(StoreMessageRequest $request)
    {

        $data = $request->except('_token');
        $id = $request->get('moduleid');
        $moduletype = $request->get('moduletype');

        if ($moduletype == 'leads') {
            $lead = Leads::find($id);

            if ($lead->customer) {
                $data['customer_id'] = $lead->customer->id;
            }
        } elseif ($moduletype == 'order') {
            $order = Order::find($id);

            if ($order->customer) {
                $data['customer_id'] = $order->customer->id;
            }
        } elseif ($moduletype == 'customer') {
            $customer = Customer::find($id);
            $data['customer_id'] = $customer->id;
        }

        $data['userid'] = Auth::id();
        if ($data['status'] == '4') {
            $data['assigned_to'] = $data['assigned_user'];
        }

        $message = Message::create($data);

        if ($request->hasFile('image')) {
            $media = MediaUploader::fromSource($request->file('image'))
                ->toDirectory('message/'.floor($message->id / config('constants.image_per_folder')))
                ->upload();
            $message->attachMedia($media, config('constants.media_tags'));
        }

        if ($request->images) {
            foreach (json_decode($request->images) as $image) {
                $media = Media::find($image);
                $message->attachMedia($media, config('constants.media_tags'));
            }
        }

        if ($request->screenshot_path != '') {
            $image_path = public_path().'/uploads/temp_screenshot.png';
            $img = substr($request->screenshot_path, strpos($request->screenshot_path, ',') + 1);
            $img = Image::make(base64_decode($img))->encode('png')->save($image_path);

            $media = MediaUploader::fromSource($image_path)
                ->toDirectory('message/'.floor($message->id / config('constants.image_per_folder')))
                ->upload();
            $message->attachMedia($media, config('constants.media_tags'));

            File::delete('uploads/temp_screenshot.png');
        }

        if ($moduletype == 'product') {
            $moduletype = 'purchase/product';
        }

        if ($request->ajax()) {
            return '';
        }

        return redirect()->to('/'.$moduletype.'/'.$id);
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

    public function downloadImages(Request $request): BinaryFileResponse
    {
        $new_match = [];
        preg_match_all('/<img src="(.*?)" class="message-img/', $request->images, $match);

        foreach ($match[1] as $image) {
            $exploded = explode('uploads/', $image);

            array_push($new_match, public_path('uploads/'.$exploded[1]));
        }

        \Zipper::make(public_path('images.zip'))->add($new_match)->close();

        return response()->download(public_path('images.zip'))->deleteFileAfterSend();
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
     */
    public function update(Request $request, int $id): Response
    {
        if ($request->type == 'message') {
            $message = Message::find($id);
            $message->body = $request->get('body');
            $message->save();
        } elseif ($request->type == 'whatsapp') {
            $message = ChatMessage::find($id);

            if ($message->is_chatbot == 1) {
                $oldMessage = $message->message;
                // find the old message into dilog and update the new one
                $dialogResponse = ChatbotDialogResponse::where('value', $oldMessage)->get();
                if (! $dialogResponse->isEmpty()) {
                    foreach ($dialogResponse as $response) {
                        $response->value = $request->get('body');
                        $response->save();
                        WatsonManager::pushDialog($response->chatbot_dialog_id);
                    }
                }
            }

            $message->message = $request->get('body');
            $message->save();
        }

        return response(['message' => 'Success']);
    }

    public function updatestatus(Request $request)
    {
        $message = Message::find($request->get('id'));
        $message->status = $request->get('status');
        $message->save();
    }

    public function loadmore(Request $request): View
    {
        $moduleid = $request->get('moduleid');
        $moduletype = $request->get('moduletype');
        $messageid = $request->get('messageid');
        $messages = Message::all()->where('id', '<', $messageid)->where('moduleid', '=', $moduleid)->where('moduletype', '=', $moduletype)->sortByDesc('created_at')->take(10)->toArray();

        return view('leads.bubbles', compact(['messages', 'moduletype']));
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

    public function removeImage(Request $request, $id): Response
    {
        if ($request->type == 'message') {
            $message = Message::find($id);
        } else {
            $message = ChatMessage::find($id);
        }

        $message->detachMedia($request->image_id, config('constants.media_tags'));

        return response()->noContent(200);
    }
}
