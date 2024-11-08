<?php

namespace App\Console\Commands;

use App\Currency;
use GuzzleHttp\Client;
use Illuminate\Console\Command;

class UpdateCurrencyNames extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'currencies:update_name';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update currency names from Fixer';

    /**
     * Create a new command instance.
     */

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $fixerApiKey = config('settings.fixer_api_key');

        if (! isset($fixerApiKey)) {
            echo 'FIXER_API_KEY not set in env';

            return;
        }

        $client = new Client;
        $url = 'http://data.fixer.io/api/symbols?access_key='.$fixerApiKey;

        $response = $client->get($url);

        $responseJson = json_decode($response->getBody()->getContents());

        $currencies = json_decode(json_encode($responseJson->symbols), true);

        foreach ($currencies as $symbol => $name) {
            Currency::updateOrCreate(
                [
                    'code' => $symbol,
                ],
                [
                    'name' => $name,
                ]
            );
        }
    }
}
