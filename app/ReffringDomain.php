<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ReffringDomain extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'store_website_id', 'tool_id', 'database', 'referring_domains', 'referring_ips', 'referring_domains_by_country',
    ];

    public function reffringDomainSemrushApis($domain, $db, $column = null)
    {
        $key  = config('settings.key');
        $apis = [
            'referring_domains' => 'https://api.semrush.com/analytics/v1/?key=' . $key . '&type=backlinks_refdomains&target=searchenginejournal.com&target_type=root_domain&export_columns=domain_ascore,domain,backlinks_num,ip,country,first_seen,last_seen&display_limit=5',

            'referring_ips' => 'https://api.semrush.com/analytics/v1/?key=' . $key . '&type=backlinks_refips&target=searchenginejournal.com&target_type=root_domain&export_columns=ip,country,domains_num,backlinks_num,first_seen,last_seen&display_limit=5',

            'referring_domains_by_country' => 'https://api.semrush.com/analytics/v1/?key=' . $key . '&type=backlinks_geo&target=searchenginejournal.com&target_type=root_domain&export_columns=country,domains_num,backlinks_num&display_limit=5',
        ];

        if ($column == null) {
            return $apis;
        } else {
            return $apis[$column];
        }
    }

    public function reffringDomainSemrushResponse($column)
    {
        $apisResponse = [
            'referring_domains' => 'domain_ascore;domain;backlinks_num;ip;country;first_seen;last_seen
86;libsyn.com;1850868;204.16.246.222;us;1495338484;1580410670
38;customerguru.in;503992;37.60.254.149;us;1532739198;1578767338
58;obs.co.kr;386005;59.25.202.101;kr;1565621989;1580411659
22;recip-links.com;354282;52.95.147.26;ca;1524707034;1580411673
38;goldenarticles.net;348079;89.190.202.12;bg;1544015188;1580411732',

            'referring_ips' => 'ip;country;domains_num;backlinks_num;first_seen;last_seen
78.69.18.135;se;664;1195;1371696859;1580409277
192.0.78.12;us;357;3675;1534413883;1580408412
192.0.78.13;us;356;4012;1533338180;1580408397
172.217.15.65;us;306;617;1473348232;1580411014
172.217.164.161;us;300;581;1473018187;1580396737',

            'referring_domains_by_country' => 'country;domains_num;backlinks_num
United States;36489;5463278
Germany;2594;149154
United Kingdom;1750;102385
France;917;99323
Canada;791;695950',
        ];

        return $apisResponse[$column];
    }

    public function reffringDomainAhrefApis($domain, $db, $column = null)
    {
        $key  = config('settings.key');
        $apis = [
            'referring_domains' => 'https://apiv2.ahrefs.com?from=refdomains&target=ahrefs.com&mode=domain&limit=3&output=json',

            'referring_ips' => 'https://apiv2.ahrefs.com?from=refips&target=ahrefs.com&mode=domain&limit=5&order_by=backlinks%3Adesc&output=json',
        ];

        if ($column == null) {
            return $apis;
        } else {
            return $apis[$column];
        }
    }

    public function reffringDomainAhrefResponse($column)
    {
        $apisResponse = [
            'referring_domains' => '{
				"refdomains": [
					{
						"refdomain": "001baidu.com",
						"backlinks": 1,
						"refpages": 1,
						"first_seen": "2013-08-08T23:04:54Z",
						"last_visited": "2013-11-07T11:10:04Z",
						"domain_rating": 431
					},
					{
						"refdomain": "0411soft.net",
						"backlinks": 1,
						"refpages": 1,
						"first_seen": "2013-08-09T06:38:13Z",
						"last_visited": "2013-08-09T06:38:13Z",
						"domain_rating": 41
					},
					{
						"refdomain": "0769pxw.com",
						"backlinks": 1,
						"refpages": 1,
						"first_seen": "2013-08-08T18:57:12Z",
						"last_visited": "2013-11-07T11:10:04Z",
						"domain_rating": 47
					}
				]
			}',

            'referring_ips' => '{
				"refdomains": [
					{
						"refip": "204.225.190.144",
						"refdomain": "nydailynews.com",
						"backlinks": 960541
					},
					{
						"refip": "204.225.190.144",
						"refdomain": "torontosun.com",
						"backlinks": 260570
					},
					{
						"refip": "161.111.47.11",
						"refdomain": "webometrics.info",
						"backlinks": 246135
					},
					{
						"refip": "204.225.190.144",
						"refdomain": "enidnews.com",
						"backlinks": 126253
					},
					{
						"refip": "65.254.53.187",
						"refdomain": "netinfo.org.ua",
						"backlinks": 120625
					}
			]}',
        ];

        return $apisResponse[$column];
    }
}
