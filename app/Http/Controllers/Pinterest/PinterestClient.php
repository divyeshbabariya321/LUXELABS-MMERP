<?php

namespace App\Http\Controllers\Pinterest;

use App\LogRequest;
use App\PinterestBusinessAccountMails;
use Exception;

class PinterestClient
{
    const USER_ACCOUNT_READ = 'user_accounts:read';

    const USER_ACCOUNT_WRITE = 'user_accounts:write';

    const PINS_READ = 'pins:read';

    const PINS_WRITE = 'pins:write';

    const PINS_READ_SECRET = 'pins:read_secret';

    const PINS_WRITE_SECRET = 'pins:write_secret';

    const BOARDS_READ = 'boards:read';

    const BOARDS_WRITE = 'boards:write';

    const BOARDS_READ_SECRET = 'boards:read_secret';

    const BOARDS_WRITE_SECRET = 'boards:write_secret';

    const ADS_READ = 'ads:read';

    const ADS_WRITE = 'ads:write';

    private $BASE_API = 'https://api-sandbox.pinterest.com/v5/';

    private $BASE_AUTH_API_URL = 'https://www.pinterest.com/oauth/';

    private $accessToken = '';

    private $scopes = [
        self::ADS_READ, self::ADS_WRITE,
        self::BOARDS_READ, self::BOARDS_WRITE,
        self::PINS_READ, self::PINS_WRITE,
        self::USER_ACCOUNT_READ,
    ];

    public function __construct(private $clientId = null, private $clientSecret = null, private $accountId = null)
    {
    }

    public function buildParams($url, $query): string
    {
        $url .= '?';
        foreach ($query as $key => $item) {
            $url .= $key . '=' . $item;
        }

        return $url;
    }

    public function getBASEAPI(): string
    {
        return $this->BASE_API;
    }

    public function getBASEAUTHAPIURL(): string
    {
        return $this->BASE_AUTH_API_URL;
    }

    /**
     * @return string[]
     */
    public function getScopes(): array
    {
        return $this->scopes;
    }

    public function getAccessToken(): string
    {
        return $this->accessToken;
    }

    public function setAccessToken(string $accessToken): void
    {
        $this->accessToken = $accessToken;
    }

    /**
     * @return mixed|null
     */
    public function getClientId()
    {
        return $this->clientId;
    }

    /**
     * @param mixed|null $clientId
     */
    public function setClientId($clientId): void
    {
        $this->clientId = $clientId;
    }

    /**
     * @return mixed|null
     */
    public function getClientSecret()
    {
        return $this->clientSecret;
    }

    /**
     * @param mixed|null $clientSecret
     */
    public function setClientSecret($clientSecret): void
    {
        $this->clientSecret = $clientSecret;
    }

    /**
     * @return mixed|null
     */
    public function getAccountId()
    {
        return $this->accountId;
    }

    /**
     * @param mixed|null $accountId
     */
    public function setAccountId($accountId): void
    {
        $this->accountId = $accountId;
    }

    /**
     * @param mixed $accessToken
     *
     * @throws Exception
     */
    public function updateAccessToken($accessToken)
    {
        $accessAccount = PinterestBusinessAccountMails::where('pinterest_access_token', $accessToken)->first();
        $expireDate    = (strtotime($accessAccount->updated_at) + $accessAccount->expires_in);
        if (strtotime(date('d-m-Y h:i:s')) > $expireDate) {
            $response = $this->validateAccessTokenAndRefreshToken(['refresh_token' => $accessAccount->pinterest_refresh_token], true);
            if ($response['status']) {
                $accessAccount->pinterest_access_token = $response['data']['access_token'];
                $accessAccount->expires_in             = $response['data']['expires_in'];
                $accessAccount->save();
                $this->setAccessToken($accessAccount->pinterest_access_token);
            } else {
                throw new Exception('Unable to set access token');
            }
        } else {
            $this->setAccessToken($accessAccount->pinterest_access_token);
        }
    }

    /**
     * Get authorization URL
     */
    public function getAuthURL(): string
    {
        $params = [
            'client_id'     => $this->getClientId(),
            'redirect_uri'  => str_replace('http://', 'https://', route('pinterest.accounts.connect.login')),
            'response_type' => 'code',
            'scope'         => implode(',', $this->getScopes()),
            'state'         => base64_encode($this->getAccountId()),
        ];
        $url = $this->getBASEAUTHAPIURL() . '?';
        foreach ($params as $key => $param) {
            $url .= $key . '=' . $param . '&';
        }

        return $url;
    }

    public function getSupportedCountries(): array
    {
        return [
            'AI' => 'Anguilla',
            'AL' => 'Albania',
            'AM' => 'Armenia',
            'AO' => 'Angola',
            'AQ' => 'Antarctica',
            'AR' => 'Argentina',
            'AS' => 'American Samoa',
            'AT' => 'Austria',
            'AU' => 'Australia',
            'AW' => 'Aruba',
            'AX' => 'Aland Islands',
            'AZ' => 'Azerbaijan',
            'BA' => 'Bosnia And Herzegovina',
            'BB' => 'Barbados',
            'BD' => 'Bangladesh',
            'BE' => 'Belgium',
            'BF' => 'Burkina Faso',
            'BG' => 'Bulgaria',
            'BH' => 'Bahrain',
            'BI' => 'Burundi',
            'BJ' => 'Benin',
            'BL' => 'Saint Barthelemy',
            'BM' => 'Bermuda',
            'BN' => 'Brunei Darussalam',
            'BO' => 'Bolivia',
            'BR' => 'Brazil',
            'BS' => 'Bahamas',
            'BT' => 'Bhutan',
            'BV' => 'Bouvet Island',
            'BW' => 'Botswana',
            'BY' => 'Belarus',
            'BZ' => 'Belize',
            'CA' => 'Canada',
            'CC' => 'Cocos (Keeling) Islands',
            'CD' => 'Congo, Democratic Republic',
            'CF' => 'Central African Republic',
            'CG' => 'Congo',
            'CH' => 'Switzerland',
            'CI' => "Cote D'Ivoire",
            'CK' => 'Cook Islands',
            'CL' => 'Chile',
            'CM' => 'Cameroon',
            'CN' => 'China',
            'CO' => 'Colombia',
            'CR' => 'Costa Rica',
            'CU' => 'Cuba',
            'CV' => 'Cape Verde',
            'CX' => 'Christmas Island',
            'CY' => 'Cyprus',
            'CZ' => 'Czech Republic',
            'DE' => 'Germany',
            'DJ' => 'Djibouti',
            'DK' => 'Denmark',
            'DM' => 'Dominica',
            'DO' => 'Dominican Republic',
            'DZ' => 'Algeria',
            'EC' => 'Ecuador',
            'EE' => 'Estonia',
            'EG' => 'Egypt',
            'EH' => 'Western Sahara',
            'ER' => 'Eritrea',
            'ES' => 'Spain',
            'ET' => 'Ethiopia',
            'FI' => 'Finland',
            'FJ' => 'Fiji',
            'FK' => 'Falkland Islands (Malvinas)',
            'FM' => 'Micronesia, Federated States Of',
            'FO' => 'Faroe Islands',
            'FR' => 'France',
            'GA' => 'Gabon',
            'GB' => 'United Kingdom',
            'GD' => 'Grenada',
            'GE' => 'Georgia',
            'GF' => 'French Guiana',
            'GG' => 'Guernsey',
            'GH' => 'Ghana',
            'GI' => 'Gibraltar',
            'GL' => 'Greenland',
            'GM' => 'Gambia',
            'GN' => 'Guinea',
            'GP' => 'Guadeloupe',
            'GQ' => 'Equatorial Guinea',
            'GR' => 'Greece',
            'GS' => 'South Georgia And Sandwich Isl.',
            'GT' => 'Guatemala',
            'GU' => 'Guam',
            'GW' => 'Guinea-Bissau',
            'GY' => 'Guyana',
            'HK' => 'Hong Kong',
            'HM' => 'Heard Island & Mcdonald Islands',
            'HN' => 'Honduras',
            'HR' => 'Croatia',
            'HT' => 'Haiti',
            'HU' => 'Hungary',
            'ID' => 'Indonesia',
            'IE' => 'Ireland',
            'IL' => 'Israel',
            'IM' => 'Isle Of Man',
            'IN' => 'India',
            'IO' => 'British Indian Ocean Territory',
            'IQ' => 'Iraq',
            'IR' => 'Iran, Islamic Republic Of',
            'IS' => 'Iceland',
            'IT' => 'Italy',
            'JE' => 'Jersey',
            'JM' => 'Jamaica',
            'JO' => 'Jordan',
            'JP' => 'Japan',
            'KE' => 'Kenya',
            'KG' => 'Kyrgyzstan',
            'KH' => 'Cambodia',
            'KI' => 'Kiribati',
            'KM' => 'Comoros',
            'KN' => 'Saint Kitts And Nevis',
            'KR' => 'Korea',
            'KW' => 'Kuwait',
            'KY' => 'Cayman Islands',
            'KZ' => 'Kazakhstan',
            'LA' => "Lao People's Democratic Republic",
            'LB' => 'Lebanon',
            'LC' => 'Saint Lucia',
            'LI' => 'Liechtenstein',
            'LK' => 'Sri Lanka',
            'LR' => 'Liberia',
            'LS' => 'Lesotho',
            'LT' => 'Lithuania',
            'LU' => 'Luxembourg',
            'LV' => 'Latvia',
            'LY' => 'Libyan Arab Jamahiriya',
            'MA' => 'Morocco',
            'MC' => 'Monaco',
            'MD' => 'Moldova',
            'ME' => 'Montenegro',
            'MF' => 'Saint Martin',
            'MG' => 'Madagascar',
            'MH' => 'Marshall Islands',
            'MK' => 'Macedonia',
            'ML' => 'Mali',
            'MM' => 'Myanmar',
            'MN' => 'Mongolia',
            'MO' => 'Macao',
            'MP' => 'Northern Mariana Islands',
            'MQ' => 'Martinique',
            'MR' => 'Mauritania',
            'MS' => 'Montserrat',
            'MT' => 'Malta',
            'MU' => 'Mauritius',
            'MV' => 'Maldives',
            'MW' => 'Malawi',
            'MX' => 'Mexico',
            'MY' => 'Malaysia',
            'MZ' => 'Mozambique',
            'NA' => 'Namibia',
            'NC' => 'New Caledonia',
            'NE' => 'Niger',
            'NF' => 'Norfolk Island',
            'NG' => 'Nigeria',
            'NI' => 'Nicaragua',
            'NL' => 'Netherlands',
            'NO' => 'Norway',
            'NP' => 'Nepal',
            'NR' => 'Nauru',
            'NU' => 'Niue',
            'NZ' => 'New Zealand',
            'OM' => 'Oman',
            'PA' => 'Panama',
            'PE' => 'Peru',
            'PF' => 'French Polynesia',
            'PG' => 'Papua New Guinea',
            'PH' => 'Philippines',
            'PK' => 'Pakistan',
            'PL' => 'Poland',
            'PM' => 'Saint Pierre And Miquelon',
            'PN' => 'Pitcairn',
            'PR' => 'Puerto Rico',
            'PS' => 'Palestinian Territory, Occupied',
            'PT' => 'Portugal',
            'PW' => 'Palau',
            'PY' => 'Paraguay',
            'QA' => 'Qatar',
            'RE' => 'Reunion',
            'RO' => 'Romania',
            'RS' => 'Serbia',
            'RU' => 'Russian Federation',
            'RW' => 'Rwanda',
            'SA' => 'Saudi Arabia',
            'SB' => 'Solomon Islands',
            'SC' => 'Seychelles',
            'SD' => 'Sudan',
            'SE' => 'Sweden',
            'SG' => 'Singapore',
            'SH' => 'Saint Helena',
            'SI' => 'Slovenia',
            'SJ' => 'Svalbard And Jan Mayen',
            'SK' => 'Slovakia',
            'SL' => 'Sierra Leone',
            'SM' => 'San Marino',
            'SN' => 'Senegal',
            'SO' => 'Somalia',
            'SR' => 'Suriname',
            'ST' => 'Sao Tome And Principe',
            'SV' => 'El Salvador',
            'SY' => 'Syrian Arab Republic',
            'SZ' => 'Swaziland',
            'TC' => 'Turks And Caicos Islands',
            'TD' => 'Chad',
            'TF' => 'French Southern Territories',
            'TG' => 'Togo',
            'TH' => 'Thailand',
            'TJ' => 'Tajikistan',
            'TK' => 'Tokelau',
            'TL' => 'Timor-Leste',
            'TM' => 'Turkmenistan',
            'TN' => 'Tunisia',
            'TO' => 'Tonga',
            'TR' => 'Turkey',
            'TT' => 'Trinidad And Tobago',
            'TV' => 'Tuvalu',
            'TW' => 'Taiwan',
            'TZ' => 'Tanzania',
            'UA' => 'Ukraine',
            'UG' => 'Uganda',
            'UM' => 'United States Outlying Islands',
            'US' => 'United States',
            'UY' => 'Uruguay',
            'UZ' => 'Uzbekistan',
            'VA' => 'Holy See (Vatican City State)',
            'VC' => 'Saint Vincent And Grenadines',
            'VE' => 'Venezuela',
            'VG' => 'Virgin Islands, British',
            'VI' => 'Virgin Islands, U.S.',
            'VN' => 'Vietnam',
            'VU' => 'Vanuatu',
            'WF' => 'Wallis And Futuna',
            'WS' => 'Samoa',
            'YE' => 'Yemen',
            'YT' => 'Mayotte',
            'ZA' => 'South Africa',
            'ZM' => 'Zambia',
            'ZW' => 'Zimbabwe',
        ];
    }

    /**
     * Validate and get access token from given code
     *
     * @param mixed $params
     * @param mixed $isRefresh
     */
    public function validateAccessTokenAndRefreshToken($params, $isRefresh = false): array
    {
        if ($isRefresh) {
            $postFields = 'grant_type=refresh_token&refresh_token=' . $params['refresh_token'] . '&scope=' . implode(',', $this->getScopes());
        } else {
            $postFields = 'grant_type=authorization_code&code=' . $params['code'] . '&redirect_uri=' . str_replace('http://', 'https://', route('pinterest.accounts.connect.login'));
        }
        $startTime = date('Y-m-d H:i:s', LARAVEL_START);
        $curl      = curl_init();
        curl_setopt_array($curl, [
            CURLOPT_URL            => $this->getBASEAPI() . 'oauth/token',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING       => '',
            CURLOPT_MAXREDIRS      => 10,
            CURLOPT_TIMEOUT        => 30,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION   => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST  => 'POST',
            CURLOPT_POSTFIELDS     => $postFields,
            CURLOPT_HTTPHEADER     => [
                'Content-Type: application/x-www-form-urlencoded',
                'Authorization: Basic ' . base64_encode($this->getClientId() . ':' . $this->getClientSecret()),
            ],
        ]);
        $response = curl_exec($curl);
        $httpcode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        LogRequest::log($startTime, $this->getBASEAPI() . 'oauth/token', 'POST', json_encode($postFields), json_decode($response), $httpcode, PinterestClient::class, 'validateAccessTokenAndRefreshToken');
        $err = curl_error($curl);
        curl_close($curl);
        if ($err) {
            $message = 'Account :- ' . $this->getAccountId() . ', ';

            return ['status' => false, 'message' => $message . 'cURL Error #:' . $err];
        } else {
            $response = json_decode($response, true);

            return ['status' => true, 'message' => 'Data found', 'data' => $response];
        }
    }

    /**
     * Common function to fetch data from API using CURL.
     *
     * @param mixed $method
     * @param mixed $url
     */
    public function callApi($method, $url, array $params = []): array
    {
        $startTime = date('Y-m-d H:i:s', LARAVEL_START);
        $curl      = curl_init();
        curl_setopt_array($curl, [
            CURLOPT_URL            => $this->getBASEAPI() . $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING       => '',
            CURLOPT_MAXREDIRS      => 10,
            CURLOPT_TIMEOUT        => 30,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION   => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST  => $method,
            CURLINFO_HEADER_OUT    => true,
            CURLOPT_HTTPHEADER     => [
                'Content-Type: application/json',
                'Authorization: Bearer ' . $this->getAccessToken(),
            ],
        ]);

        if ($method == 'POST' || $method == 'PATCH' || $method == 'PUT') {
            curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($params));
        }

        $response = curl_exec($curl);
        $err      = curl_error($curl);
        $httpcode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        LogRequest::log($startTime, $url, $method, json_encode($params), json_decode($response), $httpcode, PinterestClient::class, 'callApi');
        curl_close($curl);
        if ($err) {
            $message = 'Account :- ' . $this->getAccountId() . ', ';

            return ['status' => false, 'message' => $message . 'cURL Error #:' . $err];
        } else {
            $response = json_decode($response, true);
            if (is_array($response)) {
                if (isset($response['code'])) {
                    return ['status' => false, 'message' => $response['message']];
                }
                if (isset($response['items'])) {
                    if (isset($response['items'][0]['exceptions'][0])) {
                        if (isset($response['items'][0]['exceptions'][0]['message'])) {
                            return ['status' => false, 'message' => $response['items'][0]['exceptions'][0]['message']];
                        }
                    }
                }

                return ['status' => true, 'message' => 'Data found', 'data' => $response];
            } else {
                if ($method == 'DELETE') {
                    return ['status' => true, 'message' => 'Data found', 'data' => $response];
                } else {
                    $message = 'Account :- ' . $this->getAccountId() . ', ';

                    return ['status' => false, 'message' => $message . 'cURL Error #:' . $response];
                }
            }
        }
    }
}
