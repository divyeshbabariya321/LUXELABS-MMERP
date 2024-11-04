<?php

namespace App\Console\Commands;

use App\ChatbotQuestionExample;
use App\Product;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class WatsonBrandCategoryGenerate extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'WatsonBrandCategoryGenerate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate the brand and category combination';

    /**
     * Create a new command instance.
     */

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        try {
            Log::info($this->signature.'Starting..');

            Product::where('products.name', '!=', null)->join('brands', 'products.brand', 'brands.id')
                ->join('categories as cat', 'cat.id', 'products.category')
                ->leftjoin('categories as sub_cat', 'sub_cat.id', 'cat.parent_id')
                ->leftjoin('categories as main_cat', 'main_cat.id', 'sub_cat.parent_id')
                ->select('cat.title', 'products.id as id', 'brands.name as brand', 'sub_cat.title as sub_category', 'main_cat.title as main_category')
                ->groupBy(['brand', 'category'])->orderBy('products.id')->chunk(100, function ($Query) {
                    $chatQueArr = [];

                    foreach ($Query as $value) {
                        $chatQueArr[] = [
                            'question' => ucwords($value->brand.' '.$value->main_category.' '.$value->sub_category.' '.$value->title),
                            'chatbot_question_id' => 117,
                        ];
                    }

                    ChatbotQuestionExample::insert($chatQueArr);
                    $chatQueArr = [];
                });

            Log::info($this->signature.'Run success');
        } catch (Exception $e) {
            Log::error($this->signature.':: '.$e->getMessage());
        }
    }
}
