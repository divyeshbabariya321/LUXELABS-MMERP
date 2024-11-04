<?php

namespace App\Console\Commands;

use App\Library\Youtube\Helper;
use App\Models\YoutubeChannel;
use Illuminate\Console\Command;

class ChannelDataSync extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'channeldata-auto-sync';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Channel Data Automatically Sync.';

    /**
     * Create a new command instance.
     *
     * @return void
     */

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $youtubeChannels = YoutubeChannel::whereNotNull('oauth2_refresh_token')->get();
        foreach ($youtubeChannels as $channelTable) {
            $accessToken = Helper::getAccessTokenFromRefreshToken($channelTable->oauth2_refresh_token, $channelTable->id);
            if ($accessToken) {
                $getChannelData = Helper::getChanelData($accessToken, $channelTable->id);
                $channelTable->subscribe_count = ! empty($getChannelData['statistics']['subscriberCount']) ? $getChannelData['statistics']['subscriberCount'] : null;
                $channelTable->video_count = ! empty($getChannelData['statistics']['videoCount']) ? $getChannelData['statistics']['videoCount'] : null;
                $channelTable->chanelId = ! empty($getChannelData['id']) ? $getChannelData['id'] : null;
                $channelTable->chanel_name = ! empty($getChannelData['snippet']['title']) ? $getChannelData['snippet']['title'] : null;
                $channelTable->save();
                Helper::getVideoAndInsertDB($channelTable->id, $accessToken, $channelTable->chanelId);
            }
        }

        return $this->info('Channeldata Auto sync Done.');
    }
}
