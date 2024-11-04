<?php

namespace App\Services;

use Exception;
use RenokiCo\PhpK8s\KubernetesCluster;

class KubernetesService
{
    protected $cluster;

    public function __construct($configFile)
    {
        $this->cluster = KubernetesCluster::fromKubeConfigYamlFile(storage_path($configFile));
    }

    public function getPodList()
    {
        $pods = $this->cluster->pod()->get();
        $names = [];
        foreach ($pods as $pod) {
            $names[] = $pod->getAttribute('metadata')['name'];
        }

        return $names;
    }

    public function getPodByName($podName)
    {
        return $this->cluster->pod()->whereName($podName)->get();
    }

    public function executeCommandInPod($podName, $command)
    {
        $pod = $this->getPodByName($podName);
        if (! $pod->running()) {
            throw new Exception('Pod is not runnig', 1);
        }
        $messages = $pod->exec(['/bin/sh', '-c', $command]);
        $cleanedData = '';
        foreach ($messages as $item) {
            if (isset($item['output']) && ! empty($item['output']) && $item['output'] != '') {
                $output = trim($item['output']);
                $output = preg_replace('/[\t\n\r]+/', "\n", $output); // Replace tabs and multiple newlines with a single newline
                $lines = array_filter(array_map('trim', explode("\n", $output)));
                $cleanedData .= implode("\n", $lines);
                if ($item['channel'] == 'error') {
                    throw new Exception($cleanedData, 1);
                }
            }
        }
        $cleanedData = json_encode($cleanedData, JSON_PRETTY_PRINT);

        return $cleanedData;
    }
}
