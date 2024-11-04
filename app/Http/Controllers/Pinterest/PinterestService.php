<?php

namespace App\Http\Controllers\Pinterest;

class PinterestService extends PinterestClient
{
    public function __construct($clientId = null, $clientSecret = null, $accountId = null)
    {
        parent::__construct($clientId, $clientSecret, $accountId);
    }

    public function getAds($accountId, $query = []): array
    {
        return $this->callApi('GET', $this->buildParams('ad_accounts/' . $accountId . '/ads', $query));
    }

    public function createAd($accountId, $params, $query = []): array
    {
        return $this->callApi('POST', $this->buildParams('ad_accounts/' . $accountId . '/ads', $query), $params);
    }

    public function updateAd($accountId, $params, $query = []): array
    {
        return $this->callApi('PATCH', $this->buildParams('ad_accounts/' . $accountId . '/ads', $query), $params);
    }

    public function getAd($accountId, $id, $query = []): array
    {
        return $this->callApi('GET', $this->buildParams('ad_accounts/' . $accountId . '/ads' . $id, $query));
    }

    public function getAdsAccounts($query = []): array
    {
        return $this->callApi('GET', $this->buildParams('ad_accounts', $query));
    }

    public function createAdsAccount($params, $query = []): array
    {
        return $this->callApi('POST', $this->buildParams('ad_accounts', $query), $params);
    }

    public function getAdsAccount($accountId, $query = []): array
    {
        return $this->callApi('GET', $this->buildParams('ad_accounts/' . $accountId, $query));
    }

    public function getAdsGroups($accountId, $query = []): array
    {
        return $this->callApi('GET', $this->buildParams('ad_accounts/' . $accountId . '/ad_groups', $query));
    }

    public function createAdsGroup($accountId, $params, $query = []): array
    {
        return $this->callApi('POST', $this->buildParams('ad_accounts/' . $accountId . '/ad_groups', $query), $params);
    }

    public function updateAdsGroup($accountId, $params, $query = []): array
    {
        return $this->callApi('PATCH', $this->buildParams('ad_accounts/' . $accountId . '/ad_groups', $query), $params);
    }

    public function getAdsGroup($accountId, $id, $query = []): array
    {
        return $this->callApi('GET', $this->buildParams('ad_accounts/' . $accountId . '/ad_groups' . $id, $query));
    }

    public function getBoards($query = []): array
    {
        return $this->callApi('GET', $this->buildParams('boards', $query));
    }

    public function getBoard($id, $query = []): array
    {
        return $this->callApi('GET', $this->buildParams('boards/' . $id, $query));
    }

    public function createBoards($params, $query = []): array
    {
        return $this->callApi('POST', $this->buildParams('boards', $query), $params);
    }

    public function deleteBoards($id, $query = []): array
    {
        return $this->callApi('DELETE', $this->buildParams('boards/' . $id, $query));
    }

    public function updateBoards($id, $params, $query = []): array
    {
        return $this->callApi('PATCH', $this->buildParams('boards/' . $id, $query), $params);
    }

    public function getBoardSections($id, $query = []): array
    {
        return $this->callApi('GET', $this->buildParams('boards/' . $id . '/sections', $query));
    }

    public function createBoardSection($id, $params, $query = []): array
    {
        return $this->callApi('POST', $this->buildParams('boards/' . $id . '/sections', $query), $params);
    }

    public function updateBoardSection($boardId, $sectionId, $params, $query = []): array
    {
        return $this->callApi('PATCH', $this->buildParams('boards/' . $boardId . '/sections/' . $sectionId, $query), $params);
    }

    public function deleteBoardSection($boardId, $sectionId, $query = []): array
    {
        return $this->callApi('DELETE', $this->buildParams('boards/' . $boardId . '/sections/' . $sectionId, $query));
    }

    public function getCampaigns($accountId, $query = []): array
    {
        return $this->callApi('GET', $this->buildParams('ad_accounts/' . $accountId . '/campaigns', $query));
    }

    public function createCampaign($accountId, $params, $query = []): array
    {
        return $this->callApi('POST', $this->buildParams('ad_accounts/' . $accountId . '/campaigns', $query), $params);
    }

    public function updateCampaign($accountId, $params, $query = []): array
    {
        return $this->callApi('PATCH', $this->buildParams('ad_accounts/' . $accountId . '/campaigns', $query), $params);
    }

    public function getCampaign($accountId, $id, $query = []): array
    {
        return $this->callApi('GET', $this->buildParams('ad_accounts/' . $accountId . '/campaigns/' . $id, $query));
    }

    public function createMedia($params, $query = []): array
    {
        return $this->callApi('POST', $this->buildParams('media', $query), $params);
    }

    public function getUserAccount($query = []): array
    {
        return $this->callApi('GET', $this->buildParams('user_account', $query));
    }

    public function getPins($query = []): array
    {
        return $this->callApi('GET', $this->buildParams('pins', $query));
    }

    public function getPin($id, $query = []): array
    {
        return $this->callApi('GET', $this->buildParams('pins/' . $id, $query));
    }

    public function createPin($params, $query = []): array
    {
        return $this->callApi('POST', $this->buildParams('pins', $query), $params);
    }

    public function deletePin($id, $query = []): array
    {
        return $this->callApi('DELETE', $this->buildParams('pins/' . $id, $query));
    }

    public function updatePin($id, $params, $query = []): array
    {
        return $this->callApi('PATCH', $this->buildParams('pins/' . $id, $query), $params);
    }
}
