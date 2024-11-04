<?php

namespace App\Console\Commands;

use App\CronJob;
use App\CronJobReport;
use App\Services\Bots\CucProductDataEmulator;
use App\Services\Bots\CucProductExistsEmulator;
use Carbon\Carbon;
use Exception;
use Illuminate\Console\Command;

class GetCuccuiniDetailsWithEmulator extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cuccu:get-product-details';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    protected $country;

    protected $IP;

    public function handle(): void
    {
        try {
            $report = CronJobReport::create([
                'signature' => $this->signature,
                'start_time' => Carbon::now(),
            ]);

            $letters = config('settings.scrap_alphas');
            if (strpos($letters, 'C') === false) {
                return;
            }

            $this->authenticate();

            $report->update(['end_time' => Carbon::now()]);
        } catch (Exception $e) {
            CronJob::insertLastError($this->signature, $e->getMessage());
        }
    }

    private function authenticate()
    {
        $url = 'https://shop.cuccuini.it/it/register.html';

        $duskShell = new CucProductDataEmulator;
        $this->setCountry('IT');
        $duskShell->prepare();

        try {
            $duskShell->emulate($this, $url, '');
        } catch (Exception $e) {
            return response()->json(
                [
                    'code' => 500,
                    'message' => 'Opps! Something went wrong, Please try again',
                ]
            );
        }
    }

    public function doesProductExist($product)
    {
        $url = 'https://shop.cuccuini.it/it/register.html';

        $duskShell = new CucProductExistsEmulator;
        $this->setCountry('IT');
        $duskShell->prepare();

        try {
            $content = $duskShell->emulate($this, $url, '', $product);
        } catch (Exception $exception) {
            $content = false;
        }

        return $content;
    }

    private function setCountry(): void
    {
        $this->country = 'IT';
    }
}
