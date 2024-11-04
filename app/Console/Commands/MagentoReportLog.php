<?php

namespace App\Console\Commands;

use App\CronJob;
use App\StoreWebsite;
use App\WebsiteLog;
use Exception;
use Illuminate\Console\Command;

class MagentoReportLog extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:MagentoReportLog';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Magento Report Log';

    /**
     * Create a new command instance.
     */
    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        try {
            $storewebsite = StoreWebsite::whereNotNull('server_ip')->get();

            $types = ['unit', 'integration', 'integration-all', 'static', 'static-all', 'integrity', 'legacy', 'default'];

            foreach ($storewebsite as $stroewebsite) {
                foreach ($types as $type) {
                    $cmd = 'bash '.getenv('DEPLOYMENT_SCRIPTS_PATH').'magento-commands.sh --server '.$stroewebsite->server_ip.' --type tests --test '.$type;

                    $allOutput = [];
                    $allOutput[] = $cmd;

                    $result = exec($cmd, $allOutput); //Execute command

                    //Storing data to log table
                    if (! empty($result)) {
                        $ins = new WebsiteLog;
                        $ins->sql_query = json_encode($result);
                        $ins->time = date('Y-m-d H:s:i');
                        $ins->website_id = $storewebsite->id ?? '';
                        $ins->type = 'MagentoLog-'.$type;
                        $ins->save();
                    }
                }
            }
            echo '=== DONE ===';
        } catch (Exception $e) {
            CronJob::insertLastError($this->signature, $e->getMessage());
        }
    }
}
