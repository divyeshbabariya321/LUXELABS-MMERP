<?php

namespace App\Console\Commands;

use App\CronJob;
use App\Currency;
use App\Helpers\LogHelper;
use Exception;
use GuzzleHttp\Client;
use Illuminate\Console\Command;

class RefreshCurrencies extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'currencies:refresh';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Refreshes the currency convertion and rates from Fixer';

    /**
     * Create a new command instance.
     */

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        LogHelper::createCustomLogForCron($this->signature, ['message' => 'cron was started.']);
        try {
            $fixerApiKey = config('settings.fixer_api_key');
            if (! isset($fixerApiKey)) {
                echo 'FIXER_API_KEY not set in env';

                return;
            }

            $client = new Client;
            $url = 'http://data.fixer.io/api/latest?base=EUR&access_key='.$fixerApiKey;

            $response = $client->get($url);

            $responseJson = json_decode($response->getBody()->getContents());

            $currencies = json_decode(json_encode($responseJson->rates), true);

            foreach ($currencies as $symbol => $rate) {
                Currency::updateOrCreate(
                    [
                        'code' => $symbol,
                    ],
                    [
                        'rate' => $rate,
                    ]
                );
                LogHelper::createCustomLogForCron($this->signature, ['message' => 'currency rate saved.']);
            }
            LogHelper::createCustomLogForCron($this->signature, ['message' => 'cron was ended.']);
        } catch (Exception $e) {
            LogHelper::createCustomLogForCron($this->signature, ['Exception' => $e->getTraceAsString(), 'message' => $e->getMessage()]);

            CronJob::insertLastError($this->signature, $e->getMessage());
        }
    }
}
