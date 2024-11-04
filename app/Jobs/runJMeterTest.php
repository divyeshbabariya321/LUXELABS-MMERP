<?php

namespace App\Jobs;

use Illuminate\Support\Facades\Log;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Traits\JMeter;
use Illuminate\Support\Facades\Artisan;
use App\Models\loadTesting;
use Exception;

class runJMeterTest implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, JMeter;

    public $jmxFilePath;

    public $getLoadTesting;

    public $timeout = 4000;

    /**
     * Create a new job instance.
     *
     * 
     */
    public function __construct($getLoadTestingData)
    {
        $this->jmxFilePath = $getLoadTestingData->jmx_file_path;
        $this->getLoadTesting = $getLoadTestingData;
    }
    
    /**
     * Execute the job.
     *
     *
     */
    public function handle(): void
    {
        try {
            Log::info('command run id :'.$this->getLoadTesting->id);
            Artisan::call('run:JMeterTest',['loadTestingId' => $this->getLoadTesting->id]);
        } catch (Exception $e) {
            Log::info($e->getMessage());
        }
        
    }
}
