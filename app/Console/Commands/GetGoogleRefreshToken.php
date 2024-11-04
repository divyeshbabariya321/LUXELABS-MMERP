<?php

namespace App\Console\Commands;

use Google\Auth\CredentialsLoader;
use Google\Auth\OAuth2;
use Illuminate\Console\Command;

class GetGoogleRefreshToken extends Command
{
    /**
     * @var string the OAuth2 scope for the AdWords API
     *
     * @see https://developers.google.com/adwords/api/docs/guides/authentication#scope
     */
    const ADWORDS_API_SCOPE = 'https://www.googleapis.com/auth/adwords';

    /**
     * @var string the OAuth2 scope for the Ad Manger API
     *
     * @see https://developers.google.com/ad-manager/docs/authentication#scope
     */
    const AD_MANAGER_API_SCOPE = 'https://www.googleapis.com/auth/dfp';

    /**
     * @var string the Google OAuth2 authorization URI for OAuth2 requests
     *
     * @see https://developers.google.com/identity/protocols/OAuth2InstalledApp#formingtheurl
     */
    const AUTHORIZATION_URI = 'https://accounts.google.com/o/oauth2/v2/auth';

    /**
     * @var string the redirect URI for OAuth2 installed application flows
     *
     * @see https://developers.google.com/identity/protocols/OAuth2InstalledApp#formingtheurl
     */
    const REDIRECT_URI = 'urn:ietf:wg:oauth:2.0:oob';

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'google:refresh-token';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Google Refresh Token';

    /**
     * Create a new command instance.
     */

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $PRODUCTS = [
            ['AdWords API', self::ADWORDS_API_SCOPE],
            ['Ad Manager API', self::AD_MANAGER_API_SCOPE],
            ['AdWords API and Ad Manager API', self::ADWORDS_API_SCOPE.' '
                .self::AD_MANAGER_API_SCOPE, ],
        ];

        $stdin = fopen('php://stdin', 'r');

        echo 'Enter your OAuth2 client ID here: ';
        $clientId = trim(fgets($stdin));

        echo 'Enter your OAuth2 client secret here: ';
        $clientSecret = trim(fgets($stdin));

        echo "Select the API you're using: [0] AdWords API [1] Ad Manager API "
            .'[2] Both'.PHP_EOL;
        $api = trim(fgets($stdin));

        while (! is_numeric($api) || (strval(intval($api)) != $api) || intval($api) < 0 || intval($api) > 2) {
            echo "Please enter a valid number for the API you're using: ".
                '[0] AdWords API [1] Ad Manager API [2] Both'.PHP_EOL;
            $api = trim(fgets($stdin));
        }
        $api = intval($api);

        if ($api === 2) {
            echo '[OPTIONAL] enter any additional OAuth2 scopes as a space '
                .'delimited string here (the AdWords API and Ad Manager API '
                .'scopes are already included): ';
        } else {
            printf(
                '[OPTIONAL] enter any additional OAuth2 scopes as a space '
                .'delimited string here (the %s scope is already included): ',
                $PRODUCTS[$api][0]
            );
        }
        $scopes = $PRODUCTS[$api][1].' '.trim(fgets($stdin));

        $oauth2 = new OAuth2(
            [
                'authorizationUri' => self::AUTHORIZATION_URI,
                'redirectUri' => self::REDIRECT_URI,
                'tokenCredentialUri' => CredentialsLoader::TOKEN_CREDENTIAL_URI,
                'clientId' => $clientId,
                'clientSecret' => $clientSecret,
                'scope' => $scopes,
            ]
        );

        printf(
            'Log into the Google account you use for %s and visit the following'
            ." URL:\n%s\n\n",
            $PRODUCTS[$api][0],
            $oauth2->buildFullAuthorizationUri()
        );
        echo 'After approving the application, enter the authorization code '
            .'here: ';
        $code = trim(fgets($stdin));
        fclose($stdin);
        echo "\n";

        $oauth2->setCode($code);
        $authToken = $oauth2->fetchAuthToken();

        printf("Your refresh token is: %s\n\n", $authToken['refresh_token']);
        printf(
            "Copy the following lines to your 'adsapi_php.ini' file:\n"
            ."clientId = \"%s\"\nclientSecret = \"%s\"\n"
            ."refreshToken = \"%s\"\n",
            $clientId,
            $clientSecret,
            $authToken['refresh_token']
        );
    }
}
