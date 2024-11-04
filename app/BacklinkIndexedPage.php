<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class BacklinkIndexedPage extends Model
{

    protected $fillable = ['store_website_id', 'tool_id', 'database', 'source_url', 'source_title',
        'response_code', 'backlinks_num', 'domains_num', 'last_seen', 'external_num', 'internal_num', ];

    public function indexedPageSemrushApi($domain, $db, $column = null)
    {
        $key  = config('env.SEMRUSH_API');
        $apis = [
            'indexed_page' => 'https://api.semrush.com/analytics/v1/?key=' . $key . '&type=backlinks_pages&target=' . $domain . '&target_type=root_domain&export_columns=source_url,source_title,response_code,backlinks_num,domains_num,last_seen,external_num,internal_num&display_sort=domains_num_desc&display_limit=5',
        ];

        if ($column == null) {
            return $apis;
        } else {
            return $apis[$column];
        }
    }

    public function indexedPageSemrushResponse($column)
    {
        $apisResponse = [
            'indexed_page' => 'source_url;source_title;response_code;backlinks_num;domains_num;last_seen;external_num;internal_num
https://www.searchenginejournal.com/;Search Engine Journal - SEO, Search Marketing News and Tutorials;200;129873;3602;1580113263;16;405
https://www.searchenginejournal.com/;;301;213841;3543;1580400186;0;0
https://www.searchenginejournal.com/seo-101/seo-statistics/;60+ Mind-Blowing Search Engine Optimization Stats - SEO 101;200;11746;1675;1580367611;88;156
https://www.searchenginejournal.com/24-eye-popping-seo-statistics/42665/;;301;3127;822;1579709305;0;0
https://www.searchenginejournal.com/seo-guide/;A Complete Guide to SEO | Search Engine Journal;200;12856;743;1580411596;19;130',

        ];

        return $apisResponse[$column];
    }
}
