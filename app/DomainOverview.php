<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DomainOverview extends Model
{
    protected $fillable = [
        'tool_id', 'store_website_id', 'database', 'rank', 'organic_keywords', 'organic_traffic', 'organic_cost', 'adwords_keywords', 'adwords_traffic', 'adwords_cost', 'pla_keywords', 'pla_uniques',
    ];

    public function domainOverviewSemrushApis($domain, $db, $column = null)
    {
        $key  = config('env.SEMRUSH_API');
        $apis = [
            'overview_all' => 'https://api.semrush.com/?key=' . $key . '&type=domain_ranks&export_columns=Db,Dn,Rk,Or,Ot,Oc,Ad,At,Ac,Sh,Sv&domain=' . $domain . '&database=' . $db,
        ];

        if ($column == null) {
            return $apis;
        } else {
            return $apis[$column];
        }
    }

    public function domainOverviewSemrushResponse($column)
    {
        $apisResponse = [
            'overview_all' => 'Database;Domain;Rank;Organic Keywords;Organic Traffic;Organic Cost;Adwords Keywords;Adwords Traffic;Adwords Cost;PLA keywords;PLA uniques
us;apple.com;17;16464474;149904314;169865994;128201;2419518;2807373;38208;1583',

        ];

        return $apisResponse[$column];
    }
}
