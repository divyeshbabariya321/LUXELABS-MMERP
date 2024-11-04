<?php

namespace App\Http\Controllers\Slack;

use App\Http\Controllers\Controller;
use App\SlackChannel;
use Exception;
use GuzzleHttp\Client;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SlackChannelController extends Controller
{
    public function index()
    {
        $query = SlackChannel::query();

        if (request('term')) {
            $query->where('channel_name', 'LIKE', '%'.request('term').'%');
            $query->orwhere('channel_id', 'LIKE', '%'.request('term').'%');
            $query->orwhere('description', 'LIKE', '%'.request('term').'%');
            $query->orwhere('status', 'LIKE', '%'.request('term').'%');
        }

        $channelList = $query->orderByDesc('id')->paginate(10);

        return view('slack-channel-page.index', compact('channelList'));
    }

    public function store(Request $request): RedirectResponse
    {
        $rules = [
            'channel_name' => 'required|string|max:200',
            'description' => 'nullable|string|max:300',
            'channel_status' => 'required|string|max:20',
        ];

        $validation = validator($request->all(), $rules);

        if ($validation->fails()) {
            //withInput keep the users info
            return redirect()->back()->withErrors($validation)->withInput();
        } else {
            $sanitizedChannelName = strtolower(preg_replace('/[^a-z0-9_-]+/', '', str_replace(' ', '-', $request->channel_name)));

            if ($request->channel_edit_id > 0) {
                $NewSlackChannel = SlackChannel::find($request->channel_edit_id);
                if ($NewSlackChannel) {
                    $resChannelCreate = $this->updateChannelNameInSlack($NewSlackChannel->channel_id, $sanitizedChannelName);
                    if ($resChannelCreate && $resChannelCreate['status_code'] == 200) {
                        $NewSlackChannel->channel_name = $sanitizedChannelName;
                        $NewSlackChannel->description = $request->description;
                        $NewSlackChannel->status = $request->channel_status;
                        $NewSlackChannel->update_by = Auth::user()->id;
                        $NewSlackChannel->update_ip = $request->ip();
                        $NewSlackChannel->save();
                        if ($NewSlackChannel) {
                            return redirect()->back()->with('success', 'Slack channel updated successfully.');
                        } else {
                            return redirect()->back()->with('error', 'Oops! Something went wrong, Please try again.');
                        }
                    } else {
                        return redirect()->back()->with('error', 'Oops! '.$resChannelCreate['message'].', Please try again.');
                    }
                }
            } else {
                $resChannelCreate = $this->createChannelInSlack($sanitizedChannelName);
                if ($resChannelCreate && $resChannelCreate['status_code'] == 200) {
                    $NewSlackChannel = new SlackChannel;
                    $NewSlackChannel->channel_id = $resChannelCreate['id'];
                    $NewSlackChannel->channel_name = $sanitizedChannelName;
                    $NewSlackChannel->description = $request->description;
                    $NewSlackChannel->status = $request->channel_status;
                    $NewSlackChannel->entry_by = Auth::user()->id;
                    $NewSlackChannel->entry_ip = $request->ip();
                    $NewSlackChannel->update_by = Auth::user()->id;
                    $NewSlackChannel->update_ip = $request->ip();
                    $NewSlackChannel->save();
                    if ($NewSlackChannel) {
                        return redirect()->back()->with('success', 'Slack channel created successfully.');
                    } else {
                        return redirect()->back()->with('error', 'Oops! Something went wrong, Please try again.');
                    }
                } else {
                    return redirect()
                        ->back()
                        ->with('error', 'Oops! '.$resChannelCreate['message'].', Please try again.');
                }
            }
        }
    }

    public function createChannelInSlack(string $channelName)
    {
        try {
            $client = new Client;
            $response = $client->post('https://slack.com/api/conversations.create', [
                'headers' => [
                    'Authorization' => 'Bearer '.env('SLACK_BOT_TOKEN'),
                    'Content-Type' => 'application/json; charset=utf-8',
                ],
                'json' => [
                    'name' => $channelName,
                ],
            ]);

            $responseBody = json_decode($response->getBody(), true);

            if ($responseBody['ok']) {
                $response = $responseBody['channel'];
                $response['message'] = 'channel created successfully';
                $response['status_code'] = 200;

                return $response;
            } else {
                $response = [];
                $response['message'] = $responseBody['error'];
                $response['status_code'] = 400;

                return $response;
            }
        } catch (Exception $e) {
            $response = [];
            $response['message'] = $e->getMessage();
            $response['status_code'] = 402;

            return $response;
        }
    }

    public function updateChannelNameInSlack(string $channelId, string $newChannelName)
    {
        try {
            $client = new Client;
            $response = $client->post('https://slack.com/api/conversations.rename', [
                'headers' => [
                    'Authorization' => 'Bearer '.env('SLACK_BOT_TOKEN'),
                    'Content-Type' => 'application/json; charset=utf-8',
                ],
                'json' => [
                    'channel' => $channelId, // Existing channel ID
                    'name' => $newChannelName, // New channel name
                ],
            ]);

            $responseBody = json_decode($response->getBody(), true);

            if ($responseBody['ok']) {
                $response = $responseBody['channel'];
                $response['message'] = 'Channel renamed successfully';
                $response['status_code'] = 200;

                return $response;
            } else {
                $response = [];
                $response['message'] = $responseBody['error'];
                $response['status_code'] = 400;

                return $response;
            }
        } catch (Exception $e) {
            $response = [];
            $response['message'] = $e->getMessage();
            $response['status_code'] = 400;

            return $response;
        }
    }

    public function edit(Request $request): JsonResponse
    {
        $data = SlackChannel::where('id', $request->id)->first();
        if ($data) {
            return response()->json([
                'code' => 200,
                'object' => $data,
            ]);
        }

        return response()->json([
            'code' => 500,
            'object' => null,
        ]);
    }

    public function changeStatus(Request $request): JsonResponse
    {
        $channel = SlackChannel::where('id', $request->channel_id)->first();
        $channel->status = $request->status;
        $channel->update();

        return response()->json(['code' => 500, 'message' => 'Status Update Successfully!']);
    }
}
