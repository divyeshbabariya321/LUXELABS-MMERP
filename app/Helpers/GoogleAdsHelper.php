<?php

namespace App\Helpers;

use Exception;
use App\GoogleAdsAccount;
use Illuminate\Support\Facades\Log;
use Google\Ads\GoogleAds\Lib\OAuth2TokenBuilder;
use Google\Ads\GoogleAds\Lib\V16\GoogleAdsClientBuilder;
use Google\Ads\GoogleAds\Lib\V16\GoogleAdsClientBuilder as GoogleAdsClientBuilderV13;

class GoogleAdsHelper
{
    public static function getGoogleAdsClient($account_id)
    {
        $account = GoogleAdsAccount::find($account_id);
        if (! is_null($account)) {
            try {
                $clientId     = $account->oauth2_client_id;
                $clientSecret = $account->oauth2_client_secret;
                $refreshToken = $account->oauth2_refresh_token;

                $developerToken  = $account->google_adwords_manager_account_developer_token;
                $loginCustomerId = $account->google_adwords_manager_account_customer_id;

                // Generate a refreshable OAuth2 credential for authentication.
                $oAuth2Credential = (new OAuth2TokenBuilder())
                    ->withClientId($clientId)
                    ->withClientSecret($clientSecret)
                    ->withRefreshToken($refreshToken)
                    ->build();

                $googleAdsClient = (new GoogleAdsClientBuilder())
                    ->withDeveloperToken($developerToken)
                    ->withLoginCustomerId($loginCustomerId)
                    ->withOAuth2Credential($oAuth2Credential)
                    ->build();

                return $googleAdsClient;
            } catch (Exception $e) {
                // Insert google ads log
                Log::info($e->getMessage());
                $input = [
                    'type'    => 'ERROR',
                    'module'  => 'Google Ads Client',
                    'message' => 'Create google ads client > ' . $e->getMessage(),
                ];
                insertGoogleAdsLog($input);

                return redirect()->to('/google-campaigns/ads-account')->with('actError', 'Something went to wrong. Please check logs.');
            }
        } else {
            return redirect()->to('/google-campaigns?account_id=null')->with('actError', 'Please fill proper detail in your account.');
        }
    }

    public static function getGoogleAdsClientV13($account_id)
    {
        $account = GoogleAdsAccount::find($account_id);
        if (! is_null($account)) {
            try {
                $clientId     = $account->oauth2_client_id;
                $clientSecret = $account->oauth2_client_secret;
                $refreshToken = $account->oauth2_refresh_token;

                $developerToken  = $account->google_adwords_manager_account_developer_token;
                $loginCustomerId = $account->google_adwords_manager_account_customer_id;

                // Generate a refreshable OAuth2 credential for authentication.
                $oAuth2Credential = (new OAuth2TokenBuilder())
                    ->withClientId($clientId)
                    ->withClientSecret($clientSecret)
                    ->withRefreshToken($refreshToken)
                    ->build();

                $googleAdsClient = (new GoogleAdsClientBuilderV13())
                    ->withDeveloperToken($developerToken)
                    ->withLoginCustomerId($loginCustomerId)
                    ->withOAuth2Credential($oAuth2Credential)
                    ->build();

                return $googleAdsClient;
            } catch (Exception $e) {
                // Insert google ads log
                $input = [
                    'type'    => 'ERROR',
                    'module'  => 'Google Ads Client',
                    'message' => 'Create google ads client > ' . $e->getMessage(),
                ];
                insertGoogleAdsLog($input);

                return redirect()->to('/google-campaigns/ads-account')->with('actError', 'Something went to wrong. Please check logs.');
            }
        } else {
            return redirect()->to('/google-campaigns?account_id=null')->with('actError', 'Please fill proper detail in your account.');
        }
    }
}
