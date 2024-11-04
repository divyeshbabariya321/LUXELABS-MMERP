<?php

namespace App\Console\Commands;

use App\Models\loadTesting;
use App\Traits\JMeter;
use App\Traits\JMeterHtml;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class runJMeterTest extends Command
{
    use JMeter, JMeterHtml;

    protected $jmxFilePath;

    /**
     * The name and signature of the console command.
     *
     * var string
     */
    protected $signature = 'run:JMeterTest {loadTestingId}';

    /**
     * The console command description.
     *
     * var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * return void
     */

    /**
     * Execute the console command.
     *
     * return int
     */
    public function handle(): int
    {
        try {
            $this->info('Initialized JMeter command');
            $loadTestingId = $this->argument('loadTestingId');
            Log::info('command run new');
            $getLoadTestingRow = loadTesting::find($loadTestingId);
            $domainName = str_replace('.', '-', $getLoadTestingRow->domain_name);
            $fileName = $domainName.'-'.time();

            $saveResult = storage_path('app/public/jmx/'.$fileName.'.jtl');
            $this->info($saveResult);
            $jmxFile = storage_path('app/public/jmx/'.basename($getLoadTestingRow->jmx_file_path));
            $this->info('Started executing command ');
            // $data = $this->runJMeterTest($jmxFile,$saveResult);
            Log::info('jmeter call req --> jmeter -n -t '.$jmxFile.' -l '.$saveResult);
            // Build your JMeter command
            $jmeterCommand = 'jmeter -n -t '.$jmxFile.' -l '.$saveResult;
            // Execute the command
            $output = [];
            exec($jmeterCommand, $output, $returnCode);
            $this->info('jmeter return code-->'.$returnCode);
            if ($returnCode === 0) { //if($data->original['success']){
                $this->info('sucessfully executed jmeter command.');
                $getLoadTestingRow->status = 2;
                $getLoadTestingRow->jtl_file_path = $saveResult;
                $getLoadTestingRow->save();
                $htmlFile = storage_path('app/public/jmx/'.$fileName.'.html');
                $htmlResponse = $this->generateJMeterHtml($saveResult, $htmlFile);
                if ($returnCode === 0) { //if($data->original['success']){
                    $getLoadTestingRow->status = 4;
                    $getLoadTestingRow->save();
                }
                Log::info('command run successfully - ');

                return 1;
            } else {

                $getLoadTestingRow->status = 3;
                $getLoadTestingRow->save();
                Log::info('Failed to execute JMeter command for.');

                return 0;
            }
            //return $data;
        } catch (Exception $e) {
            Log::info('command error'.$e->getMessage());
            throw new Exception($e->getMessage());
        }

    }
}
