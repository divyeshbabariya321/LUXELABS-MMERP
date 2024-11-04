<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DomainOrganicPage extends Model
{
    protected $table = 'domain_organic_page';

    protected $fillable = ['store_website_id', 'tool_id', 'database', 'url', 'number_of_keywords', 'traffic', 'traffic_percentage'];

    public function organicPageSemrushApi($domain, $db, $column = null)
    {
        $key  = config('env.SEMRUSH_API');
        $apis = [
            'organic_page' => 'https://api.semrush.com/?type=domain_organic_unique&key=' . $key . '&display_filter=%2B%7CPc%7CGt%7C100&display_limit=10&export_columns=Ur,Pc,Tg,Tr&domain=' . $domain . '&display_sort=tr_desc&database=us',
        ];

        if ($column == null) {
            return $apis;
        } else {
            return $apis[$column];
        }
    }

    public function organicPageSemrushResponse($column)
    {
        $apisResponse = [
            'organic_page' => 'Url;Number of Keywords;Traffic;Traffic (%)
https://www.seobook.com/;317;2488;15.14
https://tools.seobook.com/meta-medic/;492;1289;7.84
https://tools.seobook.com/robots-txt/generator/;197;1133;6.89
https://tools.seobook.com/;588;1015;6.17
https://tools.seobook.com/ppc-tools/free-ppc-ad-coupons.html;930;916;5.57
https://tools.seobook.com/general/keyword-density/;417;794;4.83
https://tools.seobook.com/keyword-tools/seobook/;262;729;4.43
https://tools.seobook.com/server-header-checker/;322;722;4.39
https://tools.seobook.com/robots-txt/;319;583;3.54
https://tools.seobook.com/keyword-list/generator.php;103;469;2.85',

        ];

        return $apisResponse[$column];
    }
}
