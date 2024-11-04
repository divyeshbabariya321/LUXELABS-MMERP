<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TrafficAnalyticsReport extends Model
{
    protected $table = 'trafficanalitics_reports';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'store_website_id', 'tool_id', 'database', 'traffic_summary', 'traffic_sources', 'traffic_destinations', 'geo_distribution', 'subdomains', 'top_pages', 'domain_rankings', 'audience_insights', 'data_accuracy',
    ];

    public function trafficanaliticsReportSemrushApis($domain, $db, $column = null)
    {
        $key  = config('settings.key');
        $apis = [
            'traffic_summary' => 'https://api.semrush.com/analytics/ta/api/v3/summary?targets=golang.org,blog.golang.org,tour.golang.org/welcome/&export_columns=target,visits,users&key=' . $key,

            'traffic_sources' => 'https://api.semrush.com/analytics/ta/api/v3/sources?target=medium.com&device_type=mobile&display_limit=5&display_offset=0&country=us&sort_order=traffic_diff&traffic_channel=referral&traffic_type=organic&display_date=2020-06-01&export_columns=target,from_target,display_date,country,traffic_share,traffic,channel&key=' . $key,

            'traffic_destinations' => 'https://api.semrush.com/analytics/ta/api/v3/destinations?target=mail.ru&device_type=desktop&display_limit=5&display_offset=0&country=us&export_columns=target,display_date,country,device_type,to_target,traffic_share,traffic&display_date=2020-06-01&key=' . $key,

            'geo_distribution' => 'https://api.semrush.com/analytics/ta/api/v3/geo?display_date=2020-01-01&device_type=desktop&display_limit=7&display_offset=0&target=ebay.com&target_type=domain&geo_type=country&export_columns=target,display_date,device_type,geo,traffic,avg_visit_duration&key=' . $key,

            'subdomains' => 'https://api.semrush.com/analytics/ta/api/v3/subdomains?target=amazon.com&export_columns=domain,display_date,subdomain,total_hits&country=us&display_date=2019-07-01&device_type=desktop&display_limit=3&display_offset=3&key=' . $key,

            'top_pages' => 'https://api.semrush.com/analytics/ta/api/v3/toppages?device_type=desktop&display_date=2020-06-01&country=us&display_limit=5&display_offset=0&target=amazon.com&target_type=domain&export_columns=page,display_date,desktop_share,mobile_share&key=' . $key,

            'domain_rankings' => 'https://api.semrush.com/analytics/ta/api/v3/rank?device_type=mobile&display_date=2020-05-01&country=us&display_limit=5&display_offset=0&export_columns=rank,domain&key=' . $key,

            'audience_insights' => 'https://api.semrush.com/analytics/ta/api/v3/audience_insights?display_date=2020-02-01&device_type=desktop&country=us&segment=contains&targets=amazon.com,ebay.com,searchenginesland.com&selected_targets=amazon.com,ebay.com&export_columns=target,overlap_score,similarity_score,target_users,overlap_users&display_offset=5&display_limit=7&key=' . $key,

            'data_accuracy' => 'https://api.semrush.com/analytics/ta/api/v3/accuracy?display_date=2019-01-01&target=ebay.com&country=us&device_type=desktop&export_columns=target,display_date,country,device_type,accuracy&key=' . $key,
        ];

        if ($column == null) {
            return $apis;
        } else {
            return $apis[$column];
        }
    }

    public function trafficanaliticsReportSemrushResponse($column)
    {
        $apisResponse = [
            'traffic_summary' => 'target;visits;users
golang.org;4491179;1400453
blog.golang.org;402104;204891
tour.golang.org/welcome/;10131;11628',

            'traffic_sources' => 'target;from_target;display_date;country;traffic_share;traffic;channel
medium.com;phlap.net;2020-06-01;US;0.00019134;7025;referral
medium.com;blackhatworld.com;2020-06-01;US;0.00006379;2342;referral
medium.com;crunchyroll.com;2020-06-01;US;0.00005102;1873;referral
medium.com;outline.com;2020-06-01;US;0.00005102;1873;referral
medium.com;uber.com;2020-06-01;US;0.00005102;1873;referral',

            'traffic_destinations' => 'target;display_date;country;device_type;to_target;traffic_share;traffic
mail.ru;2020-06-01;US;desktop;ok.ru;0.14817627;237336
mail.ru;2020-06-01;US;desktop;turkishairlines.com;0.07261596;116310
mail.ru;2020-06-01;US;desktop;airastana.com;0.05397156;86447
mail.ru;2020-06-01;US;desktop;vazhno.ru;0.02943909;47153
mail.ru;2020-06-01;US;desktop;belavia.by;0.0206073;33007',

            'geo_distribution' => 'target;display_date;device_type;geo;traffic;avg_visit_duration
ebay.com;2020-01-01;desktop;us;192581931;706
ebay.com;2020-01-01;desktop;ru;7305169;970
ebay.com;2020-01-01;desktop;ca;6392463;819
ebay.com;2020-01-01;desktop;il;5099407;1048
ebay.com;2020-01-01;desktop;mx;4277849;669
ebay.com;2020-01-01;desktop;br;3811888;711
ebay.com;2020-01-01;desktop;gb;3641529;384',

            'subdomains' => 'domain;display_date;subdomain;total_hits
amazon.com;2019-07-01;twitch.amazon.com;24274866
amazon.com;2019-07-01;sellercentral.amazon.com;50300062
amazon.com;2019-07-01;aws.amazon.com;14274172',

            'top_pages' => 'page;display_date;desktop_share;mobile_share
amazon.com/s;2020-06-01;1;0
amazon.com;2020-06-01;0.2545288066748602;0.7454711933251398
amazon.com/gp/css/order-history;2020-06-01;1;0
amazon.com/s/ref=nb_sb_noss_2;2020-06-01;1;0
amazon.com/gp/product/handle-buy-box/ref=dp_start-bbf_1_glance;2020-06-01;1;0',

            'domain_rankings' => 'rank;domain
1;google.com
2;facebook.com
3;wikipedia.org
4;amazon.com
5;yahoo.com',

            'audience_insights' => 'target;overlap_score;similarity_score;target_users;overlap_users
instagram.com;0.3688;0.4891;69429930;50399700
reddit.com;0.3467;0.4515;73201944;47379108
twitter.com;0.3467;0.4587;69915496;47372776
ebay.com;0.2448;0.3933;33448824;33448824
imdb.com;0.239;0.3621;43723270;32654776
apple.com;0.2326;0.3496;45222470;31789886
yahoo.com;0.2221;0.3242;50563124;30347980',

            'data_accuracy' => 'target;display_date;country;device_type;accuracy
ebay.com;2019-01-01;US;desktop;3',
        ];

        return $apisResponse[$column];
    }

    public function trafficanaliticsReportAhrefsApis($domain, $db, $column = null)
    {
        $key  = config('settings.key');
        $apis = [
            'traffic_summary' => '',

            'traffic_sources' => '',

            'traffic_destinations' => '',

            'geo_distribution' => '',

            'subdomains' => '',

            'top_pages' => '',

            'domain_rankings' => 'https://apiv2.ahrefs.com?from=domain_rating&target=ahrefs.com&mode=domain&output=json',

            'audience_insights' => '',

            'data_accuracy' => '',
        ];

        if ($column == null) {
            return $apis;
        } else {
            return $apis[$column];
        }
    }

    public function trafficanaliticsReportAhrefsResponse($column)
    {
        $apisResponse = [
            'traffic_summary' => '',

            'traffic_sources' => '',

            'traffic_destinations' => '',

            'geo_distribution' => '',

            'subdomains' => '',

            'top_pages'       => '',
            'domain_rankings' => '{
    "domain": {
        "domain_rating": 66,
        "ahrefs_top": 3840
    }
}',
            'audience_insights' => '',

            'data_accuracy' => '',
        ];

        return $apisResponse[$column];
    }
}
