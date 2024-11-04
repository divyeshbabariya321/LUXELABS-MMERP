<?php

namespace App\Repositories;

use App\GTMatrixErrorLog;
use App\StoreViewsGTMetrix;
use App\StoreGTMetrixAccount;
use Entrecore\GTMetrixClient\GTMetrixClient;
use Exception;

class GtMatrixRepository
{
    public function pushtoTest($gtMatrixURL)
    {
        $gt_metrix['store_view_id'] = $gtMatrixURL->store_view_id;
        $gt_metrix['account_id']    = $gtMatrixURL->account_id;
        $gt_metrix['website_id']    = $gtMatrixURL->id;
        $gt_metrix['website_url']   = $gtMatrixURL->website_url;
        $gtMatrix                   = StoreViewsGTMetrix::create($gt_metrix);
        $gtAccount                  = $this->getAvailableAccounts();
        if (! $gtAccount) {
            $this->GTMatrixError($gtMatrix->id, 'Store Views GTMetrix', 'Account Do not found', 'Account Do not found');

            return 'Account Do not found';
        }
        $client = new GTMetrixClient();
        $client->setUsername($gtAccount->email);
        $client->setAPIKey($gtAccount->account_id);
        $client->getLocations();
        $client->getBrowsers();
        $test   = $client->startTest($gtMatrix->website_url);
        $update = [
            'test_id'    => $test->getId(),
            'status'     => 'queued',
            'account_id' => $gtAccount->account_id,
        ];
        $gtMatrix->update($update);
        if (! $gtMatrix->test_id) {
            $this->GTMatrixError($gtMatrix->id, 'Store Views GTMetrix', 'Account ', 'Account test_id not found Email : ' . $gtAccount->email);

            return false;
        }

        return $gtMatrix;
    }

    public function generateLog($gtMatrix)
    {
        $gtmatrixAccount = StoreGTMetrixAccount::where('account_id', $gtMatrix->account_id)->where('status', 'active')->first();
        $gtAccount       = $this->getAccount($gtmatrixAccount);
        if (! $gtAccount) {
            $this->GTMatrixError($gtMatrix->id, 'Store GT Metrix Accoun', 'account_id ', 'Account account_id not found. Not account exist account_id : ' . $gtMatrix->account_id);

            return 'Account account_id not found. Not account exist account_id : ' . $gtMatrix->account_id;
        }
        $this->getGtMatrixRecord($gtAccount, $gtMatrix);
    }

    public function getAccount($gtmatrixAccount)
    {
        if ($this->checkGtmatrixAccountCredit($gtmatrixAccount)) {
            return $gtmatrixAccount;
        }
        $this->GTMatrixError($gtmatrixAccount->id, 'Store GT Metrix Accoun', 'account_id ', 'Account account_id not found. Not account exist account_id : ' . $gtmatrixAccount->account_id);

        return 'Account account_id not found. Not account exist account_id : ' . $gtmatrixAccount->account_id;
    }

    public function getAvailableAccounts($gtmatrixAccount = null)
    {
        if (! $this->checkGtmatrixAccountCredit($gtmatrixAccount)) {
            $gtmatrixAccounts = StoreGTMetrixAccount::where('status', 'active')->orderByDesc('id')->get();
            foreach ($gtmatrixAccounts as $gtmatrixAccountCheck) {
                if ($gtmatrixAccount) {
                    if ($gtmatrixAccount->id == $gtmatrixAccountCheck->id) {
                        continue;
                    }
                }
                if ($this->checkGtmatrixAccountCredit($gtmatrixAccountCheck)) {
                    return $gtmatrixAccountCheck;
                }
            }
        } else {
            $this->GTMatrixError($gtmatrixAccount->id, 'Store GT Metrix Account check', 'Check Account Credit', 'Checking Gtmatrix Account Credit not found.');

            return $gtmatrixAccount;
        }
    }

    public function checkGtmatrixAccountCredit($gtMatrixAccount)
    {
        if (! $gtMatrixAccount) {
            return false;
        }

        //Initialize curl
        $curl = curl_init();
        curl_setopt_array($curl, [
            CURLOPT_URL            => 'https://gtmetrix.com/api/2.0/status',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_USERPWD        => $gtMatrixAccount->account_id . ':' . '',
            CURLOPT_ENCODING       => '',
            CURLOPT_MAXREDIRS      => 10,
            CURLOPT_TIMEOUT        => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION   => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST  => 'GET',
        ]);
        $response = curl_exec($curl);
        $err      = curl_error($curl);
        if ($err) {
            $this->GTMatrixError($gtMatrixAccount->account_id, 'gtmetrix', 'API response error', $err . ' Repsonce => ' . $response);
        }
        curl_close($curl);
        $data    = json_decode($response);
        $credits = '';
        if (isset($data->data->attributes->api_credits)) {
            $credits = $data->data->attributes->api_credits;
        }
        if ($credits && $credits != 0) {
            return $gtMatrixAccount;
        }

        return false;
    }

    public function getGtMatrixRecord($gtMatrixAccount, $gtmatrix)
    {
        $client = new GTMetrixClient();
        $client->setUsername($gtMatrixAccount->email);
        $client->setAPIKey($gtMatrixAccount->account_id);
        $client->getLocations();
        $client->getBrowsers();
        $reportResult = $client->getTestStatus($gtmatrix->test_id);
        $gtmatrix->update([
            'status'          => $reportResult->getState(),
            'error'           => $reportResult->getError(),
            'report_url'      => $reportResult->getReportUrl(),
            'html_load_time'  => $reportResult->getHtmlLoadTime(),
            'html_bytes'      => $reportResult->getHtmlBytes(),
            'page_load_time'  => $reportResult->getPageLoadTime(),
            'page_bytes'      => $reportResult->getPageBytes(),
            'page_elements'   => $reportResult->getPageElements(),
            'pagespeed_score' => $reportResult->getPagespeedScore(),
            'yslow_score'     => $reportResult->getYslowScore(),
            'resources'       => json_encode($reportResult->getResources()),
        ]);
        $resources = $reportResult->getResources();

        //Generate Yslow
        if (! empty($resources['yslow'])) {
            $this->generateFile($resources['yslow'], $gtMatrixAccount, $gtmatrix, '_yslow.json', 'yslow_json');
        }
        //Generate Report PDF
        if (! empty($resources['report_pdf'])) {
            $this->generateFile($resources['report_pdf'], $gtMatrixAccount, $gtmatrix, '.pdf', 'pdf_file');
        }
        //Generate Pagespeed
        if (! empty($resources['pagespeed'])) {
            $this->generateFile($resources['pagespeed'], $gtMatrixAccount, $gtmatrix, '_pagespeed.json', 'pagespeed_json');
        }
    }

    //Getting PDF Report
    public function generateFile($resources, $gtMatrixAccount, $gtmatrix, $filename, $field)
    {
        $ch = curl_init($resources);
        curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
        curl_setopt($ch, CURLOPT_USERPWD, $gtMatrixAccount->email . ':' . $gtMatrixAccount->account_id);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 0);
        $err = curl_error($ch);
        if ($err) {
            $this->GTMatrixError($gtMatrixAccount->account_id, 'gtmetrix', 'Getting PDF Report Error', 'Getting PDF Report Error ' . $err);
        }
        $result   = strip_tags(curl_exec($ch));
        $fileName = '/uploads/gt-matrix/' . $gtmatrix->test_id . $filename;
        $file     = public_path() . $fileName;
        file_put_contents($file, $result);
        $gtmatrix->$field = $fileName;
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
