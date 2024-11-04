<?php

namespace App\Services\Facebook;

use JanuSoftware\Facebook\Facebook;
use JanuSoftware\Facebook\Exception\SDKException;
use JanuSoftware\Facebook\Exception\ResponseException;

class GraphLogin
{
    /*
     * Facebook Object
     */
    private $fb;

    /*
     * Facebook helper
     */
    private \JanuSoftware\Facebook\Helper\RedirectLoginHelper $fbHelper;

    /**
     * Instantiates a new Facebook class object, Facebook Helper
     *
     * @throws SDKException
     */
    public function __construct()
    {
        $this->fb = new Facebook(config('facebook.config'));

        $this->fbHelper = $this->fb->getRedirectLoginHelper();
    }

    /**
     * Get Login Url for your Application
     */
    public function getLoginUrl(array $permissions): string
    {
        return $this->fbHelper->getLoginUrl($_ENV['INSTAGRAM_CALLBACK_URL'], $permissions);
    }

    /**
     * returns an AccessToken.
     *
     *
     * @return null|false
     *
     * @throws SDKException
     */
    public function getAccessToken(): ?bool
    {
        try {
            $accessToken = $this->fbHelper->getAccessToken();
        } catch (ResponseException $e) {
            error_log('Graph returned an error: ' . $e->getMessage());

            return false;
        } catch (SDKException $e) {
            error_log('Facebook SDK returned an error: ' . $e->getMessage());

            return false;
        }

        if (! isset($accessToken)) {
            if ($this->fbHelper->getError()) {
                $error = 'Error: ' . $this->fbHelper->getError() . "\n";
                $error .= 'Error Code: ' . $this->fbHelper->getErrorCode() . "\n";
                $error .= 'Error Reason: ' . $this->fbHelper->getErrorReason() . "\n";
                $error .= 'Error Description: ' . $this->fbHelper->getErrorDescription() . "\n";

                error_log($error);
            } else {
                error_log('Bad request');
            }

            return false;
        }

        $oAuth2Client  = $this->fb->getOAuth2Client();
        $tokenMetadata = $oAuth2Client->debugToken($accessToken);
        $tokenMetadata->validateAppId($_ENV['FACEBOOK_APP_ID']);
        $tokenMetadata->validateExpiration();

        return $accessToken->getValue();
    }

    /**
     * returns User Info for Connected Instagram IDs
     *
     * @return array|false
     *
     * @throws SDKException
     */
    public function getUserInfo()
    {
        return ['access_token' => $this->getAccessToken()];
    }
}
