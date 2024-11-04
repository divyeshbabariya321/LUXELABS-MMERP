<?php

namespace App\Services\Facebook;

use App\Helpers;
use App\Models\SocialAdAccount;
use App\Social\SocialConfig;
use JanuSoftware\Facebook\Exception\SDKException;
use JanuSoftware\Facebook\Facebook;

class FB
{
    /**
     * @var string|null The Instagram or Facebook AccessToken.
     */
    protected ?string $token;

    protected SocialAdAccount|SocialConfig|null $config;

    /**
     * @var Facebook|string|null The subclass of the child GraphNode's.
     */
    protected Facebook|string|null $fb;

    /**
     * @throws SDKException
     */
    public function __construct(SocialAdAccount|SocialConfig $config)
    {
        // Remove decrypt function from app_secret as we are handling app_secret with $casts in models. DEVTASK-24765
        $this->token = $config->page_token;
        $this->config = $config;
        $config = [
            'app_id' => $config->api_key,
            'app_secret' => Helpers::getDecryptedData($config->api_secret),
            'default_graph_version' => 'v15.0',
        ];
        $this->fb = new Facebook($config);
    }

    /**
     * Generate Login and Authenticate URL to Instagram Graph API.
     *
     * @param  array  $permissions  Instagram permissions
     */
    public function getLoginUrl(array $permissions): string
    {
        $instagramLogin = new GraphLogin;

        return $instagramLogin->getLoginUrl($permissions);
    }

    /**
     * Get User Access Token from Instagram Graph API Callback.
     *
     *
     * @throws SDKException
     */
    public static function getUserAccessToken(): string
    {
        $instagramLogin = new GraphLogin;
        $connectedAccountsData = $instagramLogin->getUserInfo();

        return $connectedAccountsData['access_token'];
    }

    /**
     * Get Request on Instagram Graph API.
     *
     * @param  string  $endpoint  Destination Instagram endpoint that request should be sent to there.
     * @param  bool|null  $graphEdge  The request should be on `graphEdge` or `graphNode`.
     *
     * @throws SDKException
     */
    public function get(string $endpoint, ?bool $graphEdge = null): array
    {
        return (new FbPayloads($this->config))->getPayload($endpoint, $this->token, $graphEdge);
    }

    /**
     * POST Request on Instagram Graph API.
     *
     * @param  array  $params  Post parameters.
     * @param  string  $endpoint  Destination Instagram endpoint that request should be sent to there.
     *
     * @throws SDKException
     */
    public function post(array $params, string $endpoint): array
    {
        return (new FbPayloads($this->config))->postPayload($params, $endpoint, $this->token);
    }

    /**
     * DELETE Request on Instagram Graph API.
     *
     * @param  array  $params  DELETE parameters.
     * @param  string  $endpoint  Destination Instagram endpoint that request should be sent to there.
     *
     * @throws SDKException
     */
    public function delete(array $params, string $endpoint): array
    {
        return (new FbPayloads($this->config))->deletePayload($params, $endpoint, $this->token);
    }

    /**
     * Get Instagram connected Accounts List.
     *
     *
     * @throws SDKException
     */
    public function getConnectedAccountsList(): array
    {
        $accounts = self::get('/me/accounts', true);

        $connected_instagram_ids = [];
        foreach ($accounts as $value) {
            $result = self::get('/'.$value['id'].'?fields=instagram_business_account');

            if (@$result['instagram_business_account']) {
                $fb_data = [
                    'fb_page_id' => $value['id'],
                    'fb_page_access_token' => $value['access_token'],
                    'instagram_page_id' => $result['instagram_business_account']['id'],
                ];
                // push instagram account ID to array
                $connected_instagram_ids[] = $fb_data;
            }
        }

        $instagram_accounts = [];
        foreach ($connected_instagram_ids as $value) {
            $response = self::get('/'.$value['instagram_page_id'].'?fields=name,biography,username,followers_count,follows_count,media_count,profile_picture_url,website');

            $instagram_account = $response;
            $instagram_account['fb_page_id'] = $value['fb_page_id'];
            $instagram_account['fb_page_access_token'] = $value['fb_page_access_token'];

            $instagram_accounts[] = json_decode(json_encode($instagram_account));
        }

        return ['success' => 'true', 'instagramAccounts' => $instagram_accounts];
    }

    /**
     * Subscribe Webhook to Graph API.
     *
     * @param  int  $facebookPageId  Facebook Page ID
     * @param  string  $facebookPageAccessToken  Facebook Page Access Token
     * @param  array  $subscribed_fields  Page field (example: ["feed"])
     *
     * @throws SDKException
     */
    public function subscribeWebhook(int $facebookPageId, string $facebookPageAccessToken, array $subscribed_fields = ['email']): array
    {
        $fields = implode(',', $subscribed_fields);

        return $this->post([], '/'.$facebookPageId.'/subscribed_apps?subscribed_fields='.$fields.'&access_token='.$facebookPageAccessToken);
    }

    /**
     * Get Comment Graph API.
     *
     * @param  string  $comment_id  Comment ID
     * @param  array  $fields  Required fields
     *
     * @throws FbException|SDKException
     */
    public function getComment(string $comment_id, array $fields = []): array
    {
        if (empty($comment_id)) {
            $error = 'Instagram Comment Message: Missing Comment ID!';

            error_log($error);

            throw new FbException($error);
        }

        $endpoint = '/'.$comment_id;

        if (count($fields) > 0) {
            $fields_str = implode(',', $fields);
            $endpoint = '/'.$comment_id.'?fields='.$fields_str;
        }

        return self::get($endpoint);
    }

    public function getInstaPostComments(string $post_id)
    {
        if (empty($post_id)) {
            $error = 'Instagram post: Missing post ID!';

            error_log($error);

            throw new FbException($error);
        }

        $endpoint = '/'.$post_id.'/comments?fields=from,text,timestamp,replies{from,id,timestamp,text}&limit=1000';

        return self::get($endpoint, true);
    }

    /**
     * Add Comment Graph API.
     *
     * @param  string  $message  Comment's Text
     * @param  string  $recipient_id  Post or Comment ID
     *
     * @throws FbException|SDKException
     */
    public function addComment(string $message, string $recipient_id): array
    {
        $endpoint = $recipient_id.'/replies';

        // Check if we have recipient or content
        if (empty($message) || empty($recipient_id)) {
            $error = 'Instagram Comment Message: Missing message or recipient!';

            error_log($error);

            throw new FbException($error);
        }

        $params = [
            'message' => $message,
        ];

        return self::post($params, $endpoint);
    }

    /**
     * DELETE Comment Graph API.
     *
     * @param  string  $comment_id  Comment ID
     *
     * @throws SDKException|FbException
     */
    public function deleteComment(string $comment_id): array
    {
        if (empty($comment_id)) {
            $error = 'DELETE Comment Message: Missing Comment ID!';

            error_log($error);

            throw new FbException($error);
        }

        $endpoint = '/'.$comment_id;

        $params = [];

        return self::delete($params, $endpoint);
    }

    /**
     * DELETE Facebook Page post Graph API.
     *
     * @param  string  $comment_id  Comment ID
     *
     * @throws SDKException|FbException
     */
    public function deletePagePost(string $post_id): array
    {
        if (empty($post_id)) {
            $error = 'Facebook DELETE Post Message: Missing Post ID!';

            error_log($error);

            throw new FbException($error);
        }

        $endpoint = '/'.$post_id;

        $params = [];

        return self::delete($params, $endpoint);
    }

    public function addPagePost(string $page_id, array $data)
    {
        return self::post($data, "/$page_id/feed");
    }

    /**
     * Hide Comment Graph API.
     *
     * @param  string  $comment_id  Comment ID
     * @param  bool  $status  Hide => true | UnHide => false
     *
     * @throws SDKException|FbException
     */
    public function hideComment(string $comment_id, bool $status): array
    {
        if (empty($comment_id)) {
            $error = 'Instagram HIDE Comment Message: Missing Comment ID';

            error_log($error);

            throw new FbException($error);
        }

        $endpoint = '/'.$comment_id;

        $params = [
            'hide' => $status,
        ];

        return self::post($params, $endpoint);
    }

    /**
     * Get Message Graph API.
     *
     * @param  string  $message_id  Message ID
     * @param  array  $fields  Required fields
     *
     * @throws SDKException|FbException
     */
    public function getMessage(string $message_id, array $fields = []): array
    {
        if (empty($message_id)) {
            $error = 'Instagram Messenger GET Message: Missing Message ID!';

            error_log($error);

            throw new FbException($error);
        }

        $endpoint = '/'.$message_id.'?fields=message,from,created_time,attachments';

        if (count($fields) > 0) {
            $fields_str = implode(',', $fields);
            $endpoint = '/'.$message_id.'?fields='.$fields_str;
        }

        return self::get($endpoint);
    }

    /**
     * Add Text Message Graph API.
     *
     * @param  string  $recipient_id  Instagram USER_ID
     * @param  string  $message  Message's Text
     *
     * @throws SDKException|FbException
     */
    public function addTextMessage(string $recipient_id, string $message): array
    {
        if (empty($recipient_id) || empty($message)) {
            $error = 'Instagram ADD Text Message in Messenger: Missing message or recipient!';
            throw new FbException($error);
        }

        $endpoint = '/me/messages';

        $params = [
            'recipient' => [
                'id' => $recipient_id,
            ],
            'message' => [
                'text' => $message,
            ],
        ];

        return self::post($params, $endpoint);
    }

    /**
     * @throws SDKException
     * @throws FbException
     */
    public function replyFbMessage(string $page_id, string $recipient_id, string $message): array
    {
        if (empty($recipient_id) || empty($message)) {
            $error = 'Facebook ADD Text Message in Messenger: Missing message or recipient!';
            throw new FbException($error);
        }

        $endpoint = "/$page_id/messages";

        $params = [
            'recipient' => [
                'id' => $recipient_id,
            ],
            'message' => [
                'text' => $message,
            ],
        ];

        return self::post($params, $endpoint);
    }

    public function addMediaToInstagram(string $page_id, $image_url, $caption)
    {
        $endpoint = "/$page_id/media";

        $params = [
            'image_url' => $image_url,
            // 'is_carousel_item' => true,
            'caption' => $caption,
        ];

        return self::post($params, $endpoint);
    }

    public function addCarouselMediaToInstagram(string $page_id, $media, $caption)
    {
        $endpoint = "/$page_id/media";

        $params = [
            'media_type' => 'CAROUSEL',
            'caption' => $caption,
            'children' => $media,
        ];

        return self::post($params, $endpoint);
    }

    public function publishMediaPostToInstagram(string $page_id, $data): array
    {
        $endpoint = "/$page_id/media_publish";

        return self::post($data, $endpoint);
    }

    /**
     * Add Media Message Graph API.
     *
     * @param  string  $recipient_id  Instagram USER_ID
     * @param  string  $url  Message Attachment's url
     * @param  string  $type  Message Attachment's type
     *
     * @throws SDKException|FbException
     */
    public function addMediaMessage(string $recipient_id, string $url, string $type = 'image'): array
    {
        if (empty($recipient_id) || empty($url)) {
            $error = 'Instagram ADD Media Message in Messenger: Missing attachment or recipient!';

            error_log($error);

            throw new FbException($error);
        }

        $endpoint = '/me/messages';

        $params = [
            'recipient' => [
                'id' => $recipient_id,
            ],
            'message' => [
                'attachment' => [
                    'type' => $type,
                    'payload' => [
                        'url' => $url,
                    ],
                ],
            ],
        ];

        return self::post($params, $endpoint);
    }

    public function getAdAccounts(): array
    {
        $accounts = self::get('/me/adaccounts', true);

        return ['success' => true, $accounts];
    }

    /**
     * AdCampaign based on the ad account id API
     * FB get /act_{ad_account_id}/campaigns
     *
     *
     * @throws SDKException
     */
    public function getCampaigns(string $ad_account_id): array
    {
        $creatives = 'name,id,object_id,object_story_id,object_type,object_url,title,status';
        $ads = "id,status,name,effective_status,created_time,creative{{$creatives}}";
        $adsets = "bid_amount,name,campaign_id,objective,status,effective_status,created_time,lifetime_budget,billing_event,destination_type,start_time,end_time,daily_budget,id,ads{{$ads}},adcreatives{{$creatives}}";
        $campaigns = self::get("act_$ad_account_id/campaigns?fields=buying_type,name,objective,daily_budget,created_time,status,lifetime_budget,id,adsets{{$adsets}}&limit=1000", true);

        return ['success' => true, 'campaigns' => $campaigns];
    }

    public function getAdsets(string $ad_account_id): array
    {
        $campaigns = self::get("act_$ad_account_id/adsets?fields=bid_amount,name,campaign_id,objective,status,effective_status,created_time,lifetime_budget,billing_event,destination_type,start_time,end_time,daily_budget,id&limit=1000", true);

        return ['success' => true, 'adsets' => $campaigns];
    }

    public function createAdsets(string $ad_account_id, array $data): array
    {
        $endpoint = "/act_$ad_account_id/adsets";

        return self::post($data, $endpoint);
    }

    /**
     * Ads based on the ad account id API
     * FB get /act_{ad_account_id}/ads
     *
     *
     * @throws SDKException
     */
    public function getAds(string|int $ad_account_id): array
    {
        $ads = self::get("act_$ad_account_id/ads", true);

        return ['success' => true, 'ads' => $ads];
    }

    public function createAd(string $ad_account_id, array $data): array
    {
        $endpoint = "/act_$ad_account_id/ads";

        return self::post($data, $endpoint);
    }

    public function getCreatives(string|int $ad_account_id): array
    {
        $ads = self::get("act_$ad_account_id/adcreatives?fields=object_id,id,object_store_url,object_story_id,object_story_spec,object_type,object_url,title,name&limit=1000", true);

        return ['success' => true, 'adcreatives' => $ads];
    }

    public function createAdCreatives(string $ad_account_id, array $data): array
    {
        $endpoint = "/act_$ad_account_id/adcreatives";

        return self::post($data, $endpoint);
    }

    public function createCampaign(string $ad_account_id, array $data): array
    {
        $endpoint = "/act_$ad_account_id/campaigns";

        return self::post($data, $endpoint);
    }

    /**
     * @throws SDKException Get the conversation for page ID
     */
    public function getConversations(string|int $page_id): array
    {
        $conversation = self::get("$page_id/conversations?fields=name,messages,can_reply,id,is_subscribed,link,message_count,participants,senders,subject&limit=1000000", true);

        return ['success' => true, 'conversations' => $conversation];
    }

    /**
     * @throws SDKException Get the conversation for page ID
     */
    public function getInstagramConversations(string|int $page_id): array
    {
        $conversation = self::get("$page_id/conversations?platform=instagram&fields=name,messages{created_time,from,id,is_unsupported,reactions,message,to,attachments.limit(1000)},can_reply,id,is_subscribed,link,message_count,participants,senders,subject&limit=1000000", true);

        return ['success' => true, 'conversations' => $conversation];
    }

    /**
     * @throws SDKException Get the messages for the conversation for conversation ID
     */
    public function getConversation(int|string $conversation_id): array
    {
        $conversation = self::get($conversation_id.'?fields=id,messages.limit(1000){created_time,from,id,message,to}');

        return ['success' => true, 'conversation' => $conversation];
    }

    /**
     * @throws SDKException Get the messages for the conversation for conversation ID
     */
    public function getPageFeed(int|string $page_id): array
    {
        $feed = self::get($page_id.'/feed?fields=message,id,created_time', true);

        return ['success' => true, 'feed' => $feed];
    }

    public function getInstagramPosts(string|int $account_id): array
    {
        $feed = self::get($account_id.'?fields=media{id,caption,like_count,comments_count,timestamp,media_product_type,media_type,owner,permalink,media_url,children{media_url}}&limit=1000000');

        return ['success' => true, 'posts' => $feed];
    }

    public function addPostComments(string $message, string $post_id): array
    {
        $endpoint = $post_id.'/comments';

        // Check if we have recipient or content
        if (empty($message) || empty($post_id)) {
            $error = 'Facebook Comment Message: Missing message or post id!';

            error_log($error);

            throw new FbException($error);
        }

        $params = [
            'message' => $message,
        ];

        return self::post($params, $endpoint);
    }

    public function replyToPostComments(string $message, string $comment_id)
    {
        $endpoint = $comment_id.'/comments';

        // Check if we have recipient or content
        if (empty($message) || empty($comment_id)) {
            $error = 'Facebook Comment Message: Missing message or comment id!';

            error_log($error);

            throw new FbException($error);
        }

        $params = [
            'message' => $message,
        ];

        return self::post($params, $endpoint);
    }

    public function replyToInstaPostComments(string $message, string $comment_id)
    {
        $endpoint = $comment_id.'/replies';

        // Check if we have recipient or content
        if (empty($message) || empty($comment_id)) {
            $error = 'Instagram Comment Message: Missing message or comment id!';

            error_log($error);

            throw new FbException($error);
        }

        $params = [
            'message' => $message,
        ];

        return self::post($params, $endpoint);
    }

    public function getPostComments(string|int $post_id)
    {
        $endpoint = $post_id.'/comments?fields=from,message,can_comment,parent,comments{created_time,message,object,id,from},id,object,comment_count,created_time&limit=1000';
        if (empty($post_id)) {
            $error = 'Facebook Comment Message: Missing message or post id!';
            error_log($error);
            throw new FbException($error);
        }

        return self::get($endpoint, true);
    }
}
