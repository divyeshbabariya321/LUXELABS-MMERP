<?php

namespace App\Jobs;

use App\Category;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Exception;

class PushCategorySeoToMagento implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $category;

    public $tries = 5;

    public $backoff = 5;

    /**
     * Create a new job instance.
     *
     * @param mixed     $categories
     * @param protected $stores
     *
     * @return void
     */
    public function __construct($categories, protected $stores)
    {
        $this->category = $categories;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(): void
    {
        try {
            // Set time limit
            set_time_limit(0);
            Category::pushStoreWebsiteCategory($this->category, $this->stores);
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    public function tags()
    {
        return ['magetwo', $this->category->id];
    }
}
