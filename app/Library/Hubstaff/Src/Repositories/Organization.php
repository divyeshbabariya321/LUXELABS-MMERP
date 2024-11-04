<?php

namespace App\Library\Hubstaff\Src\Repositories;

use Curl\Curl;

class Organization
{
    private $accessToken;

    private $urls = [
        'allOrgs' => 'https://api.hubstaff.com/v1/organizations',
        'orgDetail' => 'https://api.hubstaff.com/v1/organizations/{orgId}',
        'orgProjects' => 'https://api.hubstaff.com/v2/organizations/{orgId}/projects',
        'orgUsers' => 'https://api.hubstaff.com/v2/organizations/{orgId}/members',
        'organizations-activity' => 'https://api.hubstaff.com/v2/organizations/{orgId}/activities',
    ];

    /**
     * Constructor to initialize appToken and authToken
     *
     * @param appToken [string]  authToken [string]
     * @param  mixed  $accessToken
     * @return object this
     */
    public function __construct($accessToken)
    {
        $this->accessToken = 'Bearer '.$accessToken;

        return $this;
    }

    /**
     * Get all users list
     *
     * @param offset [numetric & optional]
     * @param  mixed  $offset
     * @return object organizations
     */
    public function getAllOrgs($offset = 0): object
    {
        $curl = new Curl;
        $curl->setHeader('Authorization', $this->accessToken);

        $curl->get($this->urls['allOrgs'], [
            'offset' => $offset,
        ]);
        if ($curl->error) {
            echo 'errorCode'.$curl->error_code;
            exit();
        } else {
            $response = json_decode($curl->response);
        }

        $curl->close();

        return $response->organizations;
    }

    /**
     * Get organization detail from organization Id
     *
     * @param orgId [integer]
     * @param  null|mixed  $orgId
     * @return object organization
     */
    public function getOrgDetail($orgId = null): object
    {
        $curl = new Curl;
        $curl->setHeader('Authorization', $this->accessToken);

        $url = str_replace('{orgId}', $orgId, $this->urls['orgDetail']);

        $curl->get($url);
        if ($curl->error) {
            echo 'errorCode'.$curl->error_code;
            exit();
        } else {
            $response = json_decode($curl->response);
        }

        $curl->close();

        return $response->organization;
    }

    /**
     * Retrieve projects for an organization
     *
     * @param orgId [integer], offset [numetric & optional]
     * @param  null|mixed  $orgId
     * @param  mixed  $offset
     * @return object user
     */
    public function getOrgProjects($orgId = null, $offset = 0): object
    {
        $curl = new Curl;
        $curl->setHeader('Authorization', $this->accessToken);

        $url = str_replace('{orgId}', $orgId, $this->urls['orgProjects']);

        $curl->get($url, [
            'offset' => $offset,
        ]);

        if ($curl->error) {
            echo 'errorCode'.$curl->error_code;
            exit();
        } else {
            $response = json_decode($curl->response);
        }

        $curl->close();

        return $response;
    }

    public function createOrgProjects($orgId = null, $params = [])
    {
        $curl = new Curl;
        $curl->setHeader('Authorization', $this->accessToken);

        $url = str_replace('{orgId}', $orgId, $this->urls['orgProjects']);
        $curl->post($url, $params);

        if ($curl->error) {
            echo 'errorCode'.$curl->error_code;
            exit();
        } else {
            $response = json_decode($curl->response);
        }

        $curl->close();

        return $response;
    }

    /**
     * Retrieve users for an organization
     *
     * @param orgId [integer], offset [numetric & optional]
     * @param  null|mixed  $orgId
     * @param  mixed  $offset
     * @param  mixed  $pagestartId
     * @return object organizationusers
     */
    public function getOrgUsers($orgId = null, $offset = 0, $pagestartId = 0): object
    {
        $curl = new Curl;
        $curl->setHeader('Authorization', $this->accessToken);

        $url = str_replace('{orgId}', $orgId, $this->urls['orgUsers']);

        $params = [
            'offset' => $offset,
        ];

        if ($pagestartId > 0) {
            $params = [
                'page_start_id' => $pagestartId,
            ];
        }

        $curl->get($url, $params);

        if ($curl->error) {
            echo 'errorCode'.$curl->error_code;
            exit();
        } else {
            $response = json_decode($curl->response);
        }

        $curl->close();

        return $response;
    }

    /**
     * Get activitiy
     *
     * @param orgId [integer]
     * @param  mixed  $orgId
     * @param  mixed  $startTime
     * @param  mixed  $stopTime
     */
    public function getActivity($orgId, $startTime, $stopTime)
    {
        $curl = new Curl;
        $curl->setHeader('Authorization', $this->accessToken);

        $url = str_replace('{orgId}', $orgId, $this->urls['organizations-activity']);
        $curl->get($url, [
            'time_slot[start]' => date(DATE_ISO8601, strtotime($startTime)),
            'time_slot[stop]' => date(DATE_ISO8601, strtotime($stopTime)),
        ]);

        if ($curl->error) {
            print_r($curl);
            exit;
            echo 'errorCode'.$curl->error_code;
            exit();
        } else {
            $response = json_decode($curl->response);
        }

        $curl->close();

        return $response;
    }
}
