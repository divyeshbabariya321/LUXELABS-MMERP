<?php

namespace App\Console\Commands;

use App\CronJob;
use App\MagentoCommand;
use App\MagentoCommandRunLog;
use App\MagentoDevScripUpdateLog;
use App\Services\KubernetesService;
use App\StoreWebsite;
use Aws\Ssm\SsmClient;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Auth;

class MagentoRunCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:MagentoCreatRunCommand {id?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Magento Create Command';

    /**
     * Create a new command instance.
     */

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $request = [];
        try {
            $magCom = MagentoCommand::find($this->argument('id'));
            $website = StoreWebsite::find($magCom->website_ids);
            if ($website) {
                $region = $website->aws_region ? $website->aws_region : 'us-west-2';
                $key = $website->aws_api_key;
                $secret = $website->aws_api_secret;

                $document_name = $website->aws_document_name;
                $cluster = [$website->aws_cluster];
                $service_id = [$website->aws_ecs_service_id];

                if (! is_null($key) && ! is_null($secret)) {
                    $client = new SsmClient([
                        'region' => $region,
                        'credentials' => [
                            'key' => $key,
                            'secret' => $secret,
                        ],
                    ]);

                    $startExecutionRequest = [
                        'DocumentName' => $document_name,
                        'Region' => $region,
                        'Parameters' => [
                            'CommandToRun' => [
                                $magCom->command_type,
                            ],
                            'EcsCluster' => $cluster,
                            'EcsService' => $service_id,
                        ],
                    ];
                    try {
                        $result = $client->startAutomationExecution($startExecutionRequest);
                        $executionId = $result['AutomationExecutionId'];
                        logMagentoCommandRun($magCom, $website, "Started automation execution with ID: {$executionId}", $startExecutionRequest);

                    } catch (Exception $e) {
                        logMagentoCommandRun($magCom, $website, $e->getMessage(), $startExecutionRequest);
                    }
                } else {
                    Log::info('API Response Error: '.'API Key or Secret Key not found');
                    logMagentoCommandRun($magCom, $website, 'API Key or Secret Key not found', $request);
                }
            } else {
                Log::info('API Response Error: '.'Website not found');
                logMagentoCommandRun($magCom, null, 'Website not found', $request);
            }
        } catch (Exception $e) {
            Log::info(' Rum Magento Command catch error: '.$e->getMessage());
            logMagentoCommandRun($magCom, $website ?? null, $e->getMessage(), $request);
            MagentoDevScripUpdateLog::create(
                [
                    'command_id' => $magCom->id,
                    'user_id' => Auth::user()->id ?? '',
                    'website_ids' => $magCom->website_ids,
                    'command_name' => $magCom->command_type,
                    'server_ip' => '',
                    'command_type' => $magCom->command_type,
                    'response' => ' Error '.$e->getMessage(),
                    'request' => json_encode($request),
                ]
            );
            CronJob::insertLastError($this->signature, $e->getMessage());
        }
    }

    /**
     * Function is used to run command on kubernates pods which is hosted on Digital Ocean
     *
     * @param  MagentoCommand  $mageCom
     */
    private function runCommandOnDO(StoreWebsite $website, MagentoCommand $magCom)
    {
        $pod_name = $website->pod_name;
        $cluster_file = $website->cluster_file;
        $project = $website->websiteStoreProject;
        if (! is_null($pod_name) && ! is_null($cluster_file)) {
            $kubernates = new KubernetesService($cluster_file);

            $startExecutionRequest = [
                'Cluster File' => $cluster_file,
                'Pod Name' => $pod_name,
                'Parameters' => [
                    'CommandToRun' => [
                        $magCom->command_type,
                    ],
                    'Project' => $project->name,
                ],
            ];
            try {
                $data = $kubernates->executeCommandInPod($pod_name, $magCom->command_type);

                MagentoCommandRunLog::create(
                    [
                        'command_id' => $magCom->id,
                        'user_id' => Auth::user()->id ?? '',
                        'website_ids' => $website->id,
                        'command_name' => $magCom->command_type,
                        'server_ip' => $website->server_ip,
                        'command_type' => $magCom->command_type,
                        'response' => $data,
                        'request' => json_encode($startExecutionRequest), // Store the request as JSON,
                        'status' => true,
                    ]
                );
            } catch (Exception $e) {
                MagentoCommandRunLog::create(
                    [
                        'command_id' => $magCom->id,
                        'user_id' => Auth::user()->id ?? '',
                        'website_ids' => $website->id,
                        'command_name' => $magCom->command_type,
                        'server_ip' => $website->server_ip,
                        'command_type' => $magCom->command_type,
                        'response' => $e->getMessage(),
                        'request' => json_encode($startExecutionRequest), // Store the request as JSON
                    ]
                );
            }
        } else {
            MagentoCommandRunLog::create(
                [
                    'command_id' => $magCom->id,
                    'user_id' => Auth::user()->id ?? '',
                    'website_ids' => $website->id,
                    'command_name' => $magCom->command_type,
                    'server_ip' => $website->server_ip,
                    'command_type' => $magCom->command_type,
                    'response' => 'Pod Name or Cluster file not Found',
                    'request' => json_encode([]),
                ]
            );
        }
    }

    /**
     * Function is used to run command on kubernates pods which is hosted on AWS
     *
     * @param  MagentoCommand  $mageCom
     */
    private function runCommandOnAws(StoreWebsite $website, MagentoCommand $magCom)
    {
        $region = $website->aws_region ? $website->aws_region : 'us-west-2';
        $key = $website->aws_api_key;
        $secret = $website->aws_api_secret;

        $document_name = $website->aws_document_name;
        $cluster = [$website->aws_cluster];
        $service_id = [$website->aws_ecs_service_id];

        if (! is_null($key) && ! is_null($secret)) {
            $client = new SsmClient([
                'region' => $region,
                'credentials' => [
                    'key' => $key,
                    'secret' => $secret,
                ],
            ]);

            $startExecutionRequest = [
                'DocumentName' => $document_name,
                'Region' => $region,
                'Parameters' => [
                    'CommandToRun' => [
                        $magCom->command_type,
                    ],
                    'EcsCluster' => $cluster,
                    'EcsService' => $service_id,
                ],
            ];
            try {
                $result = $client->startAutomationExecution($startExecutionRequest);
                $executionId = $result['AutomationExecutionId'];

                MagentoCommandRunLog::create(
                    [
                        'command_id' => $magCom->id,
                        'user_id' => Auth::user()->id ?? '',
                        'website_ids' => $website->id,
                        'command_name' => $magCom->command_type,
                        'server_ip' => $website->server_ip,
                        'command_type' => $magCom->command_type,
                        'response' => "Started automation execution with ID: {$executionId}",
                        'request' => json_encode($startExecutionRequest), // Store the request as JSON,
                        'status' => true,
                    ]
                );
            } catch (Exception $e) {
                MagentoCommandRunLog::create(
                    [
                        'command_id' => $magCom->id,
                        'user_id' => Auth::user()->id ?? '',
                        'website_ids' => $website->id,
                        'command_name' => $magCom->command_type,
                        'server_ip' => $website->server_ip,
                        'command_type' => $magCom->command_type,
                        'response' => $e->getMessage(),
                        'request' => json_encode($startExecutionRequest), // Store the request as JSON
                    ]
                );
            }
        } else {
            MagentoCommandRunLog::create(
                [
                    'command_id' => $magCom->id,
                    'user_id' => Auth::user()->id ?? '',
                    'website_ids' => $website->id,
                    'command_name' => $magCom->command_type,
                    'server_ip' => $website->server_ip,
                    'command_type' => $magCom->command_type,
                    'response' => 'API Key or Secret Key not found',
                    'request' => json_encode([]),
                ]
            );
        }
    }
}
