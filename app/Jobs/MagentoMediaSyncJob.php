<?php

namespace App\Jobs;

use Illuminate\Support\Facades\Log;
use Illuminate\Bus\Queueable;
use App\Models\MagentoMediaSync;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class MagentoMediaSyncJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @param private $sourceStoreWebsites
     * @param private $destStoreWebsites
     * @param private $source_store_website_id
     * @param private $dest_store_website_id
     * @param private $login_user_id
     *
     * @return void
     */
    public function __construct(private $sourceStoreWebsites, private $destStoreWebsites, private $source_store_website_id, private $dest_store_website_id, private $login_user_id)
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
        $source_server_ip  = $this->sourceStoreWebsites->server_ip;
        $source_server_dir = $this->sourceStoreWebsites->working_directory;
        $dest_server_ip    = $this->destStoreWebsites->server_ip;
        $dest_server_dir   = $this->destStoreWebsites->working_directory;

        $scriptsPath = getenv('DEPLOYMENT_SCRIPTS_PATH');

        $cmd = "bash $scriptsPath" . "sync-magento-static-files.sh -source_server_ip \"$source_server_ip\" -source_server_dir \"$source_server_dir\" -dest_server_ip \"$dest_server_ip\" -dest_server_dir \"$dest_server_dir\" 2>&1";

        $result = exec($cmd, $output, $return_var);
        Log::info('store command:' . $cmd);
        Log::info('store output:' . print_r($output, true));
        Log::info('store return_var:' . $return_var);

        $useraccess = MagentoMediaSync::create([
            'created_by'              => $this->login_user_id,
            'source_store_website_id' => $this->source_store_website_id,
            'dest_store_website_id'   => $this->dest_store_website_id,
            'source_server_ip'        => $source_server_ip,
            'source_server_dir'       => $source_server_dir,
            'dest_server_ip'          => $dest_server_ip,
            'dest_server_dir'         => $dest_server_dir,
            'request_data'            => $cmd,
            'response_data'           => json_encode($result),
        ]);
    }

    public function tags()
    {
        return ['magento_media_sync'];
    }
}
