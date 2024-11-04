<?php
namespace App\Traits;

use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;

trait JMeterHtml {

    /**
     * @param Request $request
     * @return $this|false|string
     */
    public function generateJMeterHtml($jtlFile,$htmlFile)
    {
        Log::info('jmeter html call req');
        // Build your JMeter command
        $jmeterCommand = 'jmeter -g '.$jtlFile.' -o '.$htmlFile;
        // Execute the command
        $output = [];
        exec($jmeterCommand, $output, $returnCode);

        // Check if the command executed successfully
        if ($returnCode === 0) {
            Log::info('jmeter html called');
            // Command executed successfully
            return response()->json([
                'success' => true,
                'output' => ''
            ]);
        } else {   
            Log::info('jmeter call error');          
            // Command failed
            return response()->json([
                'success' => false,
                'output' => '',
                'error' => 'Failed to execute JMeter command.',
            ], 500);
        }
    }
}
