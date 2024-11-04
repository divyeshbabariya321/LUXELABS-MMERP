<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use App\Models\YoutubeChannel;
use App\Library\Youtube\Helper;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class FetchYoutubeChannelData implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @param protected $inputs
     *
     * @return void
     */
    public function __construct(protected $inputs)
    {
        $refreshToken = $inputs['oauth2_refresh_token'];

        // this id is a Which is Create a New Data for create channel.
        $id                = $inputs['id'];
        $youTubeChanelData = YoutubeChannel::where('id', $id)->first();

        $accessToken    = Helper::getAccessTokenFromRefreshToken($refreshToken, $id);
        $getChannelData = Helper::getChanelData($accessToken, $id);

        $youTubeChanelData->subscribe_count = ! empty($getChannelData['statistics']['subscriberCount']) ? $getChannelData['statistics']['subscriberCount'] : null;
        $youTubeChanelData->video_count     = ! empty($getChannelData['statistics']['videoCount']) ? $getChannelData['statistics']['videoCount'] : null;
        $youTubeChanelData->chanelId        = ! empty($getChannelData['id']) ? $getChannelData['id'] : null;
        $youTubeChanelData->chanel_name     = ! empty($getChannelData['snippet']['title']) ? $getChannelData['snippet']['title'] : null;
        $youTubeChanelData->save();
        if (! empty($youTubeChanelData->chanelId)) {
            Helper::getVideoAndInsertDB($id, $accessToken, $youTubeChanelData->chanelId);
        }
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(): void
    {
    }
}
