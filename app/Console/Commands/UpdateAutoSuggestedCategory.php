<?php

namespace App\Console\Commands;

use App\Category;
use App\Jobs\UpdateProductCategoryFromErp;
use App\UserUpdatedAttributeHistory;
use Illuminate\Console\Command;

class UpdateAutoSuggestedCategory extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'update:auto-suggested-category';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update Auto suggested category';

    /**
     * Create a new command instance.
     */

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $unKnownCategory = Category::where('title', 'LIKE', '%Unknown Category%')->first();
        $unKnownCategories = explode(',', $unKnownCategory->references);
        $unKnownCategories = array_unique($unKnownCategories);

        $input = preg_quote('', '~');
        $unKnownCategories = preg_grep('~'.$input.'~', $unKnownCategories);

        if (! empty($unKnownCategories)) {

            foreach ($unKnownCategories as $i => $unkc) {
                $filter = Category::updateCategoryAutoSpace($unkc);
                if ($filter) {
                    $old = $unKnownCategory->id;
                    $from = $unkc;
                    $to = $filter->id;
                    $change = 'yes';
                    $wholeString = $unkc;
                    if ($change == 'yes') {
                        UpdateProductCategoryFromErp::dispatch([
                            'from' => $from,
                            'to' => $to,
                            'user_id' => 152,
                        ])->onQueue('supplier_products');
                    }
                    $c = $unKnownCategory;
                    if ($c) {
                        $allrefernce = explode(',', $c->references);
                        $newRef = [];
                        if (! empty($allrefernce)) {
                            foreach ($allrefernce as $ar) {
                                if ($ar != $wholeString) {
                                    $newRef[] = $ar;
                                }
                            }
                        }
                        $c->references = implode(',', $newRef);
                        $c->save();
                        // new category reference store
                        if ($filter) {
                            $existingRef = explode(',', $filter->references);
                            $existingRef[] = $from;

                            UserUpdatedAttributeHistory::create([
                                'old_value' => $filter->references,
                                'new_value' => implode(',', array_unique($existingRef)),

                                'attribute_name' => 'category',
                                'attribute_id' => $filter->id,
                                'user_id' => 152,
                            ]);

                            $filter->references = implode(',', array_unique($existingRef));
                            $filter->save();

                            echo $unkc.' updated to '.$filter->title;
                            echo PHP_EOL;
                        }
                    }
                }
            }
        }
    }
}
