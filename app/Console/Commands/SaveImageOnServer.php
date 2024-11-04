<?php

namespace App\Console\Commands;

use App\CronJob;
use App\CronJobReport;
use App\Services\Bots\Prada;
use Carbon\Carbon;
use Exception;
use GuzzleHttp\Client;
use Illuminate\Console\Command;

class SaveImageOnServer extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'save:image-to-server';

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

            $this->authenticate();

            $report->update(['end_time' => Carbon::now()]);
        } catch (Exception $e) {
            CronJob::insertLastError($this->signature, $e->getMessage());
        }
    }

    private function authenticate()
    {
        $url = 'http://shop.cuccuini.it/it/register.html';

        $duskShell = new Prada(new Client);
        $this->setCountry('IT');
        $duskShell->prepare();

        try {
            $duskShell->emulate($this, $url, '');
        } catch (Exception $exception) {
            return response()->json('Opps! Something went wrong', 400);
        }
    }

    private function setCountry(): void
    {
        $this->country = 'IT';
    }
}
