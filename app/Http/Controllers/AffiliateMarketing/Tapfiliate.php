<?php

namespace App\Http\Controllers\AffiliateMarketing;


use App\LogRequest;

/**
 * Tapfiliate controller to manage and update data for tapfiliate provider.
 */
class Tapfiliate
{
    private $BASE_API_URL = 'https://api.tapfiliate.com/1.6/';

    private $API_KEY = '';

    private $PROVIDER_ACCOUNT;

    public function __construct($providerAccount)
    {
        $this->API_KEY          = $providerAccount->api_key;
        $this->PROVIDER_ACCOUNT = $providerAccount;
    }

    /**
     * Create affiliate group on provider
     *
     * @param mixed $data
     */
    public function createAffiliateGroup($data): array
    {
        return $this->callApi('POST', 'affiliate-groups/', $data);
    }

    /**
     * Update affiliate group on provider
     *
     * @param mixed $id
     * @param mixed $data
     */
    public function updateAffiliateGroup($id, $data): array
    {
        return $this->callApi('PATCH', 'affiliate-groups/' . $id . '/', $data);
    }

    public function getSyncData(): array
    {
        $programList         = $this->callApi('GET', 'programs/');
        $affiliatesGroupList = $this->callApi('GET', 'affiliate-groups/');
        $affiliatesList      = $this->callApi('GET', 'affiliates/');
        $commissionList      = $this->callApi('GET', 'commissions/');
        $conversionsList     = $this->callApi('GET', 'conversions/');
        $paymentsList        = $this->callApi('GET', 'payments/');

        return [
            'programList'         => $programList,
            'affiliatesGroupList' => $affiliatesGroupList,
            'affiliatesList'      => $affiliatesList,
            'commissionList'      => $commissionList,
            'conversionsList'     => $conversionsList,
            'paymentsList'        => $paymentsList,
        ];
    }

    /**
     * Get all the programmes from API
     */
    public function getProgrammes(): array
    {
        return $this->callApi('GET', 'programs/');
    }

    /**
     * Get all commissio types of the programmes from API
     *
     * @param mixed $program
     */
    public function getProgramCommissionType($program): array
    {
        return $this->callApi('GET', 'programs/' . $program . '/commission-types');
    }

    /**
     * Get all the commissions from API
     */
    public function getCommissions(): array
    {
        return $this->callApi('GET', 'commissions/');
    }

    /**
     * Get all the affiliates from API
     */
    public function getAffiliates(): array
    {
        return $this->callApi('GET', 'affiliates/');
    }

    /**
     * Create affiliates on provider
     *
     * @param mixed $data
     */
    public function createAffiliate($data): array
    {
        return $this->callApi('POST', 'affiliates/', $data);
    }

    /**
     * Delete affiliates on provider
     *
     * @param mixed $id
     */
    public function deleteAffiliate($id): array
    {
        return $this->callApi('DELETE', 'affiliates/' . $id . '/');
    }

    /**
     * Set affiliates group of affiliate on provider
     *
     * @param mixed $id
     * @param mixed $data
     */
    public function setAffiliateGroupForAffiliate($id, $data): array
    {
        return $this->callApi('PUT', 'affiliates/' . $id . '/group/', $data);
    }

    /**
     * Get affiliates payout methods from provider
     *
     * @param mixed $id
     */
    public function getAllAffiliatePayoutMethods($id): array
    {
        return $this->callApi('GET', 'affiliates/' . $id . '/payout-methods/');
    }

    /**
     * Set affiliates payout methods on provider
     *
     * @param mixed $id
     * @param mixed $payout_id
     */
    public function setAffiliatePayoutMethods($id, $payout_id): array
    {
        return $this->callApi('PUT', 'affiliates/' . $id . '/payout-methods/' . $payout_id . '/');
    }

    /**
     * Set affiliates programme
     *
     * @param mixed $id
     * @param mixed $data
     */
    public function addAffiliateToProgramme($id, $data): array
    {
        return $this->callApi('POST', 'programs/' . $id . '/affiliates/', $data);
    }

    /**
     * Approve, Disapprove affiliates in programme
     *
     * @param mixed $id
     * @param mixed $affiliateId
     * @param mixed $isApprove
     */
    public function approveDisapproveAffiliateToProgramme($id, $affiliateId, $isApprove): array
    {
        return $this->callApi($isApprove ? 'PUT' : 'DELETE', 'programs/' . $id . '/affiliates/' . $affiliateId . '/approved/');
    }

    /**
     * Update affiliate Commission on provider
     *
     * @param mixed $id
     * @param mixed $data
     */
    public function updateAffiliateCommission($id, $data): array
    {
        return $this->callApi('PATCH', 'commissions/' . $id . '/', $data);
    }

    /**
     * Update affiliate Commission approve or disapprove on provider
     *
     * @param $data
     * @param mixed $id
     * @param mixed $isApprove
     */
    public function updateAffiliateApproveDisapprove($id, $isApprove): array
    {
        return $this->callApi($isApprove ? 'PUT' : 'DELETE', 'commissions/' . $id . '/approved/');
    }

    /**
     * Create Affiliate Payment
     *
     * @param $id
     * @param mixed $data
     */
    public function createAffiliatePayment($data): array
    {
        return $this->callApi('POST', 'payments/', $data);
    }

    /**
     * Cancel Affiliate Payment
     *
     * @param $data
     * @param mixed $id
     */
    public function cancelAffiliatePayment($id): array
    {
        return $this->callApi('DELETE', 'payments/' . $id . '/');
    }

    /**
     * Get All Affiliate Payment
     *
     * @param $id
     * @param $data
     */
    public function getAffiliatePayment(): array
    {
        return $this->callApi('GET', 'payments/');
    }

    /**
     * Get Conversion
     *
     * @param $id
     * @param mixed $data
     */
    public function createConversions($data): array
    {
        return $this->callApi('POST', 'conversions/', $data);
    }

    /**
     * Update Conversion
     *
     * @param mixed $id
     * @param mixed $data
     */
    public function updateConversions($id, $data): array
    {
        return $this->callApi('PATCH', 'conversions/' . $id . '/', $data);
    }

    /**
     * Delete Conversion
     *
     * @param $data
     * @param mixed $id
     */
    public function deleteConversions($id): array
    {
        return $this->callApi('DELETE', 'conversions/' . $id . '/');
    }

    /**
     * Add commission to Conversion
     *
     * @param mixed $id
     * @param mixed $data
     */
    public function addCommissionConversions($id, $data): array
    {
        return $this->callApi('POST', 'conversions/' . $id . '/commissions/', $data);
    }

    /**
     * Get all Conversions
     *
     * @param $id
     * @param $data
     */
    public function getAllConversions(): array
    {
        return $this->callApi('GET', 'conversions/');
    }

    /**
     * Create customers
     *
     * @param $id
     * @param mixed $data
     */
    public function createCustomer($data): array
    {
        return $this->callApi('POST', 'customers/', $data);
    }

    /**
     * Delete customers
     *
     * @param $data
     * @param mixed $id
     */
    public function deleteCustomer($id): array
    {
        return $this->callApi('DELETE', 'customers/' . $id . '/');
    }

    /**
     * Cancel customers
     *
     * @param $data
     * @param mixed $id
     * @param mixed $isCancle
     */
    public function cancelCustomer($id, $isCancle): array
    {
        return $this->callApi($isCancle ? 'DELETE' : 'PUT', 'customers/' . $id . '/status/');
    }

    /**
     * Get all customers
     *
     * @param $id
     * @param $data
     */
    public function getAllCustomer(): array
    {
        return $this->callApi('GET', 'customers/');
    }

    /**
     * Common function to fetch data from API using CURL.
     *
     * @param mixed $method
     * @param mixed $url
     */
    public function callApi($method, $url, array $params = []): array
    {
        $curl      = curl_init();
        $startTime = date('Y-m-d H:i:s', LARAVEL_START);
        curl_setopt_array($curl, [
            CURLOPT_URL            => $this->BASE_API_URL . $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING       => '',
            CURLOPT_MAXREDIRS      => 10,
            CURLOPT_TIMEOUT        => 30,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION   => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST  => $method,
            CURLOPT_HTTPHEADER     => [
                'Content-Type: application/json',
                'X-Api-Key:' . $this->API_KEY,
            ],
        ]);

        if ($method == 'POST' || $method == 'PATCH' || $method == 'PUT') {
            curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($params));
        }
        $response = curl_exec($curl);
        $httpcode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        curl_close($curl);
        LogRequest::log($startTime, $this->BASE_API_URL . $url, curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($params)), json_encode($params), $response, $httpcode, Tapfiliate::class, 'callApi');
        $err = curl_error($curl);
        curl_close($curl);
        if ($err) {
            $message = 'Account :- ' . $this->PROVIDER_ACCOUNT->id . ', ';

            return ['status' => false, 'message' => $message . 'cURL Error #:' . $err, 'errors' => false];
        } else {
            $response = json_decode($response, true);
            if (is_array($response)) {
                if (array_key_exists('errors', $response)) {
                    $message = 'Account :- ' . $this->PROVIDER_ACCOUNT->id . ', ';

                    return ['status' => false, 'message' => $message . 'cURL Error #:' . serialize($response),
                        'errors'     => true, 'response' => $response];
                }

                return ['status' => true, 'message' => 'Data found', 'data' => $response];
            } else {
                if ($method == 'DELETE') {
                    return ['status' => true, 'message' => 'Data found', 'data' => $response];
                } else {
                    $message = 'Account :- ' . $this->PROVIDER_ACCOUNT->id . ', ';

                    return ['status' => false, 'message' => $message . 'cURL Error #:' . $response, 'errors' => false];
                }
            }
        }
    }
}
