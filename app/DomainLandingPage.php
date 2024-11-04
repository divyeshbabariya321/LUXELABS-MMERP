<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DomainLandingPage extends Model
{

    protected $fillable = ['store_website_id', 'tool_id', 'database', 'target_url', 'first_seen', 'last_seen', 'times_seen', 'ads_count'];

    public function landingPageSemrushApi($domain, $db, $column = null)
    {
        $key  = config('env.SEMRUSH_API');
        $apis = [
            'landing_page' => 'https://api.semrush.com/analytics/da/v2/?action=report&key=' . $key . '&domain=' . $domain . '&type=advertiser_landings',
        ];

        if ($column == null) {
            return $apis;
        } else {
            return $apis[$column];
        }
    }

    public function landingPageSemrushResponse($column)
    {
        $apisResponse = [
            'landing_page' => 'target_url;first_seen;last_seen;times_seen;ads_count
https://www.wayfair.com/gateway.php?refid=GX87595254316.Fixtures~&position=none&network=d&pcrid=87595254316&device=t&placement=profootballtalk.nbcsports.com&image=27571844;1465948800000;1471910400000;101584;1
https://www.wayfair.com/gateway.php?refid=GX56855779276.lampsusa.com~&position=none&network=d&pcrid=56855779276&device=c&placement=lampsusa.com;1459900800000;1465862400000;53535;1
https://www.wayfair.com/gateway.php?refid=GX49222149933.www.antiques.com~&position=none&network=d&pcrid=49222149933&device=t&placement=www.antiques.com;1459900800000;1471910400000;52003;1
https://www.wayfair.com/gateway.php?refid=GX56855779276.lampsusa.com~&position=none&network=d&pcrid=56855779276&device=t&placement=lampsusa.com;1459900800000;1465862400000;51424;1
https://www.wayfair.com/gateway.php?refid=GX87595254316.Fixtures~&position=none&network=d&pcrid=87595254316&device=t&placement=82baac0daefc2cb2.anonymous.google&image=27571844;1462406400000;1471910400000;43596;1
https://www.wayfair.com/gateway.php?refid=GX87595254316.Fixtures~&position=none&network=d&pcrid=87595254316&device=t&placement=bronxbaseballdaily.com&image=27571844;1462320000000;1471910400000;41458;1
https://www.wayfair.com/gateway.php?refid=GX62899178682.www.antiquesnavigator.com~&position=none&network=d&pcrid=62899178682&device=t&placement=www.antiquesnavigator.com&image=14557927;1459987200000;1471824000000;33808;1
https://www.wayfair.com/gateway.php?refid=GX87595254316.Fixtures~&position=none&network=d&pcrid=87595254316&device=t&placement=gotitans.com&image=27571844;1462320000000;1471910400000;32005;1
https://www.wayfair.com/gateway.php?refid=GX87595254316.Fixtures~&position=none&network=d&pcrid=87595254316&device=t&placement=www.celticslife.com&image=27571844;1468281600000;1471910400000;31344;1
https://www.wayfair.com/gateway.php?refid=GX60014892162.www.antiquesnavigator.com~&position=none&network=d&pcrid=60014892162&device=t&placement=www.antiquesnavigator.com&image=14982740;1460073600000;1471824000000;28714;1',

        ];

        return $apisResponse[$column];
    }
}
