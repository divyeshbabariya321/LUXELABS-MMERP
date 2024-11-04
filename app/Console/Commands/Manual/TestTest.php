<?php

namespace App\Console\Commands\Manual;

use App\SkuColorReferences;
use Illuminate\Console\Command;

class TestTest extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:test';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Console command to test new things';

    /**
     * Create a new command instance.
     */

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $skuColorReferences = new SkuColorReferences;
        $skuColorReferences->brand_id = 1;
        $skuColorReferences->color_code = '1000';
        $skuColorReferences->color_name = 'Black';
        $skuColorReferences->save();
    }
}
