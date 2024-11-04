<?php

namespace App\Console\Commands;

use App\BuildProcessHistory;
use Illuminate\Console\Command;

class BuildStatus extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:updatBuildStatus';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     */

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $buildHistory = BuildProcessHistory::all();
        $jenkins = new \JenkinsKhan\Jenkins('https://apibuild:117ed14fbbe668b88696baa43d37c6fb48@build.theluxuryunlimited.com:8080');

        foreach ($buildHistory as $history) {
            $job = $jenkins->getJob($history['build_name']);
            foreach ($job->getBuilds() as $build) {
                $number = $build->getNumber();
                var_dump($number);
                $result = $build->getResult();
                var_dump($result);
                BuildProcessHistory::where('build_name', $history['build_name'])->where('build_number', $number)->update(['status' => strtolower($result)]);
            }
        }
    }
}
