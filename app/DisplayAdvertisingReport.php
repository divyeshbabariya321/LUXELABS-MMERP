<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DisplayAdvertisingReport extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'store_website_id', 'tool_id', 'database', 'publisher_display_ads', 'advertisers', 'publishers', 'advertiser_display_ads', 'landing_pages', 'advertiser_display_ads_on_a_publishers_website', 'advertisers_rank', 'publishers_rank',
    ];

    const SEMRUSH_API = 'https://api.semrush.com/analytics/da/v2/?action=report&key=';

    public function displayAdvertisingReportSemrushApis($domain, $db, $column = null)
    {
        $key = config('settings.key');
        $apis = [
            'publisher_display_ads' => self::SEMRUSH_API.$key.'&domain='.$domain.'&type=publisher_text_ads',

            'advertisers' => self::SEMRUSH_API.$key.'&domain='.$domain.'&type=publisher_advertiser',

            'publishers' => self::SEMRUSH_API.$key.'&domain='.$domain.'&type=advertiser_publishers',

            'advertiser_display_ads' => self::SEMRUSH_API.$key.'&domain='.$domain.'&type=advertiser_text_ads',

            'landing_pages' => self::SEMRUSH_API.$key.'&domain='.$domain.'&type=advertiser_landings',

            'advertiser_display_ads_on_a_publishers_website' => self::SEMRUSH_API.$key.'&advertiser_domain=ebay.com&publisher_domain=urbandictionary.com&type=advertiser_publisher_text_ads',

            'advertisers_rank' => self::SEMRUSH_API.$key.'&domain='.$domain.'&type=advertiser_rank',

            'publishers_rank' => self::SEMRUSH_API.$key.'&domain='.$domain.'&type=publisher_rank',
        ];

        if ($column == null) {
            return $apis;
        } else {
            return $apis[$column];
        }
    }

    public function displayAdvertisingReportSemrushResponse($column)
    {
        $apisResponse = [
            'publisher_display_ads' => "title;text;first_seen;last_seen;times_seen;visible_url
Diabetes II Insulin;Get Complete Info on Diabetes Medication And
Treatments.;1467072000000;1471478400000;985369;everydayhealth.com/Insulin
FICA;Federally Insured Cash Account Official Site of the FICA
Account;1460073600000;1468627200000;693521;ficaaccount.com
米国暮らしでも映画は無理;アメリカに住んでるだけじゃ映画の 英語は聞き取れません。なぜ？;1461801600000;1468627200000;664230;mogomogobuster.com
Free Downloadable Videos;Download The Most Viewed End Time Bible Prophecy Videos Online
Today!;1459900800000;1467936000000;528229;worldslastchance.com
Learn English Online Free »;How to learn english fast and easy? Learn english grammar online
free!;1459900800000;1462406400000;463272;learn-english-online.us
Type 2 Diabetes Recipes;Sign Up for Weight Loss Tips & More Try our Customized Meal Planner
Now;1466985600000;1468022400000;432721;everydayhealth.com/Type-2-Diabetes
Download a Book Free »;Download Allatra - worth reading Most extraordinary book of
century;1466467200000;1468540800000;432453;allatra-book.org/Download_Free
Insulin Dependent;Learn About the Causes, Symptoms And Treatment of Type 2
Diabetes.;1467072000000;1468627200000;409819;everydayhealth.com/Insulin
Parmesan Meatball Sliders;Get Cooking With Hunt's - Make This Delicious Baked Dish
Tonight!;1459900800000;1468281600000;363474;hunts.com
BANKSY MINI WALL MURAL;Banksy Mural on Concrete Texture 12x12x3, for Wall or Table
Top;1466121600000;1468627200000;314699;designproject.net",

            'advertisers' => 'domain;ads_count;first_seen;last_seen;times_seen
stylewe.com;3243;1459900800000;1471046400000;13912898
arabmatchmaking.com;13788;1459900800000;1468627200000;10674727
facebook.com;145903;1369440000000;1468627200000;9581288
truthfinder.com;136;1461801600000;1468627200000;4455000
everydayhealth.com;699;1459900800000;1471478400000;2932806
instantcheckmate.com;49;1396137600000;1468627200000;2790630
bestbackground-1191.appspot.com;125;1459987200000;1468540800000;2479866
muslims4marriage.com;53663;1459900800000;1468627200000;1473834
mogomogobuster.com;20;1461801600000;1468627200000;1240533
newlife24h.com;6;1461456000000;1468540800000;1121633',

            'publishers' => 'domain;ads_count;first_seen;last_seen;times_seen
thisnext.com;10837;1388880000000;1471910400000;888747
antiquesnavigator.com;7410;1439424000000;1471910400000;518508
antiques.com;7945;1459900800000;1471910400000;364953
cotedetexas.blogspot.com;10980;1401235200000;1471910400000;212340
toynewsi.com;1449;1459900800000;1471910400000;176293
epicsports.com;3762;1461628800000;1471910400000;168701
lampsusa.com;1174;1459900800000;1465862400000;149453
blueridgenow.com;5130;1459900800000;1471910400000;137279
christmas.com;5293;1459900800000;1471824000000;122658
aquasupercenter.com;5045;1459900800000;1471910400000;121911',

            'advertiser_display_ads' => 'title;text;first_seen;last_seen;times_seen;visible_url
Dakota Digital Products »;Free shipping on U.S. orders $150+ Digital Dash/Gauges, Cruise
Control;1461801600000;1471910400000;76233;stores.ebay.com/Phoenix-Tuning
Star Wars Vintage Figures »;Huge selection of Vintage 1977-1985 Star Wars
Figures,Vehicles,Playsets;1462320000000;1471910400000;44121;solossmugglersshop.com
Gfo $17/Lb Free Shipping »;Prevent Algae In Aquariums Lowest Prices On Gfo
Anywhere;1459900800000;1469923200000;42029;ebay.com
PlatinumPool online store;Best online swimming pool store Lowest prices and free
shipping;1466726400000;1470441600000;22003;stores.ebay.com
Strip Curtains $99/roll »;Phthalate Free Clear PVC Save Energy &
Money;1459900800000;1469923200000;19863;ebay.com
Great Furniture »;Home Goods Furniture;1461801600000;1471910400000;13936;stores.ebay.com/doordirect/
Kathymac Jewelry »;Indian Jewelry,Silver Turquoise Pendants, Earrings, Fashion
Jewelry;1465948800000;1471910400000;11956;stores.ebay.com/Kathymac-Jewelry
Kia & Hyundai OEM Parts »;Low cost Original Equipment Parts Free
shipping;1459900800000;1470009600000;8093;ebay.com/usr/phd_auto_parts
Ducati Used Parts;European and American Sportbike Used Parts and
Accessories;1459900800000;1471305600000;7828;stores.ebay.com/imperialsportbikes
ACECLUB - eBay Stores;Welceom to ACECLUB. We carries auto parts, Mobil1 oil, LED strips
etc.;1461801600000;1468972800000;7234;stores.ebay.com/aceclub',

            'landing_pages' => 'target_url;first_seen;last_seen;times_seen;ads_count
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

            'advertiser_display_ads_on_a_publishers_website' => "title;text;first_seen;last_seen;times_seen;visible_url
Strip Curtains $99/roll »;Phthalate Free Clear PVC Save Energy &
Money;1460160000000;1468627200000;415;ebay.com
Kathymac Jewelry;Indian Jewelry,Silver Turquoise Pendants, Earrings, Fashion
Jewelry;1466035200000;1468540800000;265;stores.ebay.com/Kathymac-Jewelry
PlatinumPool online store »;Best online swimming pool store Lowest prices and free
shipping;1467331200000;1468627200000;234;stores.ebay.com
Discount Diamond Grind;Diamond Grind and ERGO Killer Prices and Free
ship;1461801600000;1468627200000;211;grinderpalace.com
Memories from the past;Memorablia from the 60s til today musicians boys bands, actors,
teens;1461888000000;1467244800000;161;stores.ebay.com/teen-idol-heaven
Muhammad Ali G.O.A.T.;Honor the Greatest of All Time! American Apparel G.O.A.T
T-Shirt;1465948800000;1466726400000;157;ebay.com
Car Headlight Fog Upgrade;Specialize Automotive Lighting Lamp Quality Brand item Replacement
Bulb;1465948800000;1468627200000;115;stores.ebay.com/formulaj87/
Rev. Tye's Coin Stache »;Are you nikel-ish? Tons of certified buffalo
nickels;1461801600000;1468195200000;88;stores.ebay.com/RevTyes-Coin-Stache
Marine Emblem Bullet Pen;Made in USA by a Veteran. Well made with the Corps in
mind.;1468195200000;1468540800000;74;ebay.com/itm/like/172037229785
RPG Books Great Price;Low Prices on New RPG Books From Mongoose &
StarFleet;1461974400000;1467936000000;69;ebay.com",

            'advertisers_rank' => 'domain;ads_overall;text_ads_overall;media_ads_overall;first_seen;last_seen;times_seen;domain_overall
ebay.com;2932;2658;274;1364342400000;1471910400000;848588;37213',

            'publishers_rank' => 'domain;ads_overall;text_ads_overall;media_ads_overall;first_seen;last_seen;times_seen;domain_overall
urbandictionary.com;1916819;1687303;229516;1368921600000;1471910400000;78970987;64985',
        ];

        return $apisResponse[$column];
    }

    public function displayAdvertisingReportAhrefsApis($domain, $db, $column = null)
    {
        $key = config('settings.key');
        $apis = [
            'publisher_display_ads' => '',

            'advertisers' => '',

            'publishers' => '',

            'advertiser_display_ads' => '',

            'landing_pages' => '',

            'advertiser_display_ads_on_a_publishers_website' => '',

            'advertisers_rank' => '',

            'publishers_rank' => '',
        ];

        if ($column == null) {
            return $apis;
        } else {
            return $apis[$column];
        }
    }

    public function displayAdvertisingReportAhrefsResponse($column)
    {
        $apisResponse = [
            'publisher_display_ads' => '',

            'advertisers' => '',

            'publishers' => '',

            'advertiser_display_ads' => '',

            'landing_pages' => '',

            'advertiser_display_ads_on_a_publishers_website' => '',

            'advertisers_rank' => '',

            'publishers_rank' => '',
        ];

        return $apisResponse[$column];
    }
}
