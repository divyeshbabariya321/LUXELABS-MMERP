<?php

namespace App\Services;

use Exception;
use GuzzleHttp\Client;

class SlackNotificationService
{
    protected $client;
    protected $slackToken;

    public function __construct()
    {
        $this->client = new Client();
        $this->slackToken = env('SLACK_BOT_TOKEN');
    }

    public function sendMessage($channel, $message)
    {
        try {
            $response = $this->client->post('https://slack.com/api/chat.postMessage', [
                'headers' => [
                    'Authorization' => 'Bearer ' . $this->slackToken,
                    'Content-Type'  => 'application/json',
                ],
                'json' => [
                    'channel' => $channel,
                    'text'    => $message,
                ],
            ]);

            $body = json_decode($response->getBody(), true);

            if (!$body['ok']) {
                return ['status' => false, 'error' => $body['error']];
            }

            return ['status' => true, 'data' => $body];
        } catch (Exception $e) {
            return ['status' => false, 'error' => $e->getMessage()];
        }
    }
}