<?php

namespace App\Jobs;
use App\Http\Controllers\PostmanRequestCreateController;

use Illuminate\Support\Facades\Log;
use App\LogRequest;
use App\PostmanResponse;
use App\PostmanMultipleUrl;
use App\PostmanRequestCreate;
use Illuminate\Bus\Queueable;
use App\PostmanRequestHistory;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Exception;

class PostmanRequestUrlRunJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @param private $urls
     * @param private $login_user_id
     *
     * @return void
     */
    public function __construct(private $urls, private $login_user_id)
    {
    }

    /**
     * Execute the job.
     * Sample doc link
     * https://docs.google.com/document/d/1O2nIeK9SOjn6ZKujfHdTkHacHnscjRKOG9G2OOiGaPU/edit
     *
     * @return void
     */
    public function handle(): void
    {
        // New Script
        $urls = $this->urls;

        $postmanUrls = PostmanMultipleUrl::whereIn('id', $urls)->get();

        foreach ($postmanUrls as $postmanUrl) {
            $postman = PostmanRequestCreate::where('id', $postmanUrl->postman_request_create_id)->first();
            if (empty($postman)) {
                Log::error('Postman Send request API Error=> Postman request data not found' . ' #id #' . $postmanUrl->postman_request_create_id ?? '');

                app(PostmanRequestCreateController::class)->PostmanErrorLog($postmanUrl->postman_request_create_id ?? '', 'Postman Send request API ', ' Postman request data not found', 'postman_request_creates', $this->login_user_id);

                try {
                    PostmanError::create([
                        'user_id'        => $this->login_user_id,
                        'parent_id'      => $postmanUrl->postman_request_create_id ?? '',
                        'parent_id_type' => 'Postman Send request API ',
                        'parent_table'   => 'postman_request_creates',
                        'error'          => ' Postman request data not found',
                    ]);
                } catch (Exception $e) {
                    PostmanError::create([
                        'user_id'        => $this->login_user_id,
                        'parent_id'      => $postmanUrl->postman_request_create_id ?? '',
                        'parent_id_type' => 'Postman Send request API ',
                        'parent_table'   => 'postman_request_creates',
                        'error'          => $e->getMessage(),
                    ]);
                }

                dd(response()->json(['code' => 500, 'message' => 'Request Data not found']));
            } else {
                PostmanRequestHistory::create(
                    [
                        'user_id'         => $this->login_user_id,
                        'request_id'      => $postman->id,
                        'request_data'    => $postman->body_json,
                        'request_url'     => $postmanUrl->request_url,
                        'request_headers' => "'Content-Type: application/json',
                                            'Authorization: '" . $postman->authorization_type . "',
                                            'Cookie: PHPSESSID=l15g0ovuc3jpr98tol956voan6'",
                    ]
                );
                $header = [
                    'Content-Type: application/json',
                    $postman->request_headers,
                    'Authorization:Bearer ' . $postman->authorization_token,
                ];

                $response = app(PostmanRequestCreateController::class)->fireApi($postman->body_json, $postmanUrl->request_url, $header, $postman->request_type);

                $startTime = date('Y-m-d H:i:s', LARAVEL_START);
                $curl      = curl_init();
                $http_code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
                $url       = $postmanUrl->request_url;
                LogRequest::log($startTime, $url, 'GET', json_encode([]), json_decode($response), $http_code, PostmanRequestCreateController::class, 'sendPostmanRequestAPI');
                curl_close($curl);

                $response = $response ? json_encode($response) : 'Not found response';
                PostmanResponse::create(
                    [
                        'user_id'       => $this->login_user_id,
                        'request_id'    => $postman->id,
                        'response'      => $response,
                        'request_url'   => $postmanUrl->request_url,
                        'request_data'  => $postman->body_json,
                        'response_code' => $http_code,
                    ]
                );

                if (! empty($response)) {
                    Log::info('Postman Send request API Response => ' . $response . ' #id #' . $postman->id ?? '');

                    app(PostmanRequestCreateController::class)->PostmanErrorLog($postman->id ?? '', 'Postman Send request API Response ', $response, 'postman_responses', $this->login_user_id);
                }
            }
        }
    }

    public function tags()
    {
        return ['magento_media_sync'];
    }
}
