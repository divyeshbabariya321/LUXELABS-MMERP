<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Log;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use App\ApiKey;
use App\Account;
use App\ImQueue;
use App\Setting;
use App\Customer;
use App\MessageQueue;
use App\BroadcastImage;
use Illuminate\Http\Request;
use App\Marketing\WhatsappConfig;
use App\Marketing\MarketingPlatform;
use Illuminate\Pagination\LengthAwarePaginator;
use Plank\Mediable\Facades\MediaUploader as MediaUploader;

class BroadcastMessageController extends Controller
{
    public function index(Request $request): View
    {
        if ($request->reportrange != '') {
            $range = explode(' - ', $request->reportrange);
            if ($range[0] == end($range)) {
                $message_groups = ImQueue::whereDate('created_at', $range[0])->orderByDesc('id')->whereNotNull('broadcast_id')->get()->groupBy('broadcast_id');
            } else {
                $message_groups = ImQueue::whereBetween('created_at', [$range[0], end($range)])->orderByDesc('id')->whereNotNull('broadcast_id')->get()->groupBy('broadcast_id');
            }
        } else {
            $message_groups = ImQueue::whereNotNull('broadcast_id')->orderByDesc('id')->get()->groupBy('broadcast_id');
        }

        $message_groups_array = [];

        $new_data = [];

        foreach ($message_groups as $group_id => $datas) {
            $pending_count  = 0;
            $received_count = 0;
            $stopped_count  = 0;
            $failed_count   = 0;
            $total_count    = 0;
            foreach ($datas as $data) {
                if ($data->sent_at != null && $data->sent_at != '2002-02-02 02:02:02') {
                    $received_count++;
                }

                if ($data->sent_at == '2002-02-02 02:02:02') {
                    $failed_count++;
                }

                $can_be_stopped = true;

                if ($data->send_after == null) {
                    $stopped_count++;
                    $can_be_stopped = false;
                }

                if ($data->sent_at == null) {
                    $pending_count++;
                }

                if ($data->im_client == 'facebook') {
                    $account = Account::where('platform', 'facebook')->where('last_name', $data->number_from)->first();
                    if ($account == null) {
                        $frequency = 0;
                    } else {
                        $frequency = $account->frequency;
                    }
                } elseif ($data->im_client == 'instagram') {
                    $account = Account::where('platform', 'instagram')->where('last_name', $data->number_from)->first();
                    if ($account == null) {
                        $frequency = 0;
                    } else {
                        $frequency = $account->frequency;
                    }
                } else {
                    $whatsappConfig = WhatsappConfig::where('number', $data->number_from)->first();
                    if ($whatsappConfig == null) {
                        $frequency = 0;
                    } else {
                        $frequency = $whatsappConfig->frequency;
                    }
                }

                //Start Date And Time
                $firstMessage = ImQueue::where('broadcast_id', $group_id)->orderBy('send_after')->first();
                if ($firstMessage == null) {
                    $firstMessage->send_after = 0;
                }

                //last Message Date And Time
                $lastMessage = ImQueue::where('broadcast_id', $group_id)->orderByDesc('send_after')->first();
                if ($lastMessage == null) {
                    $lastMessage->send_after = 0;
                }

                $message_groups_array['start_time']       = $firstMessage->send_after;
                $message_groups_array['end_time']         = $lastMessage->send_after;
                $message_groups_array['message']          = $data->text;
                $message_groups_array['broadcast_number'] = $data->number_from;
                $message_groups_array['frequency']        = $frequency;
                $message_groups_array['image']            = $data->image;
                $message_groups_array['can_be_stopped']   = $can_be_stopped;
                $message_groups_array['sending_time']     = $data->send_after;
                $message_groups_array['whatsapp_number']  = $data->number_from;
                $total_count++;
            }

            $message_groups_array['pending']        = $pending_count;
            $message_groups_array['received']       = $received_count;
            $message_groups_array['stopped']        = $stopped_count;
            $message_groups_array['failed']         = $failed_count;
            $message_groups_array['total']          = $total_count;
            $message_groups_array['expecting_time'] = '';
            $message_groups_array['group_id']       = $group_id;
            $message_groups['datas']                = $message_groups_array;

            $new_data[] = $message_groups_array;
        }

        $currentPage = LengthAwarePaginator::resolveCurrentPage();
        $perPage     = Setting::get('pagination');
        if (request()->get('select_all') == 'true') {
            $perPage     = count($vendors);
            $currentPage = 1;
        }
        $currentItems = array_slice($new_data, $perPage * ($currentPage - 1), $perPage);

        $new_data = new LengthAwarePaginator($currentItems, count($new_data), $perPage, $currentPage, [
            'path' => LengthAwarePaginator::resolveCurrentPath(),
        ]);

        // Get all numbers from config
        $configWhatsApp = WhatsappConfig::select('id', 'number')->where('status', 1)->get();

        $platforms = MarketingPlatform::all();

        return view('customers.broadcast', [
            'broadcasts' => $new_data,
            'platforms'  => $platforms,

        ]);
    }

    public function doNotDisturb(Request $request, $id): RedirectResponse
    {
        $customer                 = Customer::find($id);
        $customer->do_not_disturb = 1;
        Log::channel('customerDnd')->debug('(Customer ID ' . $customer->id . ' line ' . $customer->name . ' ' . $customer->number . ': Added To DND');

        $customer->save();

        MessageQueue::where('sent', 0)->where('customer_id', $id)->delete();

        return redirect()->route('broadcast.index')->with('success', 'You have successfully changed status!');
    }

    public function images(): View
    {
        $broadcast_images = BroadcastImage::orderByDesc('id')->paginate(Setting::get('pagination'));
        $api_keys         = ApiKey::select('number')->get();

        $platforms = MarketingPlatform::all();

        $mediaTags =  config('constants.media_tags'); // Use config variable
        return view('customers.broadcast-images', [
            'broadcast_images' => $broadcast_images,
            'api_keys'         => $api_keys,
            'platforms'        => $platforms,
            'mediaTags'        => $mediaTags,
        ]);
    }

    public function imagesUpload(Request $request): RedirectResponse
    {
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $broadcast_image = BroadcastImage::create([
                    'sending_time' => $request->sending_time,
                ]);

                $media = MediaUploader::fromSource($image)->toDirectory('broadcast-images')->upload();
                $broadcast_image->attachMedia($media, config('constants.media_tags'));
            }
        }

        return redirect()->route('broadcast.index')->withSuccess('You have successfully uploaded images!');
    }

    public function imagesLink(Request $request): RedirectResponse
    {
        $image           = BroadcastImage::find($request->moduleid);
        $image->products = $request->products;
        $image->save();

        return redirect()->route('broadcast.images')->withSuccess('You have successfully linked products!');
    }

    public function imagesDelete($id): RedirectResponse
    {
        $image = BroadcastImage::find($id);

        $path = $image->hasMedia(config('constants.media_tags')) ? $image->getMedia(config('constants.media_tags'))->first()->getAbsolutePath() : '';

        File::delete($path);

        $image->delete();

        return redirect()->route('broadcast.images')->withSuccess('You have successfully deleted images!');
    }

    public function calendar(): View
    {
        $message_queues    = MessageQueue::latest()->get()->groupBy('group_id');
        $filtered_messages = [];

        foreach ($message_queues as $group_id => $message_queue) {
            $filtered_messages[$group_id] = $message_queue[0];
        }

        return view('customers.broadcast-calendar', [
            'message_queues' => $filtered_messages,
        ]);
    }

    public function restart(Request $request): RedirectResponse
    {
        $last_group_id = MessageQueue::max('group_id');

        $last_set_stopped = MessageQueue::where('group_id', $last_group_id)->where('status', 1)->where('sent', 0)->get();

        foreach ($last_set_stopped as $set) {
            $set->status = 0;
            $set->save();
        }

        return redirect()->route('broadcast.index')->withSuccess('You have successfully restarted last set!');
    }

    public function restartGroup(Request $request, $id): RedirectResponse
    {
        $groups = ImQueue::where('broadcast_id', $id)->get();

        foreach ($groups as $group) {
            $whatappConfig = WhatsappConfig::find($request->whatsapp_number);

            $maxTime = ImQueue::select(DB::raw('IF(MAX(send_after)>MAX(sent_at), MAX(send_after), MAX(sent_at)) AS maxTime'))->where('number_from', $whatappConfig->number)->first();

            // Convert maxTime to unixtime
            $maxTime = strtotime($maxTime->maxTime);

            // Add interval
            $maxTime = $maxTime + (3600 / $whatappConfig->frequency);

            // Check if it's in the future
            if ($maxTime < time()) {
                $maxTime = time();
            }

            // Check for decent times
            if (date('H', $maxTime) < $whatappConfig->send_start) {
                $sendAfter = date('Y-m-d 0' . $whatappConfig->send_start . ':00:00', $maxTime);
            } elseif (date('H', $maxTime) > $whatappConfig->send_end) {
                $sendAfter = date('Y-m-d 0' . $whatappConfig->send_start . ':00:00', $maxTime + 86400);
            } else {
                $sendAfter = date('Y-m-d H:i:s', $maxTime);
            }

            $group->send_after = $sendAfter;

            $group->update();
        }

        return redirect()->route('broadcast.index')->withSuccess('You have successfully restarted group!');
    }

    public function DeleteGroup(Request $request, $id): RedirectResponse
    {
        ImQueue::where('broadcast_id', $id)->delete();

        return redirect()->route('broadcast.index')->withSuccess('You have successfully deleted group!');
    }

    public function stopGroup(Request $request, $id): RedirectResponse
    {
        $messageQueues = ImQueue::where('broadcast_id', $id)->whereNull('sent_at')->get();
        foreach ($messageQueues as $messageQueue) {
            $messageQueue->send_after = null;
            $messageQueue->update();
        }

        return redirect()->route('broadcast.index')->with('success', 'Broadcast group has been stopped!');
    }
}
