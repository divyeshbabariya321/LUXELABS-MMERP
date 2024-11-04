<?php

namespace App\Console\Commands;

use App\Product;
use App\Productactivitie;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class productActivityStore extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'productActivityStore';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Add product activity data for the day.';

    /**
     * Create a new command instance.
     */

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $productStats = Product::select('status_id', DB::raw('COUNT(id) as total'))
            ->where('stock', '>=', 1)
            ->groupBy('status_id')
            ->pluck('total', 'status_id')->all();
        foreach ($productStats as $key => $productStat) {
            $productStats = Productactivitie::insert([
                'status_id' => $key,
                'value' => $productStat,
                'created_at' => DB::raw('CURRENT_TIMESTAMP'),
            ]);
        }

        $this->output->write('Cron complated', true);
    }
}
