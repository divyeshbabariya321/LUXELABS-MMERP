<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Backlink extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'store_website_id', 'tool_id', 'database', 'backlinks_overview', 'backlinks', 'tld_distribution', 'anchors', 'indexed_pages', 'competitors', 'comparison_by_referring_domains', 'batch_comparison', 'authority_score_profile', 'categories_profile', 'categories', 'historical_data',
    ];

    public function backlinkSemrushApis($domain, $db, $column = null)
    {
        $key = config('settings.key');
        $backlinkSemrushApis_api = config('settings.backlinkSemrushApis');
        $apis = [
            'backlinks_overview' => $backlinkSemrushApis_api.$key.'&type=backlinks_overview&target=searchenginejournal.com&target_type=root_domain&export_columns=ascore,total,domains_num,urls_num,ips_num,ipclassc_num,follows_num,nofollows_num,sponsored_num,ugc_num,texts_num,images_num,forms_num,frames_num',

            'backlinks' => $backlinkSemrushApis_api.$key.'&type=backlinks&target=searchenginejournal.com&target_type=root_domain&export_columns=page_ascore,source_title,source_url,target_url,anchor,external_num,internal_num,first_seen,last_seen&display_limit=5',

            'tld_distribution' => $backlinkSemrushApis_api.$key.'&type=backlinks_tld&target=searchenginejournal.com&target_type=root_domain&export_columns=zone,domains_num,backlinks_num&display_limit=5',

            'anchors' => $backlinkSemrushApis_api.$key.'&type=backlinks_anchors&target=searchenginejournal.com&target_type=root_domain&export_columns=anchor,domains_num,backlinks_num,first_seen,last_seen&display_limit=5',

            'indexed_pages' => $backlinkSemrushApis_api.$key.'&type=backlinks_pages&target=searchenginejournal.com&target_type=root_domain&export_columns=source_url,source_title,response_code,backlinks_num,domains_num,last_seen,external_num,internal_num&display_sort=domains_num_desc&display_limit=5',

            'competitors' => $backlinkSemrushApis_api.$key.'&type=backlinks_competitors&target=searchenginejournal.com&target_type=root_domain&export_columns=ascore,neighbour,similarity,common_refdomains,domains_num,backlinks_num&display_limit=5',
            //doubt
            'comparison_by_referring_domains' => $backlinkSemrushApis_api.$key.'&type=backlinks_matrix&targets[]=searchenginejournal.com&targets[]=searchengineland.com&target_types[]=root_domain&target_types[]=root_domain&export_columns=domain,domain_ascore,matches_num,backlinks_num&display_limit=5',

            'batch_comparison' => $backlinkSemrushApis_api.$key.'&type=backlinks_comparison&targets[]=ebay.com&targets[]=amazon.com&target_types[]=root_domain&target_types[]=root_domain&export_columns=target,target_type,ascore,backlinks_num,domains_num,ips_num,follows_num,nofollows_num,texts_num,images_num,forms_num,frames_num',

            'authority_score_profile' => $backlinkSemrushApis_api.$key.'&type=backlinks_ascore_profile&target=searchenginejournal.com&target_type=root_domain',

            'categories_profile' => $backlinkSemrushApis_api.$key.'&type=backlinks_categories_profile&target=searchenginejournal.com&target_type=root_domain&export_columns=category_name,rating&display_limit=5',

            'categories' => $backlinkSemrushApis_api.$key.'&type=backlinks_categories&target=searchenginejournal.com&target_type=root_domain&export_columns=category_name,rating',

            'historical_data' => $backlinkSemrushApis_api.$key.'&type=backlinks_historical&target=searchenginejournal.com&target_type=root_domain&export_columns=date,backlinks_num,domains_num&display_limit=5',
        ];

        if ($column == null) {
            return $apis;
        } else {
            return $apis[$column];
        }
    }

    public function backlinkSemrushResponse($column)
    {
        $apisResponse = [
            'backlinks_overview' => 'ascore;total;domains_num;urls_num;ips_num;ipclassc_num;follows_num;nofollows_num;sponsored_num;ugc_num;texts_num;images_num;forms_num;frames_num
74;22063983;49145;13059030;47793;22956;20457307;1606307;258;1475;21784602;278624;437;320',

            'backlinks' => 'page_ascore;source_title;source_url;target_url;anchor;external_num;internal_num;first_seen;last_seen
88;ChevyBike.com is available at DomainMarket.com. Call 888-694-6735;https://www.domainmarket.com/buynow/chevybike.com;https://www.searchenginejournal.com/how-your-domain-name-will-impact-seo-social-media-marketing;Search Engine Journal;18;41;1560106298;1566808385
87;Colorlib - How To Start A Blog From Scratch Using WordPress;https://colorlib.com/;https://www.searchenginejournal.com/how-your-domain-name-will-impact-seo-social-media-marketing/;a significant impact;22;30;1454645248;1580401052
86;Blogging Fusion - Blog Directory - Article Directory - RSS Directory - Web Directory;https://www.bloggingfusion.com/;https://www.searchenginejournal.com/;Search Engine Journal;49;425;1496340118;1575940029
85;SEO Hacker on Flipboard by Sean Si | Google Lens, WordPress, DuckDuckGo;https://flipboard.com/@seansi/seo-hacker-2leifaa4z;https://www.searchenginejournal.com/wordpress-hackers-are-using-vulnerable-plugins-to-gain-access-to-sites/324171/;searchenginejournal.com - Matt Southern;27;114;1567808179;1572994828
84;Link exchange;https://www.backlinks-exchange.net/;https://www.searchenginejournal.com/ultimate-guide/321125/;The Ultimate Guide to Creating a True ‘Ultimate Guide’ - Search Engine Journal;52;287;1567704395;1571725665',

            'tld_distribution' => 'zone;domains_num;backlinks_num
com;27755;11645051
net;1894;1684571
org;1486;800180
uk;1267;22572
au;645;9531',

            'anchors' => 'anchor;domains_num;backlinks_num;first_seen;last_seen
search engine journal;8113;691263;1370650463;1580411804
93% of people;3;354284;1524707034;1580411673
the growth of social media v2.0 | search engine journal;1;251996;1532739198;1578767338
more;57;153884;1452198531;1580411620
read more >;2;114350;1545826610;1580411612',

            'indexed_pages' => 'source_url;source_title;response_code;backlinks_num;domains_num;last_seen;external_num;internal_num
https://www.searchenginejournal.com/;Search Engine Journal - SEO, Search Marketing News and Tutorials;200;129873;3602;1580113263;16;405
https://www.searchenginejournal.com/;;301;213841;3543;1580400186;0;0
https://www.searchenginejournal.com/seo-101/seo-statistics/;60+ Mind-Blowing Search Engine Optimization Stats - SEO 101;200;11746;1675;1580367611;88;156
https://www.searchenginejournal.com/24-eye-popping-seo-statistics/42665/;;301;3127;822;1579709305;0;0
https://www.searchenginejournal.com/seo-guide/;A Complete Guide to SEO | Search Engine Journal;200;12856;743;1580411596;19;130',

            'competitors' => 'ascore;neighbour;similarity;common_refdomains;domains_num;backlinks_num
80;searchengineland.com;36;17584;79939;42840590
74;searchenginewatch.com;34;11537;47115;35855777
68;wordstream.com;32;9575;37065;1750926
77;moz.com;31;15732;103754;21136846
76;marketingland.com;30;9058;39986;9756098',
            //doubt
            'comparison_by_referring_domains' => 'domain;domain_ascore;matches_num;searchenginejournal.com;searchengineland.com
google.com;98;2;100;92
squarespace.com;85;1;0;4
cloudflare.com;92;1;0;8
wordpress.org;95;2;378;1158
microsoft.com;94;2;52;85',

            'batch_comparison' => 'target;target_type;ascore;backlinks_num;domains_num;ips_num;follows_num;nofollows_num;texts_num;images_num;forms_num;frames_num
ebay.com;root_domain;94;15248332274;461273;321889;6863043986;8385235217;11753970129;3487503037;6183483;675625
amazon.com;root_domain;94;6258027263;2679680;1012020;3901022285;2355705949;4522715595;1637657601;14954399;82699668',

            'authority_score_profile' => 'ascore;domains_num
0;941
1;60
2;114
3;227
4;433
5;810
...
95;2
96;0
97;1
98;1
99;0
100;0',

            'categories_profile' => 'category_name;rating
/Business & Industrial/Advertising & Marketing/Marketing;2188
/Internet & Telecom/Web Services/Search Engine Optimization & Marketing;1975
/Business & Industrial/Advertising & Marketing/Brand Management;1725
/Business & Industrial/Advertising & Marketing/Sales;1116
/Internet & Telecom/Web Services/Web Design & Development;1001',

            'categories' => 'category_name;rating
/Internet & Telecom/Web Services/Search Engine Optimization & Marketing;0.931905
/Internet & Telecom/Web Services/Affiliate Programs;0.880989
/Business & Industrial/Advertising & Marketing/Marketing;0.872495
/Internet & Telecom/Search Engines;0.821398
/Business & Industrial/Advertising & Marketing/Brand Management;0.813207',

            'historical_data' => 'date;backlinks_num;domains_num
1618185600;18768868;266988
1617580800;19005841;270238
1616976000;19145818;270371
1616371200;20011497;309865
1615766400;20669614;383991',
        ];

        return $apisResponse[$column];
    }

    public function backlinkAhrefsApis($domain, $db, $column = null)
    {
        $apis = [

            'backlinks' => 'https://apiv2.ahrefs.com?from=backlinks&target=ahrefs.com&mode=domain&limit=2&order_by=ahrefs_rank%3Adesc&output=json',

            'anchors' => 'https://apiv2.ahrefs.com?from=anchors&target=ahrefs.com&mode=domain&limit=2&output=json',

            'indexed_pages' => 'https://apiv2.ahrefs.com?from=pages&target=ahrefs.com&mode=domain&limit=2&output=json',
        ];

        if ($column == null) {
            return $apis;
        } else {
            return $apis[$column];
        }
    }

    public function backlinkAhrefsResponse($column)
    {
        $apisResponse = [
            'backlinks' => '{
        "refpages": [
            {
                "url_to": "https://ahrefs.com/serp-checker",
                "url_from": "https://www.aletenky.cz/",
                "ahrefs_rank": 21,
                "domain_rating": 48,
                "ahrefs_top": 2348,
                "ip_from": "46.51.179.2",
                "links_internal": 0,
                "links_external": 1,
                "page_size": 199,
                "encoding": "raw",
                "language": "",
                "title": "301 Moved Permanently",
                "first_seen": "2013-10-26T20:19:04Z",
                "last_visited": "2013-11-08T19:25:22Z",
                "prev_visited": "2013-11-07T06:58:50Z",
                "original": true,
                "link_type": "redirect",
                "redirect": 301,
                "nofollow": false,
                "alt": "",
                "anchor": "",
                "text_pre": "",
                "text_post": "",
                "http_code": 200,
                "url_from_first_seen": "2013-11-07T06:58:50Z"
            },
            {
                "url_to": "https://ahrefs.com/",
                "url_from": "https://oni.toypark.in/",
                "ahrefs_rank": 16,
                "domain_rating": 62,
                "ahrefs_top": 456783,
                "ip_from": "183.177.133.249",
                "links_internal": 84,
                "links_external": 18,
                "page_size": 9529,
                "encoding": "utf8",
                "language": "ja",
                "title": "SEOの鬼サーチ ブログ版 | ディレクトリに登録されているサイトギャラリー",
                "first_seen": "2013-10-27T20:42:14Z",
                "last_visited": "2013-11-05T21:55:32Z",
                "prev_visited": "2013-11-01T17:09:20Z",
                "original": true,
                "link_type": "href",
                "redirect": 0,
                "nofollow": false,
                "alt": "",
                "anchor": "ahrefs",
                "text_pre": "",
                "text_post": "",
                "http_code": 200,
                "url_from_first_seen": "2013-11-07T06:58:50Z"
            }
        ]
    }',

            'anchors' => '{
    "anchors": [
        {
            "anchor": "$11.99/month",
            "backlinks": 2,
            "refpages": 2,
            "refdomains": 1,
            "first_seen": "2013-08-08T19:04:00Z",
            "last_visited": "2013-08-08T20:18:13Z"
        },
        {
            "anchor": "$79/month to $499/month",
            "backlinks": 2,
            "refpages": 2,
            "refdomains": 2,
            "first_seen": "2013-09-28T18:18:27Z",
            "last_visited": "2013-10-03T05:23:31Z"
        }
    ],
    "stats": {
        "backlinks": 2574830,
        "refpages": 2560709
    }
}',

            'indexed_pages' => '{
    "pages": [
        {
            "url": "https://ahrefs.com/",
            "ahrefs_rank": 85,
            "first_seen": "2013-01-02T09:46:29Z",
            "last_visited": "2013-11-20T09:46:29Z",
            "http_code": 200,
            "size": 9211,
            "links_internal": 26,
            "links_external": 4,
            "encoding": "utf8",
            "title": "Ahrefs Site Explorer & Backlink Checker",
            "redirect_url": "",
            "content_encoding": "gzip"
        },
        {
            "url": "https://ahrefs.com/",
            "ahrefs_rank": 81,
            "first_seen": "2013-01-03T16:50:51Z",
            "last_visited": "2013-11-23T16:50:51Z",
            "http_code": 301,
            "size": 20,
            "links_internal": 1,
            "links_external": 0,
            "encoding": "raw",
            "title": "",
            "redirect_url": "https://ahrefs.com/",
            "content_encoding": "gzip"
        }
    ],
    "stats": {
        "pages": 26699
    }
}',
        ];

        return $apisResponse[$column];
    }
}
