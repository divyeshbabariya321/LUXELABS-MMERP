<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DomainSearchKeyword extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'store_website_id', 'tool_id', 'database',
        'subtype',
        'database',
        'keyword',
        'position',
        'previous_position',
        'position_difference',
        'search_volume',
        'cpc',
        'url',
        'traffic_percentage',
        'traffic_cost',
        'competition',
        'number_of_results',
        'trends',
    ];

    public function domainKeywordSearchSemrushApis($domain, $db, $column = null)
    {
        $key    = config('env.SEMRUSH_API');
        $domain = strtolower($domain);
        $apis   = [
            'organic' => 'https://api.semrush.com/?type=domain_organic&key=' . $key . '&display_limit=1&export_columns=Ph,Po,Pp,Pd,Nq,Cp,Ur,Tr,Tc,Co,Nr,Td&domain=' . $domain . '&display_sort=tr_desc&database=' . $db,
            'paid'    => 'https://api.semrush.com/?type=domain_adwords&key=' . $key . '&display_limit=1&export_columns=Ph,Po,Pp,Pd,Nq,Cp,Vu,Tr,Tc,Co,Nr,Td&domain=' . $domain . '&display_sort=po_asc&database=' . $db,
        ];

        if ($column == null) {
            return $apis;
        } else {
            return $apis[$column];
        }
    }

    public function domainKeywordSearchSemrushResponse($column)
    {
        $apisResponse = [
            'organic' => 'Keyword;Position;Previous Position;Position Difference;Search Volume;CPC;Url;Traffic (%);Traffic Cost (%);Competition;Number of Results;Trends
seo;9;10;1;110000;14.82;https://www.seobook.com/;17.53;44.40;0.50;0;0.81,1.00,1.00,1.00,1.00,0.81,0.81,0.81,0.81,0.81,0.81,0.81
seobook;1;1;0;1300;4.54;https://www.seobook.com/;5.52;4.28;0.32;379000;0.62,0.81,0.62,0.81,0.81,0.62,0.62,0.81,0.81,0.62,1.00,0.81
seo tools;6;6;0;8100;10.54;https://tools.seobook.com/;2.15;3.87;0.54;321000000;0.67,0.82,0.82,1.00,0.82,0.82,0.67,0.67,0.67,0.67,0.82,0.82
seo basics;2;2;0;1600;6.84;https://www.seobook.com/learn-seo/seo-basics/;1.10;1.29;0.22;42500000;0.81,1.00,1.00,1.00,1.00,1.00,1.00,1.00,1.00,0.81,1.00,0.81
seo book keyword tool;1;1;0;110;16.73;https://tools.seobook.com/keyword-tools/seobook/;0.46;1.33;0.34;5340000;0.41,0.41,0.52,0.52,0.65,0.82,0.65,1.00,1.00,0.82,0.65,0.41
seo book keyword density;1;1;0;110;6.64;https://tools.seobook.com/general/keyword-density/;0.46;0.53;0.15;539000;0.14,0.43,0.43,0.33,0.33,0.24,0.52,0.67,0.81,0.81,1.00,1.00
free seo tools;6;6;0;1600;7.18;https://tools.seobook.com/;0.42;0.52;0.60;204000000;0.68,0.84,1.00,1.00,1.00,0.84,0.84,1.00,1.00,0.84,1.00,0.84
learn seo;8;8;0;1900;7.76;https://www.seobook.com/learn-seo/;0.30;0.40;0.47;396000000;0.67,0.79,0.79,0.79,0.79,0.79,1.00,0.79,0.79,0.67,0.79,0.67
aaron seo;1;1;0;70;0.00;https://www.seobook.com/;0.29;0.00;0.03;16300000;0.22,0.56,0.11,0.44,0.11,0.11,0.11,0.33,0.11,1.00,0.78,0.22
seo book keyword suggestion tool free download;1;1;0;70;0.00;https://tools.seobook.com/;0.29;0.00;0.14;775000;0.29,1.00,0.00,0.43,0.00,0.00,0.14,0.00,0.14,0.00,0.00,0.43
',

            'paid' => 'Keyword;Position;Previous Position;Position Difference;Search Volume;CPC;Visible Url;Traffic (%);Traffic Cost;Competition;Number of Results;Trends
g tube pads amazon;1;1;0;30;0.36;www.ebay.com/;0.00;0;0.88;3130000;0.14,0.14,0.43,0.14,0.71,0.14,0.57,0.14,1.00,0.14,0.14,0.14
13.8 v power supply;1;1;0;140;1.24;www.ebay.com/;0.00;8;1.00;9750000;0.82,0.82,0.52,1.00,0.82,0.65,0.65,0.65,0.82,0.82,0.82,1.00
ruger 22 250 magazine;1;1;0;10;0.02;www.ebay.com/22+250+magazine;0.00;0;0.64;1370000;1.00,0.60,0.80,0.40,0.20,0.20,0.20,0.20,0.20,0.20,0.20,0.40
nike roshe run woven rainbow buy online;1;1;0;50;0.00;www.ebay.com/;0.00;0;0.00;34;0.00,0.00,0.00,0.00,0.00,0.00,0.00,1.00,0.00,0.00,0.00,0.00
wildside under the influence vinyl;1;1;0;30;0.00;;0.00;0;0.00;83;0.11,0.11,1.00,0.11,0.33,0.22,0.00,0.11,0.00,0.11,0.11,0.11
rca 45 record player;1;1;0;10;0.20;www.ebay.com/;0.00;0;1.00;1170000;0.03,0.13,0.03,0.08,0.03,0.03,0.03,0.08,0.03,0.18,0.18,1.00
uber store;1;1;0;720;2.43;www.ebay.com/Uber+store;0.00;82;0.52;149000000;0.67,0.82,0.55,0.67,0.67,0.67,0.82,1.00,0.82,0.82,0.82,0.82
mouse maze for sale;1;1;0;40;0.16;;0.00;0;1.00;7410000;1.00,0.50,0.50,1.00,0.50,0.50,0.50,1.00,0.50,0.50,0.50,1.00
air britain;1;1;0;140;0.64;www.ebay.com/;0.00;4;0.08;64200000;1.00,1.00,1.00,1.00,0.82,0.82,0.82,1.00,0.82,0.82,0.82,0.53
esoterica stonehaven;1;1;0;140;0.02;;0.00;0;0.08;67;0.81,0.81,0.35,0.65,0.42,0.27,0.27,0.35,0.54,0.54,1.00,0.54',
        ];

        return $apisResponse[$column];
    }

    public function competitorResponse()
    {
        return 'Domain;Competitor Relevance;Common Keywords;Organic Keywords;Organic Traffic;Organic Cost;Adwords Keywords
seochat.com;0.13;338;11021;5640;9690;0
seocentro.com;0.12;237;2196;8091;43478;0
internetmarketingninjas.com;0.12;323;15751;16182;30168;20
webconfs.com;0.12;265;6689;4291;14093;0
link-assistant.com;0.12;326;18255;13089;51583;26
wordtracker.com;0.12;289;7685;11254;51352;1
keywordtool.io;0.11;337;19247;103615;145337;1
kwfinder.com;0.10;259;4885;8260;33067;2
seoreviewtools.com;0.10;240;4537;7181;32502;0
smallseotools.com;0.10;469;21126;299488;450094;10';
    }
}
