<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class BacklinkDomains extends Model
{
    protected $table = 'back_link_domains';

    protected $fillable = ['store_website_id', 'tool_id', 'database', 'subtype', 'domain', 'domain_ascore', 'backlinks_num'];

    public function backlinkdomainsSemrushApis($domain, $db, $column = null)
    {
        $key  = config('env.SEMRUSH_API');
        $apis = [
            'domain'     => 'https://api.semrush.com/analytics/v1/?key=' . $key . '&type=backlinks_refdomains&target=' . $domain . '&target_type=root_domain&export_columns=domain_ascore,domain,backlinks_num,ip,country,first_seen,last_seen&display_limit=5',
            'ref_domain' => 'https://api.semrush.com/analytics/v1/?key=' . $key . '&type=backlinks_refdomains&target=' . $domain . '&target_type=root_domain&export_columns=domain_ascore,domain,backlinks_num,ip,country,first_seen,last_seen&display_limit=5&display_filter=%2B%7Ctype%7C%7Cnewdomain%7C%2B%7Czone%7C%7Cuk',
        ];

        if ($column == null) {
            return $apis;
        } else {
            return $apis[$column];
        }
    }

    public function backlinkdomainsSemrushResponse($column)
    {
        $apisResponse = [
            'domain' => 'domain_ascore;domain;backlinks_num;ip;country;first_seen;last_seen
86;libsyn.com;1850868;204.16.246.222;us;1495338484;1580410670
38;customerguru.in;503992;37.60.254.149;us;1532739198;1578767338
58;obs.co.kr;386005;59.25.202.101;kr;1565621989;1580411659
22;recip-links.com;354282;52.95.147.26;ca;1524707034;1580411673
38;goldenarticles.net;348079;89.190.202.12;bg;1544015188;1580411732',
            'ref_domain' => 'domain_ascore;domain;backlinks_num;ip;country;first_seen;last_seen
0;googleadwordshero.co.uk;7;185.230.60.195;us;1579491102;1580098568
0;seo.uk;6;104.27.151.149;us;1579840950;1579972614
0;coachfactoryoutlet.co.uk;5;104.27.163.233;us;1579908382;1580096134
21;growthhakka.co.uk;4;104.18.41.24;us;1579981260;1580196001
0;pufferr.co.uk;4;104.27.164.112;us;1578876811;1578876811',

        ];

        return $apisResponse[$column];
    }
}
