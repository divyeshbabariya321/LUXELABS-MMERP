<?php

namespace App\Jobs;

use App\StoreWebsite;
use App\WebsiteStoreView;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use seo2websites\MagentoHelper\MagentoHelper;

class PushPageToMagento implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 5;

    public $backoff = 5;

    /**
     * Create a new job instance.
     *
     * @param  protected  $page
     * @param  protected  $updatedBy
     * @return void
     */
    public function __construct(protected $page, protected $updatedBy) {}

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            // Set time limit
            set_time_limit(0);

            // Load product and website
            $page = $this->page;
            $website = $page->storeWebsite;

            if ($website) {
                $storeWebsite = new StoreWebsite;
                if ((isset($website->tag_id) && $website->tag_id != '')) {
                    $allWebsites = $storeWebsite->where('tag_id', $website->tag_id)->get();
                } else {
                    $allWebsites = $storeWebsite->where('id', $page->store_website_id)->get();
                }

                if (! empty($allWebsites)) {
                    foreach ($allWebsites as $websitekey => $website) {
                        if ($website->website_source) {
                            // assign the stores  column
                            $fetchStores = WebsiteStoreView::where('website_store_views.name', $page->language)
                                ->join('website_stores as ws', 'ws.id', 'website_store_views.website_store_id')
                                ->join('websites as w', 'w.id', 'ws.website_id')
                                ->where('w.store_website_id', $page->store_website_id)
                                ->select('website_store_views.*')
                                ->get();

                            $stores = array_filter(array_unique(explode(',', $page->stores)));

                            if (! $fetchStores->isEmpty()) {
                                foreach ($fetchStores as $fetchStore) {
                                    if (! in_array($fetchStore->code, $stores)) {
                                        $stores[] = $fetchStore->code;
                                    }
                                }
                            }

                            $page->stores = implode(',', array_unique($stores));
                            $page->save();

                            $params = [];
                            $params['page'] = [
                                'identifier' => $page->url_key,
                                'title' => $page->title,
                                'meta_title' => $page->meta_title,
                                'meta_keywords' => $page->meta_keywords,
                                'meta_description' => $page->meta_description,
                                'content_heading' => $page->content_heading,
                                'content' => $page->content,
                                'active' => $page->active,
                                'platform_id' => $page->platform_id,
                                'page_id' => $page->id,
                                'updated_by' => $this->updatedBy?->id,
                            ];

                            if (! empty($stores)) {
                                foreach ($stores as $s) {
                                    $params['page']['store'] = $s;
                                    $id = MagentoHelper::pushWebsitePage($params, $website);
                                    if (! empty($id) && is_numeric($id)) {
                                        $page->platform_id = $id;
                                        $page->save();
                                    }
                                }
                            }
                        }
                    }
                }
            }
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    public function tags()
    {
        return ['PushPageToMagento', $this->page->id];
    }
}
