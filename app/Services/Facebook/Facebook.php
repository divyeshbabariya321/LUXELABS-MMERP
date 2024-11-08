<?php

namespace App\Services\Facebook;

use App\Image;
use App\ImageSchedule;
use Facebook\Facebook as Fb;
use Exception;
use Illuminate\Http\File;

class Facebook
{
    private $facebook;

    private $user_access_token;

    private $page_access_token;

    private $page_id;

    private $ad_acc_id;

    private $instagram_id;

    private $imageIds = [];

    private $feedId;

    /**
     * Instagram constructor.
     */
    public function __construct(Fb $facebook)
    {
        $this->facebook = $facebook;
        $this->user_access_token = config('settings.user_access_token');
        $this->page_access_token = config('settings.page_access_token');
        $this->page_id = '507935072915757';
        $this->ad_acc_id = 'act_128125721296439';
        $this->instagram_id = '17841406743743390';
    }

    public function getMentions($tag)
    {
        $tagText = $tag->name;
        $data = $this->facebook->get('ig_hashtag_search?user_id='.$this->instagram_id.'&q='.$tagText, '10606198004.0912694.8a4c5161260e41bb87fd646638d27093');

        dd($data);
    }

    public function postMedia($images, $message = ''): void
    {
        if (! is_array($images)) {
            $images = [$images];
        }

        $imageIds = [];
        $key = 0;

        $postMedia['access_token'] = $this->page_access_token;

        foreach ($images as $image) {
            $mediaId = $this->postMediaObject($image);
            if ($mediaId !== false) {
                $imageIds[] = $image->id;
                $postMedia['attached_media['.$key.']'] = '{"media_fbid":"'.$mediaId.'"}';
                $key++;
            }

            $postMedia['published'] = 'true';
        }

        $data = null;
        $postMedia['message'] = $message;

        try {
            $response = $this->facebook->post('/me/feed', $postMedia)->getDecodedBody();
            $data = $response['id'];
            ImageSchedule::whereIn('image_id', $imageIds)->update([
                'posted' => 1,
            ]);
        } catch (Exception $exception) {
            dd($exception);
            $data = false;
        }

        $this->imageIds = $imageIds;
        $this->feedId = $data;
    }

    private function postMediaObject(Image $image)
    {
        $data['caption'] = $image->schedule->description;
        $data['published'] = 'false';
        $data['access_token'] = $this->page_access_token;
        $file = public_path().'/uploads/social-media/'.$image->filename;
        if (! file_exists($file)) {
            $file = public_path().'/uploads/'.$image->filename;
        }
        $file = new File($file);
        $data['source'] = $this->facebook->fileToUpload($file);

        $mediaId = null;

        try {
            $response = $this->facebook->post('/me/photos', $data)->getDecodedBody();
            if (is_array($response)) {
                $mediaId = $response['id'];
            }
        } catch (Exception $exception) {
            $mediaId = false;
        }

        return $mediaId;
    }

    /**
     * @return mixed
     */
    public function getImageIds()
    {
        return $this->imageIds;
    }

    /**
     * @return mixed
     */
    public function getFeedId()
    {
        return $this->feedId;
    }

    /**
     * @throws \Facebook\Exceptions\FacebookSDKException
     *                                                   Get the conversation for page ID
     */
    public function getConversations(): array
    {
        $conversation = $this->facebook->get($this->page_id.'/conversations?fields=name,messages{created_time,from,id,message,sticker,tags,to,attachments.limit(1000)},can_reply,id,is_subscribed,link,message_count,participants,senders,subject&limit=1000000', $this->page_access_token);

        return $conversation->getDecodedBody();
    }

    /**
     * @param  mixed  $id
     *
     * @throws \Facebook\Exceptions\FacebookSDKException
     *                                                   Get the messages for the conversation for conversation ID
     */
    public function getConversation($id): array
    {
        $conversation = $this->facebook->get($id.'?fields=id,messages.limit(1000){created_time,from,id,message,to}', $this->page_access_token);

        return $conversation->getDecodedBody();
    }
}
