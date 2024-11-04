<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\RequestOptions;
use GuzzleHttp\Exception\ClientException;
use Illuminate\Support\Facades\Http;

trait HubstaffTrait
{
    private $HUBSTAFF_TOKEN_FILE_NAME = 'hubstaff_tokens.json';

    private $SEED_REFRESH_TOKEN;

    private $client;

    public function init($seedToken)
    {
        $this->client             = new Client();
        $this->SEED_REFRESH_TOKEN = $seedToken;
    }

    private function checkSeedToken()
    {
        if (! $this->SEED_REFRESH_TOKEN) {
            throw new Exception('Seed token not initialized');
        }
    }

    private function refreshTokens()
    {
        $this->generateAccessToken($this->SEED_REFRESH_TOKEN);
    }

    private function getTokens($force = false)
    {
        if (! Storage::disk('local')->exists($this->HUBSTAFF_TOKEN_FILE_NAME) || $force == true) {
            $this->generateAccessToken($this->SEED_REFRESH_TOKEN);
        }
        $tokens = json_decode(Storage::disk('local')->get($this->HUBSTAFF_TOKEN_FILE_NAME));

        return $tokens;
    }

    private function generateAccessToken(string $refreshToken)
    {
        try {
            $response = Http::post(config('constants.HUBSTAFF_ACCOUNT_URL'),[
                'grant_type' => 'refresh_token',
                'refresh_token' => $refreshToken,
            ]);

            $responseJson = $response->json();

            $tokens = [
                'access_token' => $responseJson['access_token'],
                'refresh_token' => $responseJson['refresh_token'],
                'expires_in' => $responseJson['expires_in'],
            ];

            return Storage::disk('local')->put($this->HUBSTAFF_TOKEN_FILE_NAME, json_encode($tokens));
        } catch (Exception $e) {
            Log::info('Hubstaff token regenerate issue - create a personal token if expired');

            return false;
        }
    }

    public function doHubstaffOperationWithAccessToken($functionToDo, $shouldRetry = true)
    {
        $this->checkSeedToken();
        $tokens = $this->getTokens();
        try {
            return $functionToDo($tokens['access_token']);
        } catch (Exception $e) {
            if ($e instanceof ClientException) {
            echo 'Got error';
            $this->refreshTokens();
            if ($shouldRetry) {
                echo 'Retrying';
                $tokens = $this->getTokens();

                return $functionToDo($tokens['access_token']);
            }
        } else {
            throw $e;
        }
        }
    }

    private function getActivitiesBetween($startTime, $endTime, $startId = 0, $resultArray = [], $userIds = [])
    {
        try {
            $response = $this->doHubstaffOperationWithAccessToken(function ($accessToken) use ($startTime, $endTime, $startId, $userIds) {
                $url =  config('constants.HUBSTAFF_API_URL') . config('env.HUBSTAFF_ORG_ID') . '/activities?time_slot[start]=' . $startTime . '&time_slot[stop]=' . $endTime . '&page_start_id=' . $startId;

                $q = [];
                if (! empty($userIds)) {
                    foreach ($userIds as $uid) {
                        $q[] = 'user_ids[]=' . $uid;
                    }
                }
                $queryString = implode('&', $q);
                $url .= '&' . $queryString;

                Log::info('Hubstaff url : ' . $url . ' Token  : ' . $accessToken);

                return Http::withToken($accessToken)->get($url);
            });

            $responseJson = $response->json();
            $activities = $resultArray;

            foreach ($responseJson['activities'] as $activity) {
                $activities[$activity['id']] = [
                    'user_id' => $activity['user_id'],
                    'task_id' => $activity['task_id'],
                    'starts_at' => $activity['starts_at'],
                    'tracked' => $activity['tracked'],
                    'keyboard' => $activity['keyboard'],
                    'mouse' => $activity['mouse'],
                    'overall' => $activity['overall'],
                ];
            }

            if (isset($responseJson['pagination'])) {
                $nextStart = $responseJson['pagination']['next_page_start_id'];

                return $this->getActivitiesBetween($startTime, $endTime, $nextStart, $activities, $userIds);
            } else {
                return $activities;
            }
        } catch (Exception $e) {
            Log::info('Hubstaff token issue while fetching activities : ' . $e->getMessage());

            return false;
        }
    }
}
