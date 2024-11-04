<?php

namespace App\Jobs;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\MagentoModule;
use App\MagentoModuleLogs;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Exception;

class SyncMagentoModules implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 5;

    public $backoff = 5;

    /**
     * Create a new job instance.
     *
     * @param protected $storeWebsite
     * @param protected $scriptsPath
     * @param protected $updated_by
     *
     * @return void
     */
    public function __construct(protected $storeWebsite, protected $scriptsPath, protected $updated_by)
    {
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(): void
    {
        Log::info('SyncMagentoModules Queue');
        try {
            // Set time limit
            set_time_limit(0);

            $website = $this->storeWebsite->title;
            $server  = $this->storeWebsite->server_ip;
            $rootDir = $this->storeWebsite->working_directory;
            $action  = 'sync';

            $cmd = "bash $this->scriptsPath" . "sync-magento-modules.sh -w \"$website\" -s \"$server\" -d \"$rootDir\" -a \"$action\" 2>&1";
            Log::info('syncModules command Before Command Run:' . $cmd);
            $result = exec($cmd, $output, $return_var);
            Log::info('syncModules command After command Run:' . $cmd);
            Log::info('syncModules output:' . print_r($output, true));
            Log::info('syncModules return_var:' . $return_var);

            $response = json_decode($output[0]);

            if (! isset($output[0])) {
                MagentoModuleLogs::create(['store_website_id' => $this->storeWebsite->id, 'updated_by' => $this->updated_by, 'command' => $cmd, 'status' => 'Error', 'response' => json_encode($output)]);
                $return_data[] = ['code' => 500, 'message' => 'The response is not found!', 'store_website_id' => $this->storeWebsite->id];
                Log::info('syncModules output is not set:' . print_r($return_data, true));
            }
            Log::info('Database name.' . DB::connection()->getDatabaseName());
            // Sample Output  $output[0] = enabled=mod1,mod2,mod3
            // Sample Output  $output[2] = disabled=mod1,mod2,mod3
            $enabledModules  = [];
            $disabledModules = [];

            if (strpos($output[0], 'enabled=') === 0) {
                // Remove "enabled=" and push the remaining values to the $enabledModules.
                $enabledModules = explode(',', substr($output[0], 8));
                Log::info('syncModules enabledModules:' . print_r($enabledModules, true));
            }
            if (strpos($output[2], 'disabled=') === 0) {
                // Remove "disabled=" and push the remaining values to the $disabledModules.
                $disabledModules = explode(',', substr($output[2], 9));
                Log::info('syncModules disabledModules:' . print_r($disabledModules, true));
            }

            if ($enabledModules) {
                foreach ($enabledModules as $enabledModule) {
                    if ($enabledModule) {
                        $magento_module = MagentoModule::where('module', $enabledModule)
                            ->where('store_website_id', $this->storeWebsite->id)
                            ->first();

                        if (! $magento_module) {
                            // The record does not exist, so create it
                            $magento_module = new MagentoModule([
                                'module'           => $enabledModule,
                                'store_website_id' => $this->storeWebsite->id,
                                'status'           => 1, // The value you want to set for 'status'
                            ]);
                            $magento_module->save();

                            // Log the creation of a new record
                            MagentoModuleLogs::create([
                                'store_website_id'  => $this->storeWebsite->id,
                                'updated_by'        => $this->updated_by,
                                'command'           => $cmd,
                                'status'            => 'Created',
                                'response'          => json_encode($output),
                                'magento_module_id' => $magento_module->id,
                            ]);

                            Log::info('syncModules output logs 1:' . print_r(json_encode($output), true));
                        } elseif ($magento_module->status != 1) {
                            // The record exists, but 'status' is not 1, so update it
                            $magento_module->status = 1;
                            $magento_module->save();

                            // Log the update of an existing record
                            MagentoModuleLogs::create([
                                'store_website_id'  => $this->storeWebsite->id,
                                'updated_by'        => $this->updated_by,
                                'command'           => $cmd,
                                'status'            => 'Updated',
                                'response'          => json_encode($output),
                                'magento_module_id' => $magento_module->id,
                            ]);

                            Log::info('syncModules output logs 2:' . print_r(json_encode($output), true));
                        }
                    }
                }
            }

            if ($disabledModules) {
                foreach ($disabledModules as $disableModule) {
                    if ($disableModule) {
                        $magento_module = MagentoModule::where('module', $disableModule)
                            ->where('store_website_id', $this->storeWebsite->id)
                            ->first();

                        if (! $magento_module) {
                            // The record does not exist, so create it
                            $magento_module = new MagentoModule([
                                'module'           => $disableModule,
                                'store_website_id' => $this->storeWebsite->id,
                                'status'           => 0, // The value you want to set for 'status'
                            ]);
                            $magento_module->save();

                            // Log the creation of a new record
                            MagentoModuleLogs::create([
                                'store_website_id'  => $this->storeWebsite->id,
                                'updated_by'        => $this->updated_by,
                                'command'           => $cmd,
                                'status'            => 'Created',
                                'response'          => json_encode($output),
                                'magento_module_id' => $magento_module->id,
                            ]);

                            Log::info('syncModules output logs 3:' . print_r(json_encode($output), true));
                        } elseif ($magento_module->status != 0) {
                            // The record exists, but 'status' is not 0, so update it
                            $magento_module->status = 0;
                            $magento_module->save();

                            // Log the update of an existing record
                            MagentoModuleLogs::create([
                                'store_website_id'  => $this->storeWebsite->id,
                                'updated_by'        => $this->updated_by,
                                'command'           => $cmd,
                                'status'            => 'Updated',
                                'response'          => json_encode($output),
                                'magento_module_id' => $magento_module->id,
                            ]);

                            Log::info('syncModules output logs 4:' . print_r(json_encode($output), true));
                        }
                    }
                }
            }
        } catch (Exception $e) {
            Log::info($e->getMessage());
            throw new Exception($e->getMessage());
        }
    }

    public function tags()
    {
        return ['syncMagentoModules', $this->storeWebsite->id];
    }
}
