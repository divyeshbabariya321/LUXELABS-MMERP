<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UrlReport extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'store_website_id', 'tool_id', 'database', 'url_organic_search_keywords', 'url_paid_search_keywords',
    ];

    public function urlReportSemrushApis($domain, $db, $column = null)
    {
        $key  = config('settings.key');
        $apis = [
            'url_organic_search_keywords' => 'https://api.semrush.com/?type=url_organic&key=' . $key . '&display_limit=10&export_columns=Ph,Po,Nq,Cp,Co,Tr,Tc,Nr,Td&url=https://tools.seobook.com/&database=' . $db,

            'url_paid_search_keywords' => 'https://api.semrush.com/?type=url_adwords&key=' . $key . '&display_limit=5&export_columns=Ph,Po,Nq,Cp,Co,Tr,Tc,Nr,Td,Tt,Ds&url=https://www.amazon.com/&database=' . $db,
        ];

        if ($column == null) {
            return $apis;
        } else {
            return $apis[$column];
        }
    }

    public function urlReportSemrushResponse($column)
    {
        $apisResponse = [
            'url_organic_search_keywords' => 'Keyword;Position;Search Volume;CPC;Competition;Traffic (%);Traffic Cost;Number of Results;Trends
seo tools;4;8100;10.54;0.54;3.21;5976;226000000;0.67,0.82,0.82,1.00,0.82,0.82,0.67,0.67,0.67,0.67,0.82,0.82
free seo tools;6;1600;7.18;0.60;0.45;574;209000000;0.68,0.84,1.00,1.00,1.00,0.84,0.84,1.00,1.00,0.84,1.00,0.84
seo book keyword suggestion tool free download;1;70;0.00;0.14;0.31;0;775000;0.29,1.00,0.00,0.43,0.00,0.00,0.14,0.00,0.14,0.00,0.00,0.43
seo tools search engine software;2;210;0.00;0.02;0.15;0;37600000;0.54,1.00,1.00,0.81,0.65,0.81,1.00,0.81,0.81,0.81,0.54,0.35
tools seobook;1;30;16.96;0.29;0.13;407;162000;0.27,0.08,0.04,0.04,0.04,0.04,0.04,0.04,0.54,1.00,1.00,0.04
seo tools software;7;480;5.66;0.11;0.10;108;152000000;0.54,0.81,0.81,0.81,0.81,1.00,1.00,1.00,0.66,0.81,1.00,0.54
free seo;7;480;4.09;0.54;0.10;78;727000000;0.81,1.00,0.81,1.00,1.00,0.66,0.81,0.81,0.81,0.81,1.00,1.00
search engine optimization tools;4;260;9.58;0.45;0.10;174;80600000;1.00,0.81,0.66,0.81,0.66,0.66,0.81,0.81,0.81,0.66,1.00,1.00
seo optimization tools;4;210;8.21;0.33;0.07;120;56300000;0.81,1.00,0.81,0.81,0.81,0.81,0.65,0.81,0.65,0.65,0.81,1.00
seo optimization software;6;210;21.58;0.13;0.05;226;59100000;0.54,0.81,0.54,0.54,0.81,0.81,0.81,0.54,0.65,0.81,1.00,1.00',

            'url_paid_search_keywords' => "Keyword;Position;Search Volume;CPC;Competition;Traffic (%);Traffic Cost;Number of Results;Trends;Title;Description
amazon;1;83100000;0.02;0.16;0.68;78114;81;1.00,0.67,0.67,0.67,0.67,0.67,0.67,0.81,0.67,0.67,0.67,0.81;Amazon.com Official Site | Huge Selection & Great Prices;Free Two-Day Shipping with Prime. Read Ratings & Reviews. Try Prime for Free. Explore Amazon Devices. Shop Best Sellers & Deals. Save with Our Low Prices. Shop Our Huge Selection. Fast Shipping.
amazon;1;83100000;0.02;0.16;0.68;78114;75;1.00,0.67,0.67,0.67,0.67,0.67,0.67,0.81,0.67,0.67,0.67,0.81;Amazon.com Official Site | Huge Selection & Great Prices;Free Two-Day Shipping with Prime. Shop Our Huge Selection. Try Prime for Free.
amazon;1;83100000;0.02;0.16;0.68;78114;2680000000;1.00,0.67,0.67,0.67,0.67,0.67,0.67,0.81,0.67,0.67,0.67,0.81;Amazon.com | Amazon® Official Site | Huge Selection & Great Prices;Free Two-Day Shipping with Prime. Explore Amazon Devices. Shop Our Huge Selection. Read Ratings & Reviews. Try Prime for Free. Fast Shipping. Save with Our Low Prices.
amazon;1;83100000;0.02;0.16;0.68;78114;84;1.00,0.67,0.67,0.67,0.67,0.67,0.67,0.81,0.67,0.67,0.67,0.81;Amazon® Official Site | Huge Selection & Great Prices;Free Two-Day Shipping with Prime. Shop Our Huge Selection. Fast Shipping. Read Ratings & Reviews. Shop Best Sellers & Deals. Stream Videos Instantly. Save with Our Low Prices.
amazon;1;83100000;0.02;0.16;0.68;78114;76;1.00,0.67,0.67,0.67,0.67,0.67,0.67,0.81,0.67,0.67,0.67,0.81;Amazon.com Official Site | Free 2-Day Shipping with Prime;Earth's biggest selection of books, electronics, apparel & more at low prices.",
        ];

        return $apisResponse[$column];
    }

    public function urlReportAhrefsApis($column = null)
    {
        $key  = config('settings.key');
        $apis = [
            'url_organic_search_keywords' => '',

            'url_paid_search_keywords' => '',
        ];

        if ($column == null) {
            return $apis;
        } else {
            return $apis[$column];
        }
    }

    public function urlReportAhrefsResponse($column)
    {
        $apisResponse = [
            'url_organic_search_keywords' => '',

            'url_paid_search_keywords' => '',
        ];

        return $apisResponse[$column];
    }
}
