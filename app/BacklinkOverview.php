<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class BacklinkOverview extends Model
{
    protected $table = 'backlink_overview';

    protected $fillable = ['store_website_id', 'tool_id', 'database', 'ascore', 'total', 'domains_num', 'urls_num', 'ips_num', 'ipclassc_num', 'follows_num', 'nofollows_num', 'sponsored_num', 'ugc_num', 'texts_num', 'images_num', 'forms_num', 'frames_num'];

    public function backlinkoverviewSemrushApis($domain, $db, $column = null)
    {
        $key  = config('env.SEMRUSH_API');
        $apis = [
            'overview' => 'https://api.semrush.com/analytics/v1/?key=' . $key . '&type=backlinks_overview&target=' . $domain . '&target_type=root_domain&export_columns=ascore,total,domains_num,urls_num,ips_num,ipclassc_num,follows_num,nofollows_num,sponsored_num,ugc_num,texts_num,images_num,forms_num,frames_num',
        ];

        if ($column == null) {
            return $apis;
        } else {
            return $apis[$column];
        }
    }

    public function backlinkoverviewSemrushResponse($column)
    {
        $apisResponse = [
            'overview' => 'ascore;total;domains_num;urls_num;ips_num;ipclassc_num;follows_num;nofollows_num;sponsored_num;ugc_num;texts_num;images_num;forms_num;frames_num
74;22063983;49145;13059030;47793;22956;20457307;1606307;258;1475;21784602;278624;437;320', ];

        return $apisResponse[$column];
    }
}
