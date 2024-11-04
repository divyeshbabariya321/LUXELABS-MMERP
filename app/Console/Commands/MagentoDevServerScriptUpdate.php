<?php

namespace App\Console\Commands;

use App\CronJob;
use App\MagentoDevScripUpdateLog;
use App\StoreWebsite;
use Exception;
use Illuminate\Console\Command;

class MagentoDevServerScriptUpdate extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:MagentoDevUpdateScript {id?} {folder_name?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command Magento Dev Script Updates';

    /**
     * Create a new command instance.
     */

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        try {
            $websites = StoreWebsite::where('is_dev_website', 1)->where('id', $this->argument('id'))->get();
            foreach ($websites as $website) {
                $folder_name = $this->argument('folder_name');
                if ($folder_name != '' && $website->server_ip != '') {
                    $cmd = 'bash '.getenv('DEPLOYMENT_SCRIPTS_PATH').'magento-dev.sh --server '.$website->server_ip.' --site '.$folder_name;

                    $allOutput = [];
                    $allOutput[] = $cmd;
                    $result = exec($cmd, $allOutput);
                    if ($result == '') {
                        $result = 'Not any response';
                    } elseif ($result == 0) {
                        $result = 'Command run success Response '.$result;
                    } elseif ($result == 1) {
                        $result = 'Command run Fail Response '.$result;
                    } else {
                        $result = is_array($result) ? json_encode($result, true) : $result;
                    }

                    MagentoDevScripUpdateLog::create(
                        [
                            'store_website_id' => $website->id,
                            'website' => $website->website,
                            'response' => $result,
                            'command_name' => $cmd,
                            'site_folder' => $website->site_folder,
                        ]
                    );
                } else {
                    MagentoDevScripUpdateLog::create(
                        [
                            'store_website_id' => $website->id ?? '',
                            'website' => $website->website ?? '',
                            'response' => 'Please check Site folder and server ip',
                            'error' => 'Error',
                            'command_name' => 'Not run command. Please server Ip and site folder',
                            'site_folder' => $website->site_folder ?? '',
                        ]);
                }
            } //end website foreach
        } catch (Exception $e) {
            MagentoDevScripUpdateLog::create(
                [
                    'store_website_id' => $website[0]->id ?? '',
                    'website' => $website[0]->website ?? '',
                    'error' => $e->getMessage(),
                    'command_name' => 'Not run command. Please server Ip and site folder',
                    'site_folder' => $website[0]->site_folder ?? '',
                ]
            );
            CronJob::insertLastError($this->signature, $e->getMessage());
        }
    }
}
