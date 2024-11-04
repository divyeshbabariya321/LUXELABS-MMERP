<?php

namespace App\Library\Watson\Interfaces;

use App\Library\Watson\Response;
use GuzzleHttp\Client as GuzzleClient;

interface ClientInterface
{
    /**
     * Make a HTTP request
     *
     * @param mixed $method
     * @param mixed $uri
     * @param mixed $options
     *
     * @return Response
     */
    public function request($method, $uri, $options = []);

    /**
     * Set the current Guzzle instance
     *
     *
     * @internal param GuzzleClient $client
     */
    public function setGuzzleInstance(GuzzleClient $guzzle);

    /**
     * Get the client options
     *
     * @return array
     */
    public function getOptions();

    /**
     *  Set the client options merging and/or overwriting its contents
     *
     * @return null
     */
    public function setOptions(array $options);

    /**
     *  Set the response instance
     *
     * @return null
     */
    public function setResponse(ResponseInterface $response);

    /**
     *  Get the response instance
     *
     * @return ResponseInterface
     */
    public function getResponse();
}
