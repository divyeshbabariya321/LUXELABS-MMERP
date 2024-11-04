<?php

namespace App\Repositories;

use App\GTMatrixErrorLog;
use Exception;

class GooglePageSpeedRepository
{
    public function generateReport($gtmatrix)
    {
        $Api_key = config('settings.pagespeedonline_api_key');
        $curl    = curl_init();
        $url     = $gtmatrix->website_url;
        $parsed  = parse_url($url);
        if (empty($parsed['scheme'])) {
            $url = 'https://' . ltrim(strtolower($url), '/');
        }

        curl_setopt_array($curl, [
            CURLOPT_URL            => 'https://pagespeedonline.googleapis.com/pagespeedonline/v5/runPagespeed?url=' . $url . '&key=' . $Api_key,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING       => '',
            CURLOPT_MAXREDIRS      => 10,
            CURLOPT_TIMEOUT        => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION   => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST  => 'GET',
            CURLOPT_HTTPHEADER     => [
                'Accept: application/json',
            ],
        ]);

        $response = curl_exec($curl);
        $err      = curl_error($curl);
        if ($err) {
            $this->GTMatrixError($gtmatrix->account_id, 'Google page speed report', 'Generating Report', 'Generating Report Error ' . $url . ' Error' . $err);
        }

        curl_close($curl);

        $JsonfileName = '/uploads/speed-insight/' . $gtmatrix->test_id . '_pagespeedInsight.json';
        $Jsonfile     = public_path() . $JsonfileName;
        file_put_contents($Jsonfile, $response);
        $gtmatrix->pagespeed_insight_json = $JsonfileName;
        $gtmatrix->save();
    }

    public function GTMatrixError($store_viewGTM_id, $erro_type, $error_title, $error = '')
    {
        try {
            $GTError                   = new GTMatrixErrorLog();
            $GTError->store_viewGTM_id = $store_viewGTM_id;
            $GTError->error_type       = $erro_type;
            $GTError->error_title      = $error_title;
            $GTError->error            = $error;
            $GTError->save();
        } catch (Exception $e) {
            $GTError                   = new GTMatrixErrorLog();
            $GTError->store_viewGTM_id = $store_viewGTM_id;
            $GTError->error_type       = $erro_type;
            $GTError->error            = $e->getMessage();
            $GTError->save();
        }
    }
}
