<?php

namespace App\Console\Commands;

use App\Models\Country;
use App\Models\State;
use Http;
use Illuminate\Console\Command;

class StoreStates extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'store:states';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This command is used for store states of countries.';

    /**
     * Create a new command instance.
     *
     * @return void
     */

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        // API Reference: https://countrystatecity.in/docs/api/all-states/
        $response = Http::withHeaders([
            'X-CSCAPI-KEY' => 'WUZWeG9GbFpXMnhEcmRBNUZzN0JIYXpuN1FlMTd3eG1YR2duRnlwRA==',
        ])->get('https://api.countrystatecity.in/v1/states')->json();

        if (! @$response['error']) {
            foreach ($response as $value) {
                $country = Country::whereCode($value['country_code'])->first();

                if (! empty($country)) {
                    $input = [
                        'name' => $value['name'],
                        'code' => $value['iso2'],
                        'country_id' => $country->id,
                    ];

                    State::updateOrCreate($input);

                    $this->info('Stored state: '.$value['name']);
                }
            }
        }
    }
}
