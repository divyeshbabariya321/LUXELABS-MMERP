<?php

namespace App\Http\Controllers;

use App\BusinessComment;
use App\BusinessPost;
use App\ChatMessage;
use App\Helpers\MessageHelper;
use App\LogRequest;
use App\Social\SocialConfig;
use App\SocialContact;
use App\SocialContactThread;
use App\SocialWebhookLog;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class SocialWebhookController extends Controller
{
    /**
     * Verify Facebook and Instagram Webhook URL
     *
     * @method GET
     */
    public function verifyWebhook(Request $request)
    {
        $hub = $request->all();
        $verifyToken = $hub['hub_verify_token'];
        $challange = $hub['hub_challenge'];

        SocialWebhookLog::log(SocialWebhookLog::INFO, 'request params', ['token' => $hub]);

        SocialWebhookLog::log(SocialWebhookLog::INFO, 'Verify Webhook => Webhook Verifying.....', ['token' => $verifyToken, 'challange' => $challange]);

        $countAccount = SocialConfig::where('webhook_token', $verifyToken)->count();

        if ($countAccount == 1) {
            SocialWebhookLog::log(SocialWebhookLog::SUCCESS, 'Verify Webhook => Webhook Verified', ['token' => $verifyToken, 'challange' => $challange]);
            SocialWebhookLog::log(SocialWebhookLog::INFO, 'ans.....', ['token' => $challange]);
            echo $hub['hub_challenge'];
        } else {
            SocialWebhookLog::log(SocialWebhookLog::ERROR, 'Verify Webhook => Webhook not Verified', ['token' => $verifyToken, 'challange' => $challange]);
        }
    }

    public function webhookfbtoken(Request $request)
    {
        $data = json_decode($request->getContent(), true);
        SocialWebhookLog::log(SocialWebhookLog::INFO, 'Webhook => FB Token', ['data' => $data]);

        return true;
    }

    /**
     * When Subscirbe Event fire on Facebook and Instagram
     *
     * @method POST
     */
    public function webhook(Request $request)
    {
        SocialWebhookLog::log(SocialWebhookLog::INFO, 'Webhook => Request Body', ['data' => 'comming']);
        $data = json_decode($request->getContent(), true);
        SocialWebhookLog::log(SocialWebhookLog::INFO, 'Webhook => Request Body', ['data' => $data]);

        foreach ($data['entry'] as $entry) {
            if (isset($entry['messaging'])) {
                $this->receiveMessage($entry, $data);
            } elseif (isset($entry['changes'])) {
                $this->changes($entry, $data);
            } else {
                SocialWebhookLog::log(SocialWebhookLog::ERROR, 'Webhook => Request Body entry type not found', ['data' => $data]);
            }
        }
    }

    /**
     * Entry type = messaging
     */
    private function receiveMessage(array $entry, array $data)
    {
        foreach ($entry['messaging'] as $message) {
            if (! isset($message['message']['text'])) {
                continue;
            }

            $senderId = $message['sender']['id'];
            $recipientId = $message['recipient']['id'];
            $type = SocialContactThread::RECEIVE;
            $senderAccount = SocialConfig::where('account_id', $recipientId)->first();

            if (! $senderAccount) {
                $temp = $senderId;
                $senderId = $recipientId;
                $recipientId = $temp;
                $type = SocialContactThread::SEND;
            }

            $messageId = $message['message']['mid'];
            $text = $message['message']['text'];
            $socialAccountId = $entry['id'];
            $sendingAt = Carbon::createFromTimestampMs($message['timestamp'])->toDateTimeString();
            $account = SocialConfig::where('account_id', $recipientId)->first();

            if ($account) {
                $object = null;
                if ($data['object'] == SocialContact::TEXT_INSTA) {
                    $object = SocialContact::INSTAGRAM;
                } elseif ($data['object'] == SocialContact::TEXT_FB) {
                    $object = SocialContact::FACEBOOK;
                }
                if ($object) {
                    $user = SocialContact::where('account_id', $senderId)->where('platform', $object)->first();
                    if (! $user) {
                        $result = $this->getUserInfo($senderId, $account->page_token);
                        $response = $result['response'];
                        $httpcode = $result['http_code'];

                        SocialWebhookLog::log(SocialWebhookLog::INFO, 'Webhook (Receive Message) => Fetched user details using Page access Token', ['response' => $response, 'object' => $data['object'], 'data' => $data]);

                        if ($httpcode == 200) {
                            $user = SocialContact::create([
                                'account_id' => $senderId,
                                'social_config_id' => $account->id,
                                'name' => $response['name'],
                                'platform' => $object,
                            ]);

                            SocialWebhookLog::log(SocialWebhookLog::INFO, 'Webhook (Receive Message) => New user create', ['id' => $senderId, 'object' => $data['object'], 'data' => $data]);
                        }
                    }

                    if ($user) {
                        $socialContactThread = SocialContactThread::create([
                            'social_contact_id' => $user->id,
                            'message_id' => $messageId,
                            'sender_id' => $message['sender']['id'],
                            'recipient_id' => $message['recipient']['id'],
                            'text' => $text,
                            'type' => $type,
                            'sending_at' => $sendingAt,
                        ]);
                        $messageModel = ChatMessage::create([
                            'message' => $socialContactThread->text,
                            'message_type' => $socialContactThread->socialContact->platform == 1 ? 'IG_DMS' : 'FB_DMS',
                            'message_type_id' => $socialContactThread->social_contact_id,
                        ]);
                        if ($type == 2 && $user->socialConfig->storeWebsite->ai_assistant == 'geminiai') {
                            MessageHelper::sendGeminiAiReply($socialContactThread->text, 'DMS', $messageModel, $user->socialConfig->storeWebsite);
                        }
                        // else {
                        //     //Here is the logic for Watson Reply
                        // }

                        SocialWebhookLog::log(SocialWebhookLog::SUCCESS, 'Webhook (Receive Message) => Message Received', ['mid' => $messageId, 'object' => $data['object'], 'data' => $data]);
                    } else {
                        SocialWebhookLog::log(SocialWebhookLog::ERROR, 'Webhook (Receive Message) => User not found', ['id' => $senderId, 'object' => $data['object'], 'data' => $data]);
                    }
                } else {
                    SocialWebhookLog::log(SocialWebhookLog::ERROR, 'Webhook (Receive Message) => Object Type not found', ['object' => $data['object'], 'data' => $data, 'object' => $data['object'], 'data' => $data]);
                }
            } else {
                SocialWebhookLog::log(SocialWebhookLog::ERROR, 'Webhook (Receive Message) => Account not found', ['id' => $socialAccountId, 'object' => $data['object'], 'data' => $data]);
            }
        }
    }

    /**
     * When Status, Photo, Video or Comment Post on Page Feed
     */
    private function changes(array $entry, array $data)
    {
        foreach ($entry['changes'] as $change) {
            if ($change['field'] == BusinessPost::FEED && $change['value']['item'] == BusinessPost::STATUS) {
                $this->statusUploaded($change, $entry, $data);
            } elseif ($change['field'] == BusinessPost::FEED && $change['value']['item'] == BusinessPost::PHOTO) {
                $this->photoUploaded($change, $entry, $data);
            } elseif ($change['field'] == BusinessPost::FEED && $change['value']['item'] == BusinessPost::VIDEO) {
                $this->videoUploaded($change, $entry, $data);
            } elseif ($change['field'] == BusinessPost::FEED && $change['value']['item'] == BusinessPost::COMMENT) {
                $this->commentOnPostFB($change, $entry, $data);
            } elseif ($change['field'] == BusinessPost::COMMENTS && $data['object'] == SocialContact::TEXT_INSTA) {
                $this->commentOnPostIG($change, $entry, $data);
            } else {
                SocialWebhookLog::log(SocialWebhookLog::ERROR, 'Webhook => No changes requested found', ['data' => $data]);
            }
        }
    }

    /**
     * Status Upload
     *
     *
     * @throws Exception
     */
    private function statusUploaded(array $changes, array $entry, array $data)
    {
        try {
            $socialConfig = SocialConfig::where('account_id', $entry['id'])->firstOrFail();

            $post = BusinessPost::updateOrCreate(
                [
                    'post_id' => $changes['value']['post_id'],
                ],
                [
                    'social_config_id' => $socialConfig->id,
                    'message' => $changes['value']['message'] ?? null,
                    'item' => BusinessPost::STATUS,
                    'verb' => $changes['value']['verb'],
                    'time' => Carbon::createFromTimestamp($changes['value']['created_time'])->toDateTimeString(),
                ]
            );

            SocialWebhookLog::log(SocialWebhookLog::SUCCESS, 'Webhook (Status Upload) => Status upload Successfully', ['data' => $data, 'post' => $post->toArray()]);
        } catch (Exception $e) {
            SocialWebhookLog::log(SocialWebhookLog::ERROR, "Webhook (Status Upload) => {$e->getMessage()}", ['data' => $data]);
        }
    }

    /**
     * Photo Upload
     *
     *
     * @throws Exception
     */
    private function photoUploaded(array $changes, array $entry, array $data)
    {
        try {
            $socialConfig = SocialConfig::where('account_id', $entry['id'])->firstOrFail();

            $post = BusinessPost::updateOrCreate(
                [
                    'post_id' => $changes['value']['post_id'],
                ],
                [
                    'social_config_id' => $socialConfig->id,
                    'message' => $changes['value']['message'] ?? null,
                    'item' => BusinessPost::PHOTO,
                    'verb' => $changes['value']['verb'],
                    'time' => Carbon::createFromTimestamp($changes['value']['created_time'])->toDateTimeString(),
                ]
            );

            SocialWebhookLog::log(SocialWebhookLog::SUCCESS, 'Webhook (Photo Upload) => Photo upload Successfully', ['data' => $data, 'post' => $post->toArray()]);
        } catch (Exception $e) {
            SocialWebhookLog::log(SocialWebhookLog::ERROR, "Webhook (Photo Upload) => {$e->getMessage()}", ['data' => $data]);
        }
    }

    /**
     * Video Upload
     *
     *
     * @throws Exception
     */
    private function videoUploaded(array $changes, array $entry, array $data)
    {
        try {
            $socialConfig = SocialConfig::where('account_id', $entry['id'])->firstOrFail();

            $post = BusinessPost::updateOrCreate(
                [
                    'post_id' => $changes['value']['post_id'],
                ],
                [
                    'social_config_id' => $socialConfig->id,
                    'message' => $changes['value']['message'] ?? null,
                    'item' => BusinessPost::VIDEO,
                    'verb' => $changes['value']['verb'],
                    'time' => Carbon::createFromTimestamp($changes['value']['created_time'])->toDateTimeString(),
                ]
            );

            SocialWebhookLog::log(SocialWebhookLog::SUCCESS, 'Webhook (Video Upload) => Video upload Successfully', ['data' => $data, 'post' => $post->toArray()]);
        } catch (Exception $e) {
            SocialWebhookLog::log(SocialWebhookLog::ERROR, "Webhook (Video Upload) => {$e->getMessage()}", ['data' => $data]);
        }
    }

    /**
     * Comment on Post (FB)
     *
     *
     * @throws Exception
     */
    private function commentOnPostFB(array $changes, array $entry, array $data)
    {
        try {
            $isAdminComment = 1;
            $isParent = 0;
            $fromId = $changes['value']['from']['id'];
            $pageId = $entry['id'];
            $socialConfig = SocialConfig::where('account_id', $pageId)->firstOrFail();

            if ($pageId !== $fromId) {
                $socialContact = SocialContact::where('account_id', $fromId)->first();
                if (! $socialContact) {
                    $socialContact = SocialContact::create([
                        'account_id' => $fromId,
                        'social_config_id' => $socialConfig->id,
                        'name' => $changes['value']['from']['name'],
                        'platform' => SocialContact::FACEBOOK,
                    ]);
                }
                $socialConfig = $socialContact;
                $isAdminComment = 0;
            }

            if ($changes['value']['parent_id'] !== $changes['value']['post_id']) {
                $isParent = 1;
            }

            BusinessComment::updateOrCreate(
                [
                    'comment_id' => $changes['value']['comment_id'],
                ],
                [
                    'post_id' => $changes['value']['post_id'],
                    'is_admin_comment' => $isAdminComment,
                    'social_contact_id' => $socialConfig->id,
                    'message' => $changes['value']['message'] ?? null,
                    'photo' => $changes['value']['photo'] ?? null,
                    'is_parent' => $isParent,
                    'parent_comment_id' => $changes['value']['parent_id'] ?? null,
                    'verb' => $changes['value']['verb'],
                    'time' => Carbon::createFromTimestamp($changes['value']['created_time'])->toDateTimeString(),
                ]
            );

            SocialWebhookLog::log(SocialWebhookLog::INFO, 'Webhook (FB - Post Comment) => Comment Added Successfully', ['data' => $data]);
        } catch (Exception $e) {
            SocialWebhookLog::log(SocialWebhookLog::ERROR, "Webhook (IG - Post Comment) => {$e->getMessage()}", ['data' => $data]);
        }
    }

    /**
     * Comment on Post (IG)
     *
     *
     * @throws Exception
     */
    private function commentOnPostIG(array $changes, array $entry, array $data)
    {
        try {
            $isAdminComment = 1;
            $isParent = 0;
            $fromId = $changes['value']['from']['id'];
            $pageId = $entry['id'];
            $socialConfig = SocialConfig::where('account_id', $pageId)->firstOrFail();
            $mediaId = $changes['value']['media']['id'];
            $igMedia = BusinessPost::find($mediaId);
            if (! $igMedia) {
                $response = $this->getIGMedia($mediaId, $socialConfig->page_token);

                if ($response['http_code'] == 200) {
                    BusinessPost::create([
                        'post_id' => $mediaId,
                        'social_config_id' => $socialConfig->id,
                        'message' => $response['response']['caption'] ?? null,
                        'item' => $response['response']['media_type'],
                        'verb' => 'add',
                        'time' => $response['response']['timestamp'],
                    ]);
                } else {
                    throw new Exception('Media not found');
                }
            }

            if ($pageId !== $fromId) {
                $socialContact = SocialContact::where('account_id', $fromId)->first();
                if (! $socialContact) {
                    $socialContact = SocialContact::create([
                        'account_id' => $fromId,
                        'social_config_id' => $socialConfig->id,
                        'name' => $changes['value']['from']['username'],
                        'platform' => SocialContact::INSTAGRAM,
                    ]);
                }
                $socialConfig = $socialContact;
                $isAdminComment = 0;
            }

            if (isset($changes['value']['parent_id']) && ($changes['value']['parent_id'] !== $mediaId)) {
                $isParent = 1;
            }

            BusinessComment::updateOrCreate(
                [
                    'comment_id' => $changes['value']['id'],
                ],
                [
                    'post_id' => $mediaId,
                    'is_admin_comment' => $isAdminComment,
                    'social_contact_id' => $socialConfig->id,
                    'message' => $changes['value']['text'] ?? null,
                    'is_parent' => $isParent,
                    'parent_comment_id' => $changes['value']['parent_id'] ?? null,
                    'verb' => 'add',
                    'time' => Carbon::createFromTimestamp($entry['time'])->toDateTimeString(),
                ]
            );

            SocialWebhookLog::log(SocialWebhookLog::INFO, 'Webhook (IG - Media Comment) => Comment Added Successfully', ['data' => $data]);
        } catch (Exception $e) {
            SocialWebhookLog::log(SocialWebhookLog::ERROR, "Webhook (IG - Media Comment) => {$e->getMessage()}", ['data' => $data]);
        }
    }

    /**
     * Get Facebook or Instagram User Details
     */
    private function getUserInfo(int $userId, string $pageAccessToken)
    {
        $startTime = date('Y-m-d H:i:s', LARAVEL_START);
        $url = sprintf('https://graph.facebook.com/v12.0/%s?fields=%s&access_token=%s', $userId, 'id,name', $pageAccessToken);

        $response = Http::get($url);
        $responseData = $response->json();

        SocialWebhookLog::log(SocialWebhookLog::INFO, 'Webhook (Getting User) => Fetched user details using Page access Token', ['response' => $responseData, 'user_id' => $userId]);

        LogRequest::log($startTime, $url, 'GET', json_encode([]), $responseData, $response->status(), TemplatesController::class, 'getImageByCurl');

        return [
            'response' => $response,
            'http_code' => $response->status(),
        ];
    }

    /**
     * Get Instagram Media Details
     *
     * @param  int  $userId
     * @param  mixed  $mediaId
     */
    private function getIGMedia($mediaId, string $pageAccessToken)
    {
        $startTime = date('Y-m-d H:i:s', LARAVEL_START);
        $url = sprintf('https://graph.facebook.com/v12.0/%s?fields=%s&access_token=%s', $mediaId, 'caption,media_type,timestamp', $pageAccessToken);
        $response = Http::get($url);
        $responseData = $response->json();
        SocialWebhookLog::log(SocialWebhookLog::INFO, 'Webhook (Getting ID Media) => Fetched IG Media using Page access Token', ['response' => $responseData, 'media_id' => $mediaId]);

        LogRequest::log($startTime, $url, 'GET', json_encode([]), $responseData, $response->status(), SocialWebhookController::class, 'getImageByCurl');

        return [
            'response' => $response,
            'http_code' => $response->status(),
        ];
    }
}
