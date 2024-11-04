<?php

namespace App\Console\Commands;

use App\LogRequest;
use App\MagentoDevScripUpdateLog;
use App\Models\MagentoCronList;
use App\Models\MagentoCronRunLog;
use App\StoreWebsite;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class MagentoRunCronOnMultipleWebsite extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:MagentoRunCronOnMultipleWebsite {id?} {websites_ids?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Magento Run Command On Multiple Website';

    /**
     * Create a new command instance.
     */

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $startTime = date('Y-m-d H:i:s', LARAVEL_START);
        Log::info('Start Run Magento Command On Multiple Website');

        try {
            $commandId = $this->argument('id');
            $websiteIds = $this->argument('websites_ids');
            $magCom = MagentoCronList::find($commandId);

            Log::info('Magento Command ID: '.$commandId);
            Log::info('Magento Cron Name: '.$magCom->cron_name);

            foreach ($websiteIds as $websiteId) {
                $websites = StoreWebsite::where('id', $websiteId)->get();
                if ($websites->isEmpty()) {
                    $this->logWebsiteNotFound($magCom, $websiteId);

                    continue;
                }

                foreach ($websites as $website) {
                    $this->processWebsiteCron($magCom, $website, $startTime);
                }
            }
        } catch (\Exception $e) {
            $this->handleException($e, $this->argument('id'));
        }

        Log::info('End Run Magento Command On Multiple Website');
    }

    private function logWebsiteNotFound($magCom, $websiteId): void
    {
        MagentoCronRunLog::create([
            'command_id' => $magCom->id,
            'user_id' => Auth::user()->id ?? '',
            'website_ids' => $websiteId,
            'command_name' => $magCom->cron_name,
            'server_ip' => '',
            'working_directory' => '',
            'response' => 'The website is not found!',
        ]);
    }

    private function processWebsiteCron($magCom, $website, $startTime): void
    {
        Log::info('Start Run Magento Cron for website_id: '.$website->id);

        if ($this->isValidCron($magCom, $website)) {
            $requestParams = $this->prepareRequestParams($magCom, $website);
            $response = $this->executeApiRequest($requestParams);
            $this->logApiResponse($startTime, $requestParams, $response);

            $this->handleApiResponse($magCom, $website, $response, $requestParams);
            $this->executeLocalCommand($magCom, $website);
        } else {
            $this->logInvalidCron($magCom, $website);
        }

        Log::info('End Run Magento Cron for website_id: '.$website->id);
    }

    private function isValidCron($magCom, $website): bool
    {
        return $magCom->cron_name !== '' && $website->server_ip !== '';
    }

    private function prepareRequestParams($magCom, $website): array
    {
        return [
            'command' => $magCom->cron_name,
            'dir' => $website->working_directory,
            'server' => $website->server_ip,
        ];
    }

    private function executeApiRequest(array $requestParams)
    {
        $url = getenv('MAGENTO_COMMAND_API_URL');
        $key = base64_encode('admin:86286706-032e-44cb-981c-588224f80a7d');

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($requestParams));

        $headers = [
            'Authorization: Basic '.$key,
            'Content-Type: application/json',
        ];
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        $result = curl_exec($ch);
        if (curl_errno($ch)) {
            Log::info('API Error: '.curl_error($ch));
            $result = curl_error($ch);
        }

        curl_close($ch);

        return $result;
    }

    private function logApiResponse($startTime, $requestParams, $response): void
    {
        $url = getenv('MAGENTO_COMMAND_API_URL');
        $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        LogRequest::log($startTime, $url, 'POST', json_encode($requestParams), json_decode($response), $httpcode, self::class, 'handle');
    }

    private function handleApiResponse($magCom, $website, $response, $requestParams): void
    {
        $responseDecoded = json_decode($response);

        if (isset($responseDecoded->errors)) {
            foreach ($responseDecoded->errors as $error) {
                $message = $error->code.':'.$error->title.':'.$error->detail;
                Log::info('API Response Error: '.$message);
                $this->logCronRun($magCom, $website, $message, $requestParams);
            }
        }

        if (isset($responseDecoded->data->jid)) {
            Log::info('API Response job_id: '.$responseDecoded->data->jid);
        }
    }

    private function executeLocalCommand($magCom, $website): void
    {
        $cmd = 'bash '.getenv('DEPLOYMENT_SCRIPTS_PATH').'magento-commands.sh --server '.$website->server_ip." --type custom --command '".$magCom->command_type."'";
        $this->runCommandAndLog($cmd, $magCom, $website);
    }

    private function logCronRun($magCom, $website, $response, $requestParams): void
    {
        MagentoCronRunLog::create([
            'command_id' => $magCom->id,
            'user_id' => Auth::user()->id ?? '',
            'website_ids' => $website->id,
            'command_name' => $magCom->cron_name,
            'server_ip' => $website->server_ip,
            'working_directory' => $website->working_directory,
            'response' => $response,
            'request' => json_encode($requestParams),
        ]);
    }

    private function handleException(\Exception $e, $commandId): void
    {
        Log::info(' Error on Run Magento Cron On Multiple Website: '.$e->getMessage());
        MagentoDevScripUpdateLog::create([
            'command_id' => $commandId,
            'user_id' => Auth::user()->id ?? '',
            'website_ids' => '',
            'command_name' => '',
            'server_ip' => '',
            'command_type' => '',
            'response' => ' Error '.$e->getMessage(),
        ]);
        \App\CronJob::insertLastError($this->signature, $e->getMessage());
    }
}
