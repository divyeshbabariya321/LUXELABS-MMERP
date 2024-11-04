<?php

namespace App\Library\Watson\Language\ToneAnalyser\V3;

use App\Library\Watson\Service;
use App\Library\Watson\Response;

class ToneAnalyserService extends Service
{
    /**
     * Base url for the service
     *
     * @var string
     */
    protected $url = 'https://gateway.watsonplatform.net/tone-analyzer/api';

    /**
     * API service version
     *
     * @var string
     */
    protected $version = 'v3';

    /**
     * ToneAnalyserService constructor
     *
     * @param $username string The service api username
     * @param $password string The service api password
     */
    public function __construct($username = null, $password = null)
    {
        parent::__construct($username, $password);
    }

    /**
     * Analyzes the tone of a piece of text
     *
     * @param mixed $textToAnalyse
     * @param mixed $version
     */
    public function plainText($textToAnalyse, $version = '2016-05-19'): Response
    {
        return $this->client->request(
            'GET',
            $this->getMountedUrl() . '/tone',
            ['query' => ['version' => $version, 'text' => $textToAnalyse]]
        );
    }
}
