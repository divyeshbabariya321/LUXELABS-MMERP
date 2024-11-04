<?php

namespace App;

use Plank\Mediable\Mediable;
use Illuminate\Database\Eloquent\Model;

class StoreViewsGTMetrix extends Model
{
    use Mediable;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'store_views_gt_metrix';

    protected $fillable = [
        'store_view_id',
        'account_id',
        'test_id',
        'status',
        'flag',
        'error',
        'report_url',
        'website_url',
        'website_id',
        'html_load_time',
        'html_bytes',
        'page_load_time',
        'page_bytes',
        'page_elements',
        'pagespeed_score',
        'yslow_score',
        'resources',
        'pdf_file',
    ];

    protected function casts(): array
    {
        return [
            'resources' => 'array',
        ];
    }
}
