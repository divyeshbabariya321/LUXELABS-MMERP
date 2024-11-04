<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class KeywordReport extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'store_website_id', 'tool_id', 'database', 'keyword_overview_all_database', 'keyword_overview_one_database', 'batch_keyword_overview_one_database', 'organic_results', 'paid_results', 'related_keyword', 'keyword_ads_history', 'broad_match_keywords', 'phrase_questions', 'keyword_difficulty',
    ];

    public function keywordReportSemrushApis($domain, $db, $column = null)
    {
        $key  = config('settings.key');
        $apis = [
            'keyword_overview_all_database' => 'https://api.semrush.com/?type=phrase_all&key=' . $key . '&phrase=seo&export_columns=Dt,Db,Ph,Nq,Cp,Co,Nr',

            'keyword_overview_one_database' => 'https://api.semrush.com/?type=phrase_this&key=' . $key . '&phrase=seo&export_columns=Ph,Nq,Cp,Co,Nr,Td&database=' . $db,

            'batch_keyword_overview_one_database' => 'https://api.semrush.com/?type=phrase_these&key=' . $key . '&phrase=ebay;seo&export_columns=Ph,Nq,Cp,Co,Nr,Td&database=' . $db,

            'organic_results' => 'https://api.semrush.com/?type=phrase_organic&key=' . $key . '&phrase=seo&export_columns=Dn,Ur,Fk,Fp&database=' . $db . '&display_limit=10',

            'paid_results' => 'https://api.semrush.com/?type=phrase_adwords&key=' . $key . '&phrase=seo&export_columns=Dn,Ur,Vu&database=' . $db . '&display_limit=10',

            'related_keyword' => 'https://api.semrush.com/?type=phrase_related&key=' . $key . '&phrase=seo&export_columns=Ph,Nq,Cp,Co,Nr,Td,Rr,Fk&database=' . $db . '&display_limit=10&display_sort=nq_desc&display_filter=%2B|Nq|Lt|1000',

            'keyword_ads_history' => 'https://api.semrush.com/?type=phrase_adwords_historical&key=' . $key . '&display_limit=1&export_columns=Dn,Dt,Po,Ur,Tt,Ds,Vu&phrase=movie&database=' . $db,

            'broad_match_keywords' => 'https://api.semrush.com/?type=phrase_fullsearch&key=' . $key . '&phrase=seo&export_columns=Ph,Nq,Cp,Co,Nr,Td,Fk&database=' . $db . '&display_limit=10&display_sort=nq_desc&display_filter=%2B|Nq|Lt|1000',

            'phrase_questions' => 'https://api.semrush.com/?type=phrase_questions&key=' . $key . '&phrase=seo&export_columns=Ph,Nq,Cp,Co,Nr,Td&database=' . $db . '&display_limit=10&display_sort=nq_desc&display_filter=%2B|Nq|Lt|1000',

            'keyword_difficulty' => 'https://api.semrush.com/?type=phrase_kdi&key=' . $key . '&export_columns=Ph,Kd&phrase=ebay;seo&database=' . $db,
        ];

        if ($column == null) {
            return $apis;
        } else {
            return $apis[$column];
        }
    }

    public function keywordReportSemrushResponse($column)
    {
        $apisResponse = [
            'keyword_overview_all_database' => 'Date;Database;Keyword;Search Volume;CPC;Competition
201903;bo;seo;390;0.44;0.03
201903;hu;seo;1900;0.82;0.45
201903;th;seo;5400;0.96;0.49
201903;cr;seo;590;0.43;0.14',

            'keyword_overview_one_database' => 'Keyword;Search Volume;CPC;Competition;Number of Results;Trends
seo;110000;14.82;0.5;678000000;0.81,1.00,1.00,1.00,1.00,0.81,0.81,0.81,0.81,0.81,0.81,0.81',

            'batch_keyword_overview_one_database' => 'Keyword;Search Volume;CPC;Competition;Number of Results
ebay;45500000;0.54;0.01;1880000000
seo;110000;14.82;0.5;678000000',

            'organic_results' => 'Domain;Url;Keywords SERP Features;SERP Features
moz.com;https://moz.com/beginners-guide-to-seo;1;6
moz.com;https://moz.com/learn/seo/what-is-seo;1;
searchengineland.com;https://searchengineland.com/guide/what-is-seo;1;
google.com;https://developers.google.com/search/docs/beginner/seo-starter-guide;1;6
neilpatel.com;https://neilpatel.com/what-is-seo/;1;
wikipedia.org;https://en.wikipedia.org/wiki/Search_engine_optimization;1;
wordstream.com;https://www.wordstream.com/seo;1;
wordstream.com;https://www.wordstream.com/blog/ws/2015/04/30/seo-basics;1;
ahrefs.com;https://ahrefs.com/blog/seo-basics/;1;
searchenginewatch.com;https://www.searchenginewatch.com/2016/01/21/seo-basics-22-essentials-you-need-for-optimizing-your-site/;1;',

            'paid_results' => 'Domain;Url;Visible Url
wix.com;https://www.wix.com/;www.wix.com/
webcreationus.com;https://amp.webcreationus.com/google/seo;amp.webcreationus.com/google/seo
wix.com;https://www.wix.com/;www.wix.com/
wix.com;https://www.wix.com/;www.wix.com/
brunnerworks.com;https://www.brunnerworks.com/;www.brunnerworks.com/
wix.com;https://www.wix.com/;www.wix.com/
brunnerworks.com;https://www.brunnerworks.com/;www.brunnerworks.com/
wix.com;https://www.wix.com/;www.wix.com/
rankingcoach.com;https://www.rankingcoach.com/;www.rankingcoach.com/
hinadm.com;https://www.hinadm.com/;www.hinadm.com/',

            'related_keyword' => 'Keyword;Search Volume;CPC;Competition;Number of Results;Trends;Related Relevance;SERP Features
beginners guide;880;0;0.01;1750000000;0.82,1.00,0.82,1.00,0.82,0.82,0.67,0.82,1.00,1.00,1.00,1.00;0.05;1,6,7,20,21
best po sites;880;0.14;0.38;1040000000;0.67,0.67,0.67,1.00,0.67,0.20,0.20,0.25,0.25,0.04,0.01,0.02;0.05;15,20,21
best seo;880;6.55;0.33;631000000;0.45,0.77,1.00,0.45,0.77,0.77,1.00,0.68,0.45,0.55,0.68,0.55;0.15;4,6,11,22
how seo works;880;3.93;0.28;164000000;0.55,1.00,0.77,0.77,0.68,0.45,0.45,0.45,0.45,0.45,0.77,0.68;0.15;6,11,20,21,22
how to improve seo;880;2.9;0.34;233000000;1.00,0.88,0.88,0.88,0.88,0.88,0.72,0.72,0.72,0.72,1.00,0.88;0.1;11,20,21
page two of google;880;0;0;7640000000;0.00,0.00,0.00,0.00,0.00,0.00,1.00,0.36,0.20,0.13,0.55,0.13;0.05;5,9,21
search e;880;2.3;0.1;24160000000;0.88,0.72,0.88,0.72,0.72,0.72,0.88,1.00,0.88,1.00,0.88,0.72;0.05;4,6,8
search engine optimization definition;880;5.2;0.09;29800000;0.88,0.88,1.00,0.72,0.39,0.39,0.59,0.88,1.00,0.88,0.88,0.88;0.45;0,1,6,15,20,21,22
seo for beginners;880;2.45;0.29;18500000;0.68,0.68,1.00,1.00,0.77,0.77,0.55,0.45,0.45,0.45,0.55,0.68;0.3;6,11,20,21,22
seo for dummies;880;1.93;0.98;2850000;0.68,1.00,1.00,0.68,0.77,0.68,0.68,0.68,0.55,0.55,0.77,0.68;0.05;1,6,7,20,21',

            'keyword_ads_history' => 'Domain;Date;Position;Url;Title;Description;Visible Url
blendedmovie.com;20140515;1;47.xg4ken.com/media/redir.php%3Fprof%3D626%26camp%3D58398%26affcode%3Dkw599%26cid%3D36205335494%26networkType%3Dsearch%26kdv%3Dc%26url%5B%5D%3Dhttp%253A%252F%252Fblendedmovie.com%252F%2523home&ved=0CBoQ0Qw;Blended Movie - blendedmovie.com;A wildly different family vacation. Out 5/23. Buy Tickets Today!;www.blendedmovie.com/
blendedmovie.com;20140415;;;;;
blendedmovie.com;20140315;;;;;
blendedmovie.com;20140215;;;;;
blendedmovie.com;20140115;;;;;
blendedmovie.com;20131215;;;;;
blendedmovie.com;20131115;;;;;
blendedmovie.com;20131015;;;;;
blendedmovie.com;20130915;;;;;
blendedmovie.com;20130815;;;;;
blendedmovie.com;20130715;;;;;
blendedmovie.com;20130615;;;;;',

            'broad_match_keywords' => 'Keyword;Search Volume;CPC;Competition;Number of Results;Trends;SERP Features
affordable seo;880;13.91;0.22;29900000;0.45,0.55,0.30,0.45,0.81,1.00,0.63,0.45,0.55,0.63,0.55,0.45;6,7,22
affordable seo services;880;15.67;0.11;24000000;0.37,0.55,0.25,0.25,0.68,1.00,1.00,0.68,1.00,1.00,0.55,0.45;6,11,15,22
arizona seo;880;6.05;0.02;15900000;0.72,0.72,0.88,0.59,1.00,0.88,1.00,1.00,0.88,0.88,0.59,0.59;3,20
atlanta seo expert;880;17.99;0.13;4090000;0.25,1.00,1.00,0.20,0.20,0.20,0.30,0.20,0.16,0.11,0.09,0.11;6,7,9,20
best seo;880;6.55;0.33;631000000;0.77,1.00,0.45,0.77,0.77,1.00,0.68,0.45,0.55,0.68,0.55,0.55;4,6,11,22
best seo plugin for wordpress;880;2.74;0.1;11700000;0.77,0.68,0.55,0.55,0.68,0.55,0.55,0.55,0.55,1.00,0.77,0.77;11,20,21,22
best seo plugin wordpress;880;3.09;0.09;12800000;0.68,0.68,0.77,0.68,0.55,0.55,0.68,0.55,0.55,0.55,0.55,1.00;11,20,21,22
boca raton seo;880;7.58;0.11;2770000;1.00,1.00,0.25,0.16,0.37,0.30,0.20,0.11,0.11,0.16,0.16,0.16;3,5,6,20
boston seo agency;880;15.21;0.1;8630000;0.20,1.00,1.00,0.07,0.06,0.13,0.16,0.11,0.07,0.09,0.07,0.07;3,5,20,22
boston seo expert;880;12.73;0.04;4380000;0.25,1.00,1.00,0.13,0.13,0.11,0.45,0.20,0.13,0.13,0.09,0.09;6,9,20,22',

            'phrase_questions' => 'Keyword;Search Volume;CPC;Competition;Number of Results;Trends
how to seo;880;5.23;0.16;611000000;0.88,1.00,1.00,1.00,0.88,0.88,0.88,0.88,0.88,0.88,0.88,0.88
how does seo work;590;3.6;0.09;183000000;0.67,0.82,0.82,1.00,0.82,0.82,0.82,0.82,0.82,0.82,1.00,0.67
how to improve seo;590;7.11;0.4;135000000;0.82,0.82,1.00,0.82,0.82,1.00,0.82,0.82,0.82,0.82,1.00,0.82
what is seo and how it works;590;5.69;0.18;188000000;0.44,0.54,0.82,1.00,1.00,0.67,0.54,0.67,0.67,0.82,1.00,1.00
how seo works;480;6.62;0.24;163000000;0.81,1.00,1.00,1.00,1.00,0.81,0.81,0.81,0.81,0.66,1.00,1.00
kim min seo;480;0;0;51100000;0.55,0.55,0.36,0.36,0.36,0.55,0.44,0.67,0.44,0.55,1.00,0.67
what is seo writing;480;2.16;0.12;158000000;0.66,1.00,0.66,0.81,1.00,1.00,0.81,1.00,1.00,1.00,0.81,0.66
how much does seo cost;390;7.44;0.31;80300000;0.81,0.81,0.81,0.81,0.81,1.00,0.81,0.67,0.67,0.67,0.81,0.67
kim seo hyung;390;0;0;9000000;0.54,0.67,0.54,0.54,0.54,1.00,0.81,0.81,0.67,0.67,0.81,1.00
ko seo hyun;390;0;0.01;0;0.04,0.06,0.07,0.07,0.04,0.13,0.30,0.09,0.81,1.00,0.45,0.13',

            'keyword_difficulty' => 'Keyword;Keyword Difficulty Index

ebay;95.10

seo;78.35',
        ];

        return $apisResponse[$column];
    }

    public function keywordReportAhrefsApis($domain, $db, $column = null)
    {
        $key  = config('settings.key');
        $apis = [
            'keyword_overview_all_database' => '',

            'keyword_overview_one_database' => '',

            'batch_keyword_overview_one_database' => '',

            'organic_results' => '',

            'paid_results' => '',

            'related_keyword' => '',

            'keyword_ads_history' => '',

            'broad_match_keywords' => '',

            'phrase_questions' => '',

            'keyword_difficulty' => '',
        ];

        if ($column == null) {
            return $apis;
        } else {
            return $apis[$column];
        }
    }

    public function keywordReportAhrefsResponse($column)
    {
        $apisResponse = [
            'keyword_overview_all_database' => '',

            'keyword_overview_one_database' => '',

            'batch_keyword_overview_one_database' => '',

            'organic_results' => '',

            'paid_results' => '',

            'related_keyword' => '',

            'keyword_ads_history' => '',

            'broad_match_keywords' => '',

            'phrase_questions' => '',

            'keyword_difficulty' => '',
        ];

        return $apisResponse[$column];
    }
}
