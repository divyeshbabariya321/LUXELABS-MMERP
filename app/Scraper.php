<?php

namespace App;
use App\User;
use App\Supplier;
use App\ScraperMapping;
use App\ScrapRequestHistory;
use App\ScrapRemark;
use App\ScrapLog;
use App\DeveloperTask;

/**
 * @SWG\Definition(type="object", @SWG\Xml(name="User"))
 */
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use App\Models\ScrapStatisticsStaus;
use Illuminate\Database\Eloquent\Model;

class Scraper extends Model
{
    /**
     * @var string
     *
     * @SWG\Property(property="supplier_id",type="integer")
     * @SWG\Property(property="parent_supplier_id",type="integer")
     * @SWG\Property(property="scraper_name",type="string")
     * @SWG\Property(property="scraper_type",type="string")
     * @SWG\Property(property="scraper_total_urls",type="string")
     * @SWG\Property(property="scraper_new_urls",type="string")
     * @SWG\Property(property="scraper_existing_urls",type="string")

     * @SWG\Property(property="scraper_start_time",type="datetime")
     * @SWG\Property(property="scraper_logic",type="string")
     * @SWG\Property(property="scraper_made_by",type="string")
     * @SWG\Property(property="scraper_priority",type="string")
     * @SWG\Property(property="next_step_in_product_flow",type="string")
     * @SWG\Property(property="status",type="string")
     * @SWG\Property(property="has_sku",type="string")
     * @SWG\Property(property="last_completed_at",type="datetime")
     * @SWG\Property(property="last_started_at",type="datetime")
     */
    const STATUS = [
        ''                        => 'N/A',
        'Ok'                      => 'Ok',
        'Rework'                  => 'Rework',
        'In Process'              => 'In Process',
        'Scrapper Fixed'          => 'Scrapper Fixed',
        'Process Complete'        => 'Process Complete',
        'Categories'              => 'Categories',
        'Logs Checked'            => 'Logs Checked',
        'Scrapper Checked'        => 'Scrapper Checked',
        'All brands Scrapped'     => 'All brands Scrapped',
        'All Categories Scrapped' => 'All Categories Scrapped',
    ];

    protected $fillable = [
        'supplier_id', 'parent_supplier_id', 'scraper_name', 'scraper_type', 'scraper_total_urls', 'scraper_new_urls', 'scraper_existing_urls', 'scraper_start_time', 'scraper_logic', 'scraper_made_by', 'scraper_priority', 'inventory_lifetime', 'next_step_in_product_flow', 'status', 'last_completed_at', 'last_started_at', 'flag', 'developer_flag', ];

    public function scraperMadeBy(): HasOne
    {
        return $this->hasOne(User::class, 'id', 'scraper_made_by');
    }

    public static function scrapersStatus()
    {
        // Fetch statuses from the database dynamically
        return ScrapStatisticsStaus::pluck('status', 'status_value')->toArray();
    }

    public function scraperParent(): HasOne
    {
        return $this->hasOne(Scraper::class, 'supplier_id', 'parent_supplier_id');
    }

    public function supplier(): HasOne
    {
        return $this->hasOne(Scraper::class, 'id', 'supplier_id');
    }

    public function mainSupplier(): HasOne
    {
        return $this->hasOne(Supplier::class, 'id', 'supplier_id');
    }

    public function mapping(): HasMany
    {
        return $this->hasMany(ScraperMapping::class, 'scrapers_id', 'id');
    }

    public function parent(): HasOne
    {
        return $this->hasOne(Scraper::class, 'id', 'parent_id');
    }

    public function getChildrenScraper($name)
    {
        $scraper = $this->where('scraper_name', $name)->first();

        return $parentScraper = $this->where('parent_id', $scraper->id)->get();
    }

    public function getChildrenScraperCount($name)
    {
        $scraper = $this->where('scraper_name', $name)->first();

        return $parentScraper = $this->where('parent_id', $scraper->id)->count();
    }

    public function getScrapHistory(): HasMany
    {
        return $this->hasMany(ScrapRequestHistory::class, 'scraper_id', 'id')->orderByDesc('updated_at')->take(20);
    }

    public function scraperRemark()
    {
        return ScrapRemark::where('scraper_name', $this->scraper_name)->latest()->first();
    }

    public function scrpRemark(): HasOne
    {
        return $this->hasOne(ScrapRemark::class, 'scraper_name', 'scraper_name');
    }

    public function developerTask($id)
    {
        return DeveloperTask::where('scraper_id', $id)->first();
    }

    public function developerTaskNew(): HasOne
    {
        return $this->hasOne(DeveloperTask::class, 'scraper_id');
    }

    public function latestMessage()
    {
        return self::join('developer_tasks as dt', 'dt.scraper_id', 'scrapers.id')
        ->join('chat_messages as cm', 'cm.developer_task_id', 'dt.id')
        ->where('dt.scraper_id', $this->scrapper_id)
        ->whereNotIn('cm.status', ['7', '8', '9', '10'])
        ->orderByDesc('cm.id')
        ->first();
    }

    public function latestMessageNew(): HasManyThrough
    {
        return $this->hasManyThrough(ChatMessage::class, DeveloperTask::class, 'scraper_id', 'developer_task_id', 'id', 'id');
    }

    public function latestLog()
    {
        return ScrapRemark::where('scraper_name', $this->scraper_name)->where('scrap_field', 'last_line_error')->latest()->first();
    }

    public function lastErrorFromScrapLog()
    {
        return ScrapLog::where('scraper_id', $this->scrapper_id)->latest()->first();
    }

    public function lastErrorFromScrapLogNew(): HasOne
    {
        return $this->hasOne(ScrapLog::class, 'scraper_id', 'id')->latest();
    }

    public function childrenScraper(): HasMany
    {
        return  $this->hasMany(Scraper::class, 'parent_id', 'id');
    }

    public function scraperDuration(): HasMany
    {
        return  $this->hasMany(ScraperDuration::class, 'scraper_id', 'id');
    }

    public function latestScrapperProcess(): HasOne
    {
        return $this->hasOne(ScraperProcess::class, 'scraper_id')->orderByDesc('id')->limit(1);
    }
}
