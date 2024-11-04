<?php

namespace App\Http\Controllers;
use App\Order;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Arr;
use Google\Cloud\BigQuery\BigQueryClient;
use App\Models\EmailTemplate;

class TestController extends Controller
{
    public function index()
    {
        $order = Order::find(57);

        $html     = EmailTemplate::where('id', 1)->first();
        $htmlData = $html->html;
        $re       = '/<loop-orderProducts>((.|\n)*?)<\/loop-orderProducts>/m';
        preg_match_all($re, $htmlData, $matches, PREG_SET_ORDER, 0);
        if (count($matches) != 0) {
            foreach ($matches as $index => $match) {
                $data = null;
                foreach ($order->orderProducts as $orderProduct) {
                    $data .= $this->getData($orderProduct, $match[1]);
                }
                if ($data) {
                    $htmlData = str_replace($match[1], $data, $htmlData);
                }
            }
        }

        $newData = $this->getData($order, $htmlData);
        echo $newData;
    }

    public function getData($order, $htmlData)
    {
        preg_match_all('/{{(.*?)}}/i', $htmlData, $matches);
        if (count($matches) != 0) {
            $matches = $matches[0];
            foreach ($matches as $match) {
                $matchString = str_replace(['{{', '}}'], '', $match);
                $value       = Arr::get($order, trim($matchString));
                $htmlData    = str_replace($match, $value, $htmlData);
            }
        }

        return $htmlData;
    }

    public function bigQuery()
    {
        $config = [
            'keyFilePath' => '/Users/satyamtripathi/Work/sololux-erp/public/big.json',
            'projectId'   => 'brandsandlabels',
        ];

        $bigQuery       = new BigQueryClient($config);
        $query          = 'SELECT * FROM `brandsandlabels.firebase_crashlytics.com_app_brandslabels_ANDROID_REALTIME` WHERE DATE(event_timestamp) = "2022-06-03"';
        $queryJobConfig = $bigQuery->query($query)
            ->parameters([]);
        $queryResults = $bigQuery->runQuery($queryJobConfig);
        foreach ($queryResults as $row) {
            dd($row);
        }
    }
}
