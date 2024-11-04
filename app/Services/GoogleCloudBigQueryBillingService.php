<?php
// app/Services/GoogleCloudBigQueryService.php

namespace App\Services;

use Google\Cloud\BigQuery\BigQueryClient;
use Exception;

class GoogleCloudBigQueryBillingService
{
    protected $bigQueryClient, $projectID;
    

    public function showBillingAmount($googleBillingProject)
    {
        $billingData = '';
        $serviceFile = $googleBillingProject->google_billing_master->service_file ?? '';
        $projectId = $googleBillingProject->project_id ?? '';
        $datasetId = $googleBillingProject->dataset_id ?? '';
        $tableId = $googleBillingProject->table_id ?? '';
        try {
            if(!empty($serviceFile) && !empty($projectId) && !empty($datasetId) && !empty($tableId)){
                $keyFilePath = storage_path('app/googleBillingService/'.basename($serviceFile)); // Adjust the path to your JSON key file
                
                $this->bigQueryClient = new BigQueryClient([
                    'keyFilePath' => $keyFilePath,
                    'projectId' => $projectId, // Replace with your project ID
                ]);
            
                $billingData = $this->getBillingAmount($datasetId, $tableId);
            }
            return $billingData;
        } catch (Exception $e) {
            return null;
        }
    }

    public function getBillingAmount($datasetId, $tableId)
    {
        try {
            $query = sprintf('
                SELECT 
                    usage_start_time, 
                    project.name, 
                    SUM(cost) as total_cost
                FROM `%s.%s`
                WHERE project.id = "%s"
                GROUP BY usage_start_time, project.name
                ORDER BY usage_start_time DESC
                LIMIT 10', 
                $datasetId, 
                $tableId, 
                $this->projectID // Replace with your project ID
            );

            $jobConfig = $this->bigQueryClient->query($query);
            $queryResults = $this->bigQueryClient->runQuery($jobConfig);

            if ($queryResults->isComplete()) {
                return $queryResults->rows();
            } else {
                throw new Exception('Query did not complete.');
            }
        } catch (Exception $e) {
            throw new Exception('Error fetching billing information: ' . $e->getMessage());
        }
    }
}