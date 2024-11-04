<?php
use App\GoogleAnalyticsUser;
use App\GoogleAnalyticsPlatformDevice;
use App\GoogleAnalyticsPageTracking;
use App\GoogleAnalyticsGeoNetwork;
use App\GoogleAnalyticsAudience;
use App\GoogleAnalyticData;


// Load the Google API PHP Client Library.
require_once __DIR__.'/../../vendor/autoload.php';
$data = [];
$analytics = initializeAnalytics();

if (! empty($analytics)) {
    //
}

/**
 * Initializes an Analytics Reporting API V4 service object.
 *
 * @return string authorized Analytics Reporting API V4 service object.
 */
function initializeAnalytics()
{
    // Use the developers console and download your service account
    // credentials in JSON format. Place them in this directory or
    // change the key file location if necessary.
    $KEY_FILE_LOCATION = storage_path('app/analytics/sololuxu-7674c35e7be5.json');

    return '';
}

/**
 * Queries the Analytics Reporting API V4.
 *
 * @param service An authorized Analytics Reporting API V4 service object.
 * @param  mixed  $analytics
 * @param  mixed  $request
 * @return The Analytics Reporting API V4 response.
 */
function getReportRequest($analytics, $request)
{
    // Replace with your view ID, for example XXXX.
    if (isset($request['view_id'])) {
        $view_id = (string) $request['view_id'];
    } else {
        $view_id = config('env.ANALYTICS_VIEW_ID');
    }

    if (! empty($request)) {
        $analytics = '';
        if (isset($request['google_service_account_json']) && $request['google_service_account_json'] != '') {
            $websiteKeyFile = base_path('resources/analytics_files/'.$request['google_service_account_json']);
        } else {
            $websiteKeyFile = storage_path('app/analytics/sololuxu-7674c35e7be5.json');
        }

        if (file_exists($websiteKeyFile)) {
            $client = new Google_Client;
            $client->setAuthConfig($websiteKeyFile);
            $client->setScopes(['https://www.googleapis.com/auth/analytics.readonly']);
            $analytics = new Google_Service_AnalyticsReporting($client);
        }
    }

    // Create the DateRange object.
    $dateRange = new Google_Service_AnalyticsReporting_DateRange;
    $dateRange->setStartDate('today');
    $dateRange->setEndDate('today');

    // Create the ReportRequest object.
    $request = new Google_Service_AnalyticsReporting_ReportRequest;
    $request->setViewId($view_id);
    $request->setDateRanges($dateRange);

    return ['requestObj' => $request, 'analyticsObj' => $analytics];
}

function getDimensionWiseData($analytics, $request, $GaDimension)
{
    // Create the Dimensions object.
    $dimension = new Google_Service_AnalyticsReporting_Dimension;
    $dimension->setName($GaDimension);

    $request->setDimensions([$dimension]);

    $request->setDimensions([$dimension]);

    $body = new Google_Service_AnalyticsReporting_GetReportsRequest;
    $body->setReportRequests([$request]);

    return $analytics->reports->batchGet($body);
}

function getPageTrackingData($analytics, $request)
{
    // Create the Dimensions object.
    $dimension = new Google_Service_AnalyticsReporting_Dimension;
    $dimension->setName('ga:pagePath');

    $request->setDimensions([$dimension]);
    $request->setDimensions([$dimension]);

    // Create the Metrics object.
    $metric = new Google_Service_AnalyticsReporting_Metric;
    $metric->setExpression('ga:avgTimeOnPage');
    $metric->setAlias('avgTimeOnPage');

    $uniquePageviews = new Google_Service_AnalyticsReporting_Metric;
    $uniquePageviews->setExpression('ga:uniquePageviews');
    $uniquePageviews->setAlias('uniquePageviews');

    $pageviews = new Google_Service_AnalyticsReporting_Metric;
    $pageviews->setExpression('ga:pageviews');
    $pageviews->setAlias('pageviews');

    $exitRate = new Google_Service_AnalyticsReporting_Metric;
    $exitRate->setExpression('ga:exitRate');
    $exitRate->setAlias('exitRate');

    $entrances = new Google_Service_AnalyticsReporting_Metric;
    $entrances->setExpression('ga:entrances');
    $entrances->setAlias('entrances');

    $entranceRate = new Google_Service_AnalyticsReporting_Metric;
    $entranceRate->setExpression('ga:entranceRate');
    $entranceRate->setAlias('entranceRate');

    $request->setMetrics([$metric, $uniquePageviews, $pageviews, $exitRate, $entrances, $entranceRate]);

    $body = new Google_Service_AnalyticsReporting_GetReportsRequest;
    $body->setReportRequests([$request]);

    return $analytics->reports->batchGet($body);
}

function getPlatformDeviceData($analytics, $request)
{
    // Create the Dimensions object.
    $dimension = new Google_Service_AnalyticsReporting_Dimension;
    $dimension->setName('ga:browser');

    $operatingSystem = new Google_Service_AnalyticsReporting_Dimension;
    $operatingSystem->setName('ga:operatingSystem');

    $request->setDimensions([$dimension, $operatingSystem]);

    // Create the Metrics object.

    $body = new Google_Service_AnalyticsReporting_GetReportsRequest;
    $body->setReportRequests([$request]);

    return $analytics->reports->batchGet($body);
}

function getGeoNetworkData($analytics, $request)
{
    // Create the Dimensions object.
    $dimension = new Google_Service_AnalyticsReporting_Dimension;
    $dimension->setName('ga:country');

    $countryIsoCode = new Google_Service_AnalyticsReporting_Dimension;
    $countryIsoCode->setName('ga:countryIsoCode');

    $request->setDimensions([$dimension, $countryIsoCode]);

    // Create the Metrics object.

    $body = new Google_Service_AnalyticsReporting_GetReportsRequest;
    $body->setReportRequests([$request]);

    return $analytics->reports->batchGet($body);
}

function getUsersData($analytics, $request)
{
    // Create the Dimensions object.
    $dimension = new Google_Service_AnalyticsReporting_Dimension;
    $dimension->setName('ga:userType');

    $request->setDimensions([$dimension]);

    $body = new Google_Service_AnalyticsReporting_GetReportsRequest;
    $body->setReportRequests([$request]);

    return $analytics->reports->batchGet($body);
}

function getAudiencesData($analytics, $request)
{
    // Create the Dimensions object.
    $dimension = new Google_Service_AnalyticsReporting_Dimension;
    $dimension->setName('ga:userAgeBracket');

    $userGender = new Google_Service_AnalyticsReporting_Dimension;
    $userGender->setName('ga:userGender');

    $request->setDimensions([$dimension, $userGender]);

    $body = new Google_Service_AnalyticsReporting_GetReportsRequest;
    $body->setReportRequests([$request]);

    return $analytics->reports->batchGet($body);
}

/**
 * Parses and prints the Analytics Reporting API V4 response.
 *
 * @param An Analytics Reporting API V4 response.
 * @param  mixed  $reports
 * @param  mixed  $websiteAnalyticsId
 */
function printResults($reports, $websiteAnalyticsId)
{
    $data = [];

    foreach ($reports as $report) {
        $header           = $report->getColumnHeader();

        $dimensionHeaders = $header->getDimensions();
        $metricHeaders = $header->getMetricHeader()->getMetricHeaderEntries();
        $rows = $report->getData()->getRows();

        foreach ($rows as $rowIndex => $row) {
            $dimensions = $row->getDimensions();
            $metrics = $row->getMetrics();

            // Process dimensions
            foreach ($dimensionHeaders as $i => $dimensionHeader) {
                if (isset($dimensions[$i])) {
                    $data[$rowIndex]['dimensions'] = str_replace('ga:', '', $dimensionHeader);
                    $data[$rowIndex]['dimensions_name'] = $dimensions[$i];
                    $data[$rowIndex]['website_analytics_id'] = $websiteAnalyticsId;
                }
            }

            // Process metrics
            foreach ($metrics as $metric) {
                $values = $metric->getValues();
                foreach ($values as $k => $value) {
                    $data[$rowIndex]['dimensions_value'] = $value;
                }
            }
        }
    }

    return !empty($data) ? $data : null;
}


function printPageTrackingResults($reports, $websiteAnalyticsId)
{
    $data = []; 

    foreach ($reports as $report) {
        $rows = $report->getData()->getRows(); 

        foreach ($rows as $key => $value) {
            $data[$key]['website_analytics_id'] = $websiteAnalyticsId;
            $data[$key]['page']                 = $value['dimensions'][0];

            foreach ($value['metrics'] as $m_key => $m_value) {
                $data[$key]['avg_time_page'] = $m_value['values'][0];
                $data[$key]['unique_page_views'] = $m_value['values'][1];
                $data[$key]['page_views'] = $m_value['values'][2];
                $data[$key]['exit_rate'] = $m_value['values'][3];
                $data[$key]['entrances'] = $m_value['values'][4];
                $data[$key]['entrance_rate'] = $m_value['values'][5];
            }
            \App\GoogleAnalyticsPageTracking::insert($data[$key]); 
        }
    }

    return true; 
}


function printPlatformDeviceResults($reports, $websiteAnalyticsId)
{
    $data = []; 

    foreach ($reports as $report) {
        $rows = $report->getData()->getRows(); 

        foreach ($rows as $key => $value) {
            $data[$key]['website_analytics_id'] = $websiteAnalyticsId;
            $data[$key]['browser']              = $value['dimensions'][0];
            $data[$key]['os']                   = $value['dimensions'][1];

            foreach ($value['metrics'] as $m_key => $m_value) {
                $data[$key]['session'] = $m_value['values'][0];
            }
            \App\GoogleAnalyticsPlatformDevice::insert($data[$key]);
        }
    }

    return true;
}


function printGeoNetworkResults($reports, $websiteAnalyticsId)
{
    $data = []; 

    foreach ($reports as $report) {
        $rows = $report->getData()->getRows(); 

        foreach ($rows as $key => $value) {
            $data[$key]['website_analytics_id'] = $websiteAnalyticsId;
            $data[$key]['country']              = $value['dimensions'][0];
            $data[$key]['iso_code']             = $value['dimensions'][1];

            foreach ($value['metrics'] as $m_key => $m_value) {
                $data[$key]['session'] = $m_value['values'][0];
            }
        }

        \App\GoogleAnalyticsGeoNetwork::insert($data); 
    }

    return true; 
}

function printUsersResults($reports, $websiteAnalyticsId)
{
    $data = []; 

    foreach ($reports as $report) {
        $rows = $report->getData()->getRows(); 

        foreach ($rows as $key => $value) {
            $data[$key]['website_analytics_id'] = $websiteAnalyticsId;
            $data[$key]['user_type']            = $value['dimensions'][0];

            foreach ($value['metrics'] as $m_key => $m_value) {
                $data[$key]['session'] = $m_value['values'][0];
            }
        }

        \App\GoogleAnalyticsUser::insert($data);
    }

    return true; 
}


function printAudienceResults($reports, $websiteAnalyticsId)
{
    $data = [];

    foreach ($reports as $report) {
        $rows = $report->getData()->getRows();

        foreach ($rows as $key => $value) {
            $data[$key]['website_analytics_id'] = $websiteAnalyticsId;
            $data[$key]['age']                  = $value['dimensions'][0];
            $data[$key]['gender']               = $value['dimensions'][1];

            foreach ($value['metrics'] as $m_key => $m_value) {
                $data[$key]['session'] = $m_value['values'][0];
            }
        }

        \App\GoogleAnalyticsAudience::insert($data);
    }

    return true;
}


function getGoogleAnalyticData($analytics, $request)
{
    // Create the Dimensions object.
    $dimension = new Google_Service_AnalyticsReporting_Dimension;
    $dimension->setName('ga:userType');

    $pagePath = new Google_Service_AnalyticsReporting_Dimension;
    $pagePath->setName('ga:pagePath');

    $browser = new Google_Service_AnalyticsReporting_Dimension;
    $browser->setName('ga:browser');

    $operatingSystem = new Google_Service_AnalyticsReporting_Dimension;
    $operatingSystem->setName('ga:operatingSystem');

    $country = new Google_Service_AnalyticsReporting_Dimension;
    $country->setName('ga:country');

    $countryIsoCode = new Google_Service_AnalyticsReporting_Dimension;
    $countryIsoCode->setName('ga:countryIsoCode');

    $userAge = new Google_Service_AnalyticsReporting_Dimension;
    $userAge->setName('ga:userAgeBracket');

    $userGender = new Google_Service_AnalyticsReporting_Dimension;
    $userGender->setName('ga:userGender');

    $deviceCategory = new Google_Service_AnalyticsReporting_Dimension;
    $deviceCategory->setName('ga:deviceCategory');

    $request->setDimensions([$dimension, $pagePath, $browser, $operatingSystem, $country, $countryIsoCode, $deviceCategory]);

    // Create the Metrics object.
    $metric = new Google_Service_AnalyticsReporting_Metric;
    $metric->setExpression('ga:avgTimeOnPage');
    $metric->setAlias('avgTimeOnPage');

    $uniquePageviews = new Google_Service_AnalyticsReporting_Metric;
    $uniquePageviews->setExpression('ga:uniquePageviews');
    $uniquePageviews->setAlias('uniquePageviews');

    $pageviews = new Google_Service_AnalyticsReporting_Metric;
    $pageviews->setExpression('ga:pageviews');
    $pageviews->setAlias('pageviews');

    $exitRate = new Google_Service_AnalyticsReporting_Metric;
    $exitRate->setExpression('ga:exitRate');
    $exitRate->setAlias('exitRate');

    $entrances = new Google_Service_AnalyticsReporting_Metric;
    $entrances->setExpression('ga:entrances');
    $entrances->setAlias('entrances');

    $entranceRate = new Google_Service_AnalyticsReporting_Metric;
    $entranceRate->setExpression('ga:entranceRate');
    $entranceRate->setAlias('entranceRate');

    $session = new Google_Service_AnalyticsReporting_Metric;
    $session->setExpression('ga:sessions');
    $session->setAlias('session');

    $request->setMetrics([$metric, $uniquePageviews, $pageviews, $exitRate, $entrances, $entranceRate, $session]);

    $body = new Google_Service_AnalyticsReporting_GetReportsRequest;
    $body->setReportRequests([$request]);

    return $analytics->reports->batchGet($body);
}

function printGoogleAnalyticResults($reports, $websiteAnalyticsId)
{
    $data = [];

    foreach ($reports as $report) {
        $rows = $report->getData()->getRows();

        foreach ($rows as $key => $value) {
            $data[$key]['website_analytics_id'] = $websiteAnalyticsId;
            $data[$key]['user_type']            = $value['dimensions'][0];
            $data[$key]['page']                 = $value['dimensions'][1];
            $data[$key]['browser']              = $value['dimensions'][2];
            $data[$key]['os']                   = $value['dimensions'][3];
            $data[$key]['country']              = $value['dimensions'][4];
            $data[$key]['iso_code']             = $value['dimensions'][5];
            $data[$key]['device']               = $value['dimensions'][6];
            $data[$key]['created_at']           = now();

            foreach ($value['metrics'] as $m_key => $m_value) {
                $data[$key]['avg_time_page'] = $m_value['values'][0];
                $data[$key]['unique_page_views'] = $m_value['values'][1];
                $data[$key]['page_view'] = $m_value['values'][2];
                $data[$key]['exit_rate'] = $m_value['values'][3];
                $data[$key]['entrances'] = $m_value['values'][4];
                $data[$key]['entrance_rate'] = $m_value['values'][5];
                $data[$key]['session'] = $m_value['values'][6];
            }
            \App\GoogleAnalyticData::insert($data);
        }
    }

    return true;
}

