<?php

namespace App\Console\Commands;

use App\CronJob;
use App\Models\SonarQube;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SonarQubeRecords extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'insert-sonar-qube';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sonar Qube Records';

    /**
     * Create a new command instance.
     */

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        try {
            Log::info('Start Sonar Qube');
            $url = config('settings.sonarqube_url').'api/issues/search';
            $queryParams = [];

            $username = config('settings.sonarqube_username');
            $password = config('settings.sonarqube_password');

            $response = Http::withBasicAuth($username, $password)
                ->get($url, $queryParams);

            $responseData = $response->json();

            if (isset($responseData['total'])) {
                $total = (int) $responseData['total'];

                Log::info($total);

                if ($total > 0) {
                    $pageSize = 100;
                    $counter = 1;

                    while ($counter <= ceil($total / $pageSize)) {
                        Log::info($counter);

                        $queryParams = [
                            'ps' => $pageSize,
                            'p' => $counter,
                        ];

                        $responseDatasub = Http::withBasicAuth($username, $password)
                            ->get($url, $queryParams);

                        $responseDataPage = $responseDatasub->json();

                        if (! empty($responseDataPage['issues'])) {
                            foreach ($responseDataPage['issues'] as $value) {
                                Log::info($value['key']);

                                $input = [
                                    'key' => isset($value['key']) && ! empty($value['key']) ? $value['key'] : '',
                                    'rule' => isset($value['rule']) && ! empty($value['rule']) ? $value['rule'] : '',
                                    'severity' => isset($value['severity']) && ! empty($value['severity']) ? $value['severity'] : '',
                                    'component' => isset($value['component']) && ! empty($value['component']) ? $value['component'] : '',
                                    'project' => isset($value['project']) && ! empty($value['project']) ? $value['project'] : '',
                                    'hash' => isset($value['hash']) && ! empty($value['hash']) ? $value['hash'] : '',
                                    'resolution' => isset($value['resolution']) && ! empty($value['resolution']) ? $value['resolution'] : '',
                                    'status' => isset($value['status']) && ! empty($value['status']) ? $value['status'] : '',
                                    'message' => isset($value['message']) && ! empty($value['message']) ? $value['message'] : '',
                                    'effort' => isset($value['effort']) && ! empty($value['effort']) ? $value['effort'] : '',
                                    'debt' => isset($value['debt']) && ! empty($value['debt']) ? $value['debt'] : '',
                                    'author' => isset($value['author']) && ! empty($value['author']) ? $value['author'] : '',
                                    'creationDate' => isset($value['creationDate']) && ! empty($value['creationDate']) ? $value['creationDate'] : '',
                                    'updateDate' => isset($value['updateDate']) && ! empty($value['updateDate']) ? $value['updateDate'] : '',
                                    'closeDate' => isset($value['closeDate']) && ! empty($value['closeDate']) ? $value['closeDate'] : '',
                                    'type' => isset($value['type']) && ! empty($value['type']) ? $value['type'] : '',
                                    'scope' => isset($value['scope']) && ! empty($value['scope']) ? $value['scope'] : '',
                                    'quickFixAvailable' => isset($value['quickFixAvailable']) && ! empty($value['quickFixAvailable']) ? $value['quickFixAvailable'] : '',
                                    'textRange' => isset($value['textRange']) && ! empty($value['textRange']) ? json_encode($value['textRange']) : '',
                                    'flows' => isset($value['flows']) && ! empty($value['flows']) ? json_encode($value['flows']) : '',
                                    'tags' => isset($value['tags']) && ! empty($value['tags']) ? json_encode($value['tags']) : '',
                                    'messageFormattings' => isset($value['messageFormattings']) && ! empty($value['messageFormattings']) ? json_encode($value['messageFormattings']) : '',
                                    'codeVariants' => isset($value['codeVariants']) && ! empty($value['codeVariants']) ? json_encode($value['codeVariants']) : '',
                                ];

                                $SonarQubeRecords = SonarQube::where('key', $value['key'])->first();
                                if (! empty($SonarQubeRecords)) {
                                    SonarQube::find($SonarQubeRecords->id)->update($input);
                                } else {
                                    SonarQube::create($input);
                                }
                            }
                        }
                        $counter++;
                    }
                } else {
                    echo "No issues found with the specified parameters.\n";
                }
            } else {
                echo "Failed to get the total number of issues from SonarQube.\n";
            }

            Log::info('End Sonar Qube');
        } catch (Exception $e) {
            CronJob::insertLastError($this->signature, $e->getMessage());
        }
    }
}
