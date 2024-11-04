<?php
namespace App\Traits;

use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;

trait JMeter {

    /**
     * @param Request $request
     * @return $this|false|string
     */
    public function runJMeterTest($jmxFile,$saveResult)
    {
        Log::info('jmeter call req');
        // Build your JMeter command
        $jmeterCommand = 'jmeter -n -t '.$jmxFile.' -l '.$saveResult;
        // Execute the command
        $output = [];
        exec($jmeterCommand, $output, $returnCode);

        // Check if the command executed successfully
        if ($returnCode === 0) {
            return response()->json([
                'success' => true,
                'output' => ''
            ]);
        } else {   
            Log::info('jmeter call error');
            return response()->json([
                'success' => false,
                'output' => '',
                'error' => 'Failed to execute JMeter command.',
            ], 500);
        }
    }
}