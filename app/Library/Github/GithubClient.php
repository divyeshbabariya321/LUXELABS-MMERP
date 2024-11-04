<?php

namespace App\Library\Github;

use GuzzleHttp\Client;

class GithubClient
{
    private $client;

    private $endpoint;

    public function __construct()
    {
        $this->endpoint = 'https://api.github.com';
        $this->client   = new Client([
            'auth' => [config('env.GITHUB_USERNAME'), config('env.GITHUB_TOKEN')],
        ]);
    }

    public static function getResponse($response)
    {
        return json_decode($response->getBody()->getContents());
    }

    public function getRepository()
    {
        $url = $this->endpoint . '/orgs/' . config('env.GITHUB_ORG_ID') . '/repos';

        return self::getResponse($this->client->get($url));
    }

    public function getBranches($repoId)
    {
        $url = $this->endpoint . '/repos/' . $repoId . '/branches';

        return self::getResponse($this->client->get($url));
    }

    public function getPulls($repoId, $q = '')
    {
        $url = $this->endpoint . '/repos/' . $repoId . '/pulls?' . $q;

        return self::getResponse($this->client->get($url));
    }
}
