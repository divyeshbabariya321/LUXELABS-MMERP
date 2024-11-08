<?php

namespace App\Console\Commands;

use App\LogRequest;
use App\Wetransfer;
use Illuminate\Console\Command;

class wetransferQueue extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'wetransferQueue';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'WeTransfer Queues';

    /**
     * Create a new command instance.
     */

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $queuesList = Wetransfer::where('is_processed', '1')->get()->toArray();

        if (! empty($queuesList)) {
            foreach ($queuesList as $list) {
                $file = $this->downloadWetransferFiles($list['url']);

                if (! empty($file)) {
                    $extension = last(explode('.', $file));
                    if ($extension == 'zip') {
                        $filename_list = [];
                        $zip = new \ZipArchive;

                        if ($zip->open(public_path('wetransfer/'.$file)) === true) {
                            for ($i = 0; $i < $zip->count(); $i++) {
                                $filename_list[] = $zip->getNameIndex($i);
                            }
                            $zip->extractTo(public_path('wetransfer/'));
                        }

                        $update = [
                            'files_count' => $zip->count(),
                            'files_list' => json_encode($filename_list),
                            'is_processed' => 2,
                        ];

                        $zip->close();
                        Wetransfer::where('id', $list['id'])->update($update);
                    } else {
                        $update = [
                            'files_count' => 1,
                            'files_list' => json_encode([$file]),
                            'is_processed' => 2,
                        ];
                        Wetransfer::where('id', $list['id'])->update($update);
                    }
                }
            }
        }

        $this->output->write('Cron complated', true);
    }

    /**
     * Download Wefransfer Files
     *
     * @param  null|mixed  $url
     * @return mixed
     */
    private function downloadWetransferFiles($url = null)
    {
        $WETRANSFER_API_URL = 'https://wetransfer.com/api/v4/transfers/';
        $startTime = date('Y-m-d H:i:s', LARAVEL_START);
        try {
            if (strpos($url, 'https://we.tl/') !== false) {
                $ch = curl_init($url);
                curl_setopt($ch, CURLOPT_FOLLOWLOCATION, false);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_HEADER, true);
                curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (X11; Linux x86_64; rv:21.0) Gecko/20100101 Firefox/21.0'); // Necessary. The server checks for a valid User-Agent.
                curl_exec($ch);

                $response = curl_exec($ch);
                preg_match_all('/^Location:(.*)$/mi', $response, $matches);
                $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

                curl_close($ch);
                LogRequest::log($startTime, $url, 'GET', json_encode([]), json_decode($response), $httpcode, wetransferQueue::class, 'downloadWetransferFiles');

                if (isset($matches[1])) {
                    if (isset($matches[1][0])) {
                        $url = trim($matches[1][0]);
                    }
                }
            }

            $url = str_replace('https://wetransfer.com/downloads/', '', $url);
            //making array from url
            $dataArray = explode('/', $url);

            if (count($dataArray) == 2) {
                $securityhash = $dataArray[1];
                $transferId = $dataArray[0];
            } elseif (count($dataArray) == 3) {
                $securityhash = $dataArray[2];
                $recieptId = $dataArray[1];
                $transferId = $dataArray[0];
            } else {
                exit('Something is wrong with url');
            }

            //making post request to get the url
            $data = [];
            $data['intent'] = 'entire_transfer';
            $data['security_hash'] = $securityhash;

            $curlURL = $WETRANSFER_API_URL.$transferId.'/download';

            $cookie = 'cookie.txt';
            $url = 'https://wetransfer.com/';
            $ch = curl_init();

            curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Ubuntu Chromium/32.0.1700.107 Chrome/32.0.1700.107 Safari/537.36');
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_COOKIESESSION, true);
            curl_setopt($ch, CURLOPT_COOKIEJAR, '/tmp/'.$cookie);
            curl_setopt($ch, CURLOPT_COOKIEFILE, '/tmp/'.$cookie);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            $response = curl_exec($ch);
            if (curl_errno($ch)) {
                exit(curl_error($ch));
            }

            $re = '/name="csrf-token" content="([^"]+)"/m';

            preg_match_all($re, $response, $matches, PREG_SET_ORDER, 0);

            if (count($matches) != 0) {
                if (isset($matches[0])) {
                    if (isset($matches[0][1])) {
                        $token = $matches[0][1];
                    }
                }
            }

            $headers[] = 'Content-Type: application/json';
            $headers[] = 'X-CSRF-Token:'.$token;

            curl_setopt($ch, CURLOPT_URL, $curlURL);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));

            $real = curl_exec($ch);
            $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            LogRequest::log($startTime, $url, 'POST', json_encode($data), json_decode($response), $httpcode, wetransferQueue::class, 'handle');

            $real = json_decode($real);

            $url = $real->direct_link;

            $extension = pathinfo($url, PATHINFO_EXTENSION);

            // Use basename() function to return the base name of file
            $file_name = basename(parse_url($url)['path']);

            if (! file_exists(public_path('wetransfer'))) {
                mkdir(public_path('wetransfer'), 0777, true);
            }
            $file = file_put_contents(public_path('wetransfer/'.$file_name), file_get_contents($url));

            return $file_name;
        } catch (\Throwable $th) {
            $this->output->write($th->getMessage(), true);

            return false;
        }

        return false;
    }
}
