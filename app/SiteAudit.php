<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SiteAudit extends Model
{
    protected $table = 'site_audit';

    protected $fillable = ['project_id', 'store_website_id', 'name', 'url', 'status', 'errors', 'warnings', 'notices', 'broken', 'blocked', 'redirected', 'healthy', 'haveIssues', 'haveIssuesDelta', 'defects', 'markups', 'depths', 'crawlSubdomains', 'respectCrawlDelay', 'canonical', 'user_agent_type', 'last_audit', 'last_failed_audit', 'next_audit', 'running_pages_crawled', 'running_pages_limit', 'pages_crawled', 'pages_limit', 'total_checks', 'errors_delta', 'warnings_delta', 'notices_delta', 'mask_allow', 'mask_disallow', 'removedParameters', 'excluded_checks'];

    public function semrushApis($apiName)
    {
        $key  = config('env.SEMRUSH_API');
        $apis = [
            'project_list'    => 'https://api.semrush.com/management/v1/projects?key=' . $key,
            'site_audit'      => 'https://api.semrush.com/reports/v1/projects/{ID}/siteaudit/launch?key=' . $key,
            'site_audit_info' => 'https://api.semrush.com/reports/v1/projects/{ID}/siteaudit/info?key=' . $key,
            'add_keywords'    => 'https://api.semrush.com/management/v1/projects/{id}/keywords?key=' . $key,
            'site_issues'     => 'https://api.semrush.com/reports/v1/projects/{ID}/siteaudit/meta/issues?key=' . $key,
        ];

        return $apis[$apiName];
    }

    public function semrushApiResponses($apiResponse)
    {
        $responses = [
            'project_list' => '{
				"url": "mysite.com",
				"tools": [],
				"project_id": 643526670283248,
				"project_name": "myproject"
			}',
            'site_audit_info' => '{
						"id":4594705336925861,
						"name":"test",
						"url":"semrush.com",
						"status":"FINISHED",
						"errors":228,
						"warnings":391,
						"notices":9,
						"broken":0,
						"blocked":0,
						"redirected":2,
						"healthy":1,
						"haveIssues":2,
						"haveIssuesDelta":0,
						"defects":{"109":2},
						"markups":{
							"twitterCard":0,
							"openGraph":0,
							"schemaOrg":0,
							"microfomats":0
						},
						"depths":{"0":3},
						"crawlSubdomains":true,
						"respectCrawlDelay":false,
						"canonical":0,
						"user_agent_type":2,
						"last_audit":1410346398040,
						"last_failed_audit":0,
						"next_audit":-1,
						"running_pages_crawled":178,
						"running_pages_limit":500,
						"pages_crawled":178,
						"pages_limit":500,
						"total_checks":22725,
						"errors_delta":0,
						"warnings_delta":0,
						"notices_delta":0,
						"mask_allow":[],
						"mask_disallow":[],
						"removedParameters":["rr","r","p"],
						"excluded_checks":null
					}',
            'site_issues' => '{
					"issues":[
					{
					"id":1,
					"title":"HTTP 5XX server errors",
					"desc":"5xx errors happen on the server’s side. (500 – an internal server error; 503 – a server is unavailable; 507 – a server is running out of memory, etc.) \n\nHaving a lot of error pages negatively affects both User Experience and a search engine robot’s crawlability, which can lead to less traffic to your website.",
					"title_page":"##count## pages returned 5XX status code upon request",
					"title_detailed":"This page returned 5XX status code on request",
					"info_column":"Code",
					"count_description":"This page returned 5XX status code on request",
					"multidata":false,
					"other_problem_link":"##count## more page on this site has 500 status code",
					"desc_with_link":" ##count## pages  returned 5XX status code upon request"
					}]
					}',
        ];

        return $responses[$apiResponse];
    }
}
