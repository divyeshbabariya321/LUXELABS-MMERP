<?php

namespace App\Console\Commands;

use App\Compositions;
use Illuminate\Console\Command;

class DeleteCompositionWithNoProduct extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'delete-composition:with-no-products';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Delete composition with no products';

    /**
     * Create a new command instance.
     */

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        //
        $compositions = Compositions::query();

        $compositions = $compositions->where(function ($q) {
            $q->orWhere('replace_with', '')->orWhereNull('replace_with');
        });

        $compositions = $compositions->get();

        if (! $compositions->isEmpty()) {
            foreach ($compositions as $c) {
                $count = $c->products($c->name);
                if ($count <= 0) {
                    $c->delete();
                    echo "Compositions {$c->name} => $count count found so deleted";
                    echo PHP_EOL;
                }
            }
        }
    }
}
