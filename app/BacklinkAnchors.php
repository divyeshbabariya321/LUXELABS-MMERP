<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class BacklinkAnchors extends Model
{
    protected $table = 'back_link_anchors';

    protected $fillable = ['store_website_id', 'tool_id', 'database', 'anchor', 'domains_num', 'backlinks_num'];

    public function backlinkanchorsSemrushApis($domain, $db, $column = null)
    {
        $key  = config('env.SEMRUSH_API');
        $apis = [
            'anchor' => 'https://api.semrush.com/analytics/v1/?key=' . $key . '&type=backlinks_anchors&target=' . $domain . '&target_type=root_domain&export_columns=anchor,domains_num,backlinks_num,first_seen,last_seen&display_limit=5', ];

        if ($column == null) {
            return $apis;
        } else {
            return $apis[$column];
        }
    }

    public function backlinkanchorsSemrushResponse($column)
    {
        $apisResponse = [
            'anchor' => 'anchor;domains_num;backlinks_num;first_seen;last_seen
search engine journal;8113;691263;1370650463;1580411804
93% of people;3;354284;1524707034;1580411673
the growth of social media v2.0 | search engine journal;1;251996;1532739198;1578767338
more;57;153884;1452198531;1580411620
read more >;2;114350;1545826610;1580411612', ];

        return $apisResponse[$column];
    }
}
