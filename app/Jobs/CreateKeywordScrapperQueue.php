<?php

namespace App\Jobs;

use Illuminate\Support\Facades\Log;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Exception;

class CreateKeywordScrapperQueue implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(public array $data)
    {
    }

    /**
     * @throws Exception
     */
    public function handle(): bool
    {
        try {
            self::putLog('Job start call google url to search link from erp start time : ' . date('Y-m-d H:i:s'));

            $postData         = [];
            $postData['data'] = $this->data['keyword'];
            $postData         = json_encode($postData);

            // call this endpoint - /api/googleSearch
            $curl = curl_init();
            curl_setopt_array($curl, [
                CURLOPT_URL            => config('settings.node_scraper_server') . 'api/googleSearch',
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING       => '',
                CURLOPT_MAXREDIRS      => 10,
                CURLOPT_TIMEOUT        => 50,
                CURLOPT_HTTP_VERSION   => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST  => 'POST',
                CURLOPT_HTTPHEADER     => [
                    'Content-Type: application/json',
                ],
                CURLOPT_POSTFIELDS => $postData,
            ]);
            $response = curl_exec($curl);
            $err      = curl_error($curl);
            curl_close($curl);
            Log::info(json_encode($response));
            $result = null;
            if (empty($err) && ! empty($result) && isset($result->error)) {
                self::putLog('Job create failed : ' . $result->message . ' : ' . date('Y-m-d H:i:s'));
            }

            return true;
        } catch (Exception $e) {
            self::putLog('Job Failed call google url to search link Exception from erp start time : ' . date('Y-m-d H:i:s'));
            throw new Exception($e->getMessage());
        }
    }

    public static function putLog($message)
    {
        Log::channel('daily')->info($message);

        return true;
    }
}
