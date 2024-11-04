<?php

namespace App\Console\Commands;

use App\Category;
use Exception;
use Illuminate\Console\Command;

class CategoryMergeWithProduct extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'category:merge';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     */

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $categories = Category::where('parent_id', '!=', 0)->get();

        foreach ($categories as $category) {
            if ($category->references) {
                $this->cleanCategoryReferences($category);
            }
        }
    }

    private function cleanCategoryReferences(Category $category): void
    {
        try {
            $word = $this->sanitizeInput($category->title);
            $referenceArray = explode(',', $category->references);
            $matches = $this->findSimilarReferences($referenceArray, $word);

            $this->updateCategoryReferences($category, $matches);
        } catch (Exception $e) {
            Log::error('Error cleaning category references: '.$e->getMessage(), [
                'category_id' => $category->id,
                'exception' => $e,
            ]);
        }
    }

    private function sanitizeInput(string $input): string
    {
        $input = preg_replace('/\s+/', '', $input);

        return preg_replace('/[^a-zA-Z0-9_ -]/s', '', $input);
    }

    private function findSimilarReferences(array $referenceArray, string $word): array
    {
        $matches = [];

        foreach ($referenceArray as $input) {
            if (! empty($input)) {
                $sanitizedInput = $this->sanitizeInput($input);
                if ($this->isSimilar($sanitizedInput, $word)) {
                    $matches[] = $sanitizedInput;
                }
            }
        }

        return $matches;
    }

    private function isSimilar(string $input, string $word): bool
    {
        similar_text(strtolower($input), strtolower($word), $percent);

        return $percent >= 60;
    }

    private function updateCategoryReferences(Category $category, array $matches): void
    {
        $category->references = empty($matches) ? '' : implode(',', $matches);
        $category->update();
    }
}
