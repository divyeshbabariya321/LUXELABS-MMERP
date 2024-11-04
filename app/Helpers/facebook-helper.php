<?php

const FB_GRAPH_DOMAIN = 'https://graph.facebook.com/';

/**
 * Make a curl call to an endpoint with params
 *
 * @param string $endpoint we are hitting
 * @param string $type     of request
 * @param array  $params   to send along with the request
 *
 * @return array with the api response
 */
function makeApiCall(string $endpoint, string $type, array $params, array|null $data): array
{
    $apiEndpoint = $endpoint . '?' . http_build_query($params);
    $http        = Http::send($type, $apiEndpoint, [
        'json'    => $data,
        'headers' => [
            'Content-Type' => 'application/json',
        ],
    ]);

    return [ // return data
        'type'         => $type,
        'endpoint'     => $endpoint,
        'params'       => $params,
        'api_endpoint' => $apiEndpoint,
        'data'         => $http->json(),
    ];
}

/**
 * Get an access token with the code from facebook.
 *
 * Endpoint https://graph.facebook.com/{fb-graph-version}/{endpoint-path}
 *
 * @param array $params Params for fb endpoint.
 */
function getFacebookResults(array $params): array
{
    // endpoint for getting an access token with code
    $endpoint = FB_GRAPH_DOMAIN . config('facebook.config.default_graph_version') . '/' . $params['endpoint_path'];

    $endpointParams = [ // params for the endpoint
        'fields'       => $params['fields'],
        'access_token' => $params['access_token'],
    ];

    // make the api call
    return makeApiCall($endpoint, $params['request_type'], $endpointParams, $params['data'] ?? []);
}
