<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Competitor extends Model
{

    protected $fillable = ['store_website_id', 'tool_id', 'database', 'subtype', 'domain', 'common_keywords', 'keywords', 'traffic'];

    public function competitorSemrushApis($domain, $db, $column = null)
    {
        $key  = config('env.SEMRUSH_API');
        $apis = [
            'organic' => 'https://api.semrush.com/?type=domain_organic_organic&key=' . $key . '&display_limit=1&export_columns=Dn,Cr,Np,Or,Ot,Oc,Ad&domain=' . $domain . '&database=' . $db,

            'paid' => 'https://api.semrush.com/?type=domain_adwords_adwords&key=' . $key . '&display_limit=1&export_columns=Dn,Cr,Np,Ad,At,Ac,Or&domain=' . $domain . '&database=' . $db,
        ];

        if ($column == null) {
            return $apis;
        } else {
            return $apis[$column];
        }
    }

    public function competitorSemrushResponse($column)
    {
        $apisResponse = [
            'organic' => 'Domain;Competitor Relevance;Common Keywords;Organic Keywords;Organic Traffic;Organic Cost;Adwords Keywords
seochat.com;0.13;338;11021;5640;9690;0
seocentro.com;0.12;237;2196;8091;43478;0
internetmarketingninjas.com;0.12;323;15751;16182;30168;20
webconfs.com;0.12;265;6689;4291;14093;0
link-assistant.com;0.12;326;18255;13089;51583;26
wordtracker.com;0.12;289;7685;11254;51352;1
keywordtool.io;0.11;337;19247;103615;145337;1
kwfinder.com;0.10;259;4885;8260;33067;2
seoreviewtools.com;0.10;240;4537;7181;32502;0
smallseotools.com;0.10;469;21126;299488;450094;10',
            'paid' => 'Domain;Competitor Relevance;Common Keywords;Adwords Keywords;Adwords Traffic;Adwords Cost;Organic Keywords
bestdeals.today;0.07;192427;4180961;231702687;219085005;98743
amazon.com;0.07;337566;7674897;606923091;363744884;76392627
discount99.us;0.04;21583;82580;486315;307513;0
netdeals.com;0.03;27343;740384;38400483;28864800;6
walmart.com;0.03;21533;558633;146423426;31455771;19967088
blackfridaydeals2016.co;0.02;23660;1091009;75093364;65877087;0
informationvine.com;0.02;25758;1377703;86672506;90105545;0
target.com;0.02;21805;1037283;52256534;51634896;9181821
savesmart.com;0.02;11659;151845;5477272;5183214;8
shop411.com;0.02;29103;1896105;84369570;98212347;0',

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
