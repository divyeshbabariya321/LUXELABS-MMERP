<?php

namespace App\Console\Commands;

use App\CronJob;
use App\Marketing\WhatsappConfig;
use Exception;
use Illuminate\Console\Command;

class IncrementFrequencyWhatsappConfig extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'whatsppconfig:frequency';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Increment Frequency For WHatsapp Config Till 10';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        try {
            $configs = WhatsappConfig::where('is_customer_support', 0)->get();

            foreach ($configs as $config) {
                if ($config->frequency != 10 && $config->frequency < 10) {
                    $config->frequency = ($config->frequency + 1);
                    $config->update();
                    dump('Frequency Updated');
                }
            }
        } catch (Exception $e) {
            CronJob::insertLastError($this->signature, $e->getMessage());
        }
    }
}
