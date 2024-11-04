<?php

namespace App\Library\Youtube;

use App\Models\YoutubeChannel;
use App\Models\YoutubeComment;
use App\Models\YoutubeVideo;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class Helper
{
    public static function getAccessTokenFromRefreshToken($refreshToken, $id)
    {
        try {
            $youtubeChannel = YoutubeChannel::where('id', $id)->first();

            $params = [
                'refresh_token' => $youtubeChannel->oauth2_refresh_token,
                'client_id' => $youtubeChannel->oauth2_client_id,
                'client_secret' => $youtubeChannel->oauth2_client_secret,
                'grant_type' => 'refresh_token',
            ];
            $headers = [
                'Host' => 'oauth2.googleapis.com',
            ];

            $response = Http::withHeaders($headers)->post('https://oauth2.googleapis.com/token', $params)->json();

            $expireIn = ! empty($response['expires_in']) ? $response['expires_in'] : null;

            if (! empty($expireIn)) {
                $currentTime = strtotime(Carbon::now());
                $expireIn = Carbon::createFromTimestamp(($currentTime + $expireIn));
                YoutubeChannel::where('id', $id)->update(['token_expire_time' => $expireIn]);
            }

            return $response['access_token'];
        } catch (Exception $e) {
            Log::info(__('failedGetAccessToken', ['something went wrong']));
            Log::info($e->getMessage());
        }
    }

    public static function getVideoCategories()
    {
        return [
            '1' => 'Film & Animation',
            '2' => 'Autos & Vehicles',
            '10' => 'Music',
            '15' => 'Pets & Animals',
            '17' => 'Sports',
            '18' => 'Short Movies',
            '19' => 'Travel & Events',
            '20' => 'Gaming',
            '21' => 'Videoblogging',
            '22' => 'People & Blogs',
            '23' => 'Comedy',
            '24' => 'Entertainment',
            '25' => 'News & Politics',
            '26' => 'Howto & Style',
            '27' => 'Education',
            '28' => 'Science & Technology',
            '29' => 'Nonprofits & Activism',
            '30' => 'Movies',
            '31' => 'Anime/Animation',
            '32' => 'Action/Adventure',
            '33' => 'Classics',
            '34' => 'Comedy',
            '35' => 'Documentary',
            '36' => 'Drama',
            '37' => 'Family',
            '38' => 'Foreign',
            '39' => 'Horror',
            '40' => 'Sci=>Fi/Fantasy',
            '41' => 'Thriller',
            '42' => 'Shorts',
            '43' => 'Shows',

        ];
    }

    public static function getChanelData($accessToken, $youtubeChanelTableId)
    {
        self::regenerateToken($youtubeChanelTableId);

        $youtubeChannels = Http::withToken($accessToken)
            ->get('https://www.googleapis.com/youtube/v3/channels?part=id,topicDetails,contentDetails,contentOwnerDetails,statistics,localizations,snippet,brandingSettings&mine=true')
            ->json();
        Log::info($youtubeChannels);
        Log::info($accessToken);
        $chanelsList = [];
        if (! empty($youtubeChannels['kind']) && $youtubeChannels['kind'] == 'youtube#channelListResponse') {
            if (! empty($youtubeChannels['kind']) && $youtubeChannels['kind'] == 'youtube#channelListResponse') {
                if (! empty($youtubeChannels['items'])) {
                    foreach ($youtubeChannels['items'] as $youtubeChannel) {
                        $chanelsList = $youtubeChannel;
                    }
                }
            }
        }

        return $chanelsList;
    }

    public static function regenerateToken($id)
    {
        $youtubeChannelData = YoutubeChannel::where('id', $id)->first();
        $tokenExpireTime = strtotime(Carbon::parse($youtubeChannelData->token_expire_time));
        $currentTime = strtotime(Carbon::now());

        if (($tokenExpireTime - $currentTime) <= 0) {
            self::updateYoutubeAccessToken($id);
        }
    }

    public static function updateYoutubeAccessToken($id)
    {
        try {
            $youtubeChannel = YoutubeChannel::where('id', $id)->first();

            $params = [
                'refresh_token' => $youtubeChannel->oauth2_refresh_token,
                'client_id' => $youtubeChannel->oauth2_client_id,
                'client_secret' => $youtubeChannel->oauth2_client_secret,
                'grant_type' => 'refresh_token',
            ];
            $headers = [
                'Host' => 'oauth2.googleapis.com',
            ];

            $response = Http::withHeaders($headers)->post('https://oauth2.googleapis.com/token', $params)->json();
            $youtubeChannel->access_token = $response['access_token'];

            $expireIn = ! empty($response['expires_in']) ? $response['expires_in'] : null;
            if (! empty($expireIn)) {
                $currentTime = strtotime(Carbon::now());
                $expireIn = Carbon::createFromTimestamp(($currentTime + $expireIn));
            }
            $youtubeChannel->token_expire_time = $expireIn;
            $youtubeChannel->save();
        } catch (Exception $e) {
            Log::info(__('failedToUpdateUserAccessToken', [$youtubeChannel]));
            Log::info($e->getMessage());
        }
    }

    public static function VideoListByChanelId($youtubeChanelTableId, $accessToken, $chanelId)
    {
        self::regenerateToken($youtubeChanelTableId);

        $videoIds = self::getVideoIds($youtubeChanelTableId, $accessToken, $chanelId);

        $videoData = self::getVideo($youtubeChanelTableId, $accessToken, $videoIds);

        return $videoData;
    }

    public static function channelExistsOrNot($channelId)
    {
        return YoutubeChannel::where('chanelId', $channelId)->first();
    }

    public static function getVideoAndInsertDB($youtubeChanelTableId, $accessToken, $chanelId)
    {
        self::regenerateToken($youtubeChanelTableId);
        $videoData = self::VideoListByChanelId($youtubeChanelTableId, $accessToken, $chanelId);
        $videoIds = [];
        if (! empty($videoData)) {
            foreach ($videoData as $value) {
                $videoAdd = [];
                $videoAdd['youtube_channel_id'] = $youtubeChanelTableId;
                $videoAdd['media_id'] = ! empty($value['media_id']) ? $value['media_id'] : '';
                $videoAdd['title'] = ! empty($value['title']) ? $value['title'] : '';
                $videoAdd['link'] = ! empty($value['link']) ? $value['link'] : '';
                $videoAdd['like_count'] = ! empty($value['like_count']) ? $value['like_count'] : '';
                $videoAdd['view_count'] = ! empty($value['view_count']) ? $value['view_count'] : '';
                $videoAdd['create_time'] = ! empty($value['create_time']) ? $value['create_time'] : '';
                $videoAdd['description'] = ! empty($value['description']) ? $value['description'] : '';
                $videoAdd['dislike_count'] = ! empty($value['dislike_count']) ? $value['dislike_count'] : '';
                $videoAdd['comment_count'] = ! empty($value['comment_count']) ? $value['comment_count'] : '';
                $videoAdd['channel_id'] = ! empty($value['channel_id']) ? $value['channel_id'] : '';

                $checkVideoExist = self::checkVideoExistOrNot($videoAdd['media_id']);

                if (empty($checkVideoExist)) {
                    $youtubeVideoData = YoutubeVideo::create($videoAdd);
                } else {
                    $youtubeVideoData = YoutubeVideo::where('media_id', $videoAdd['media_id'])->first();
                }

                self::getCommentAndInsertInDB($youtubeVideoData->id, $youtubeChanelTableId, $value['media_id'], $accessToken);
                array_push($videoIds, $videoAdd['media_id']);
            }
        }

        return $videoIds;
    }

    public static function checkVideoExistOrNot($videoId)
    {
        return YoutubeVideo::where('media_id', $videoId)->first();
    }

    public static function getCommentAndInsertInDB($youtubeVideoTableId, $youtubeChanelTableId, $videoId, $accessToken)
    {
        self::regenerateToken($youtubeChanelTableId);

        $commentListObjs = Http::withToken($accessToken)
            ->get('https://youtube.googleapis.com/youtube/v3/commentThreads?part=snippet&maxResults=100&mine=true&videoId='.$videoId)
            ->json();

        $comments = [];
        if (! empty($commentListObjs['kind'])) {
            if ($commentListObjs['kind'] == 'youtube#commentThreadListResponse') {
                if (! empty($commentListObjs['items'])) {
                    $videoLists = $commentListObjs['items'];
                    foreach ($videoLists as $video) {
                        if ($video['kind'] == 'youtube#commentThread') {
                            $commentInsertData['youtube_video_id'] = $youtubeVideoTableId;
                            $commentInsertData['title'] = ! empty($video['snippet']['topLevelComment']['snippet']['textOriginal']) ? $video['snippet']['topLevelComment']['snippet']['textOriginal'] : '';
                            $commentInsertData['like_count'] = ! empty($video['snippet']['topLevelComment']['snippet']['likeCount']) ? $video['snippet']['topLevelComment']['snippet']['likeCount'] : '';
                            $commentInsertData['create_time'] = ! empty($video['snippet']['topLevelComment']['snippet']['publishedAt']) ? $video['snippet']['topLevelComment']['snippet']['publishedAt'] : '';
                            $commentInsertData['video_id'] = ! empty($video['snippet']['topLevelComment']['snippet']['videoId']) ? $video['snippet']['topLevelComment']['snippet']['videoId'] : '';
                            $commentInsertData['comment_id'] = ! empty($video['id']) ? $video['id'] : '';
                            $checkCommnetExistsOrNot = self::checkCommentExistsOrNot($commentInsertData['comment_id']);
                            if (empty($checkCommnetExistsOrNot)) {
                                YoutubeComment::create($commentInsertData);
                            } else {
                                YoutubeComment::where('comment_id', $commentInsertData['comment_id'])->update($commentInsertData);
                            }
                        }
                    }
                }
            }
        }

        return true;
    }

    public static function checkCommentExistsOrNot($commentId)
    {
        return YoutubeComment::where('comment_id', $commentId)->first();
    }

    public static function getVideo($youtubeChanelTableId, $accessToken, $videoId)
    {
        self::regenerateToken($youtubeChanelTableId);
        $videosArr = [];

        $videos = Http::withToken($accessToken)
            ->get('https://www.googleapis.com/youtube/v3/videos?part=snippet,contentDetails,statistics&maxResults=50&id='.implode(',', $videoId))
            ->json();

        if (isset($videos['kind'])) {
            if ($videos['kind'] == 'youtube#videoListResponse') {
                if (! empty($videos['items'])) {
                    $videoLists = $videos['items'];
                    foreach ($videoLists as $video) {
                        if (! empty($video)) {
                            $videoObj = [];
                            if (isset($videos['kind'])) {
                                if ($video['kind'] == 'youtube#video') {
                                    if (! empty($video['id'])) {
                                        $videoObj['media_id'] = $video['id'];
                                        $videoObj['title'] = ! empty($video['snippet']['title']) ? $video['snippet']['title'] : '';
                                        $videoObj['link'] = 'https://www.youtube.com/embed/'.$video['id'];
                                        $videoObj['like_count'] = ! empty($video['statistics']['likeCount']) ? $video['statistics']['likeCount'] : '';
                                        $videoObj['view_count'] = ! empty($video['statistics']['viewCount']) ? $video['statistics']['viewCount'] : '';
                                        $videoObj['dislike_count'] = ! empty($video['statistics']['dislikeCount']) ? $video['statistics']['dislikeCount'] : '';
                                        $videoObj['comment_count'] = ! empty($video['statistics']['commentCount']) ? $video['statistics']['commentCount'] : '';
                                        $videoObj['title'] = ! empty($video['snippet']['title']) ? $video['snippet']['title'] : '';
                                        $videoObj['description'] = ! empty($video['snippet']['description']) ? $video['snippet']['description'] : '';
                                        $videoObj['create_time'] = ! empty($video['snippet']['publishedAt']) ? date('Y-m-d H:i:s', strtotime($video['snippet']['publishedAt'])) : '';
                                        $videoObj['created_at'] = now();
                                        $videoObj['channel_id'] = ! empty($video['snippet']['channelId']) ? $video['snippet']['channelId'] : '';
                                    }
                                }
                            }

                            array_push($videosArr, $videoObj);
                        }
                    }
                }
            }
        }

        return $videosArr;
    }

    public static function getVideoIds($youtubeChanelTableId, $accessToken, $chanelId)
    {
        self::regenerateToken($youtubeChanelTableId);
        $videoId = [];

        $videoListObjs = Http::withToken($accessToken)
            ->get('https://youtube.googleapis.com/youtube/v3/search?part=snippet&maxResults=50&channelId='.$chanelId)
            ->json();
        if (isset($videoListObjs['kind'])) {
            if ($videoListObjs['kind'] == 'youtube#searchListResponse') {
                if (! empty($videoListObjs['items'])) {
                    $videoLists = $videoListObjs['items'];
                    foreach ($videoLists as $video) {
                        if ($video['id']['kind'] == 'youtube#video') {
                            array_push($videoId, $video['id']['videoId']);
                        }
                    }
                }
            }
        }

        return $videoId;
    }
}
