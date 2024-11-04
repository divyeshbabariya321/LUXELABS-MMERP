<?php

namespace App\Http\Controllers;

use App\BacklinkAnchors;
use App\BacklinkDomains;
use App\BacklinkIndexedPage;
use App\BacklinkOverview;
use App\Competitor;
use App\DomainLandingPage;
use App\DomainOrganicPage;
use App\DomainOverview;
use App\DomainSearchKeyword;
use App\KeywordTag;
use App\LogRequest;
use App\ProjectKeyword;
use App\SemrushKeyword;
use App\SemrushTag;
use App\SiteAudit;
use App\SiteIssue;
use App\StoreWebsite;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SeoToolController extends Controller
{
    public function index(): View
    {
        $websites = StoreWebsite::pluck('website', 'id')->toArray();
        $siteAudits = $domainOverview = $backlinkreports = [];
        foreach ($websites as $websiteId => $website) {
            $siteAuditDetail = SiteAudit::where(['store_website_id' => $websiteId])->orderByDesc('id')->first();
            if ($siteAuditDetail != null) {
                $siteAudits[$websiteId]['errors'] = $siteAuditDetail['errors'];
                $siteAudits[$websiteId]['warnings'] = $siteAuditDetail['warnings'];
                $siteAudits[$websiteId]['pages_crawled'] = $siteAuditDetail['pages_crawled'];
            }
            $overview = DomainOverview::where(['store_website_id' => $websiteId, 'tool_id' => 1])->orderByDesc('id')->first();
            if ($overview != null) {
                $domainOverview[$websiteId]['organic_keywords'] = $this->restyle_text($overview['organic_keywords']);
                $domainOverview[$websiteId]['organic_traffic'] = $this->restyle_text($overview['organic_traffic']);
                $domainOverview[$websiteId]['organic_cost'] = $this->restyle_text($overview['organic_cost']);
            }
            $backlinkreport = BacklinkOverview::where(['store_website_id' => $websiteId, 'tool_id' => 1])->orderByDesc('id')->first();
            if ($backlinkreport != null) {
                $backlinkreports[$websiteId]['ascore'] = $this->restyle_text($backlinkreport['ascore']);
                $backlinkreports[$websiteId]['follows_num'] = $this->restyle_text($backlinkreport['follows_num']);
                $backlinkreports[$websiteId]['nofollows_num'] = $this->restyle_text($backlinkreport['nofollows_num']);
            }
        }

        return view('seo-tools.index', compact('websites', 'siteAudits', 'domainOverview', 'backlinkreports'));
    }

    /**
     * This function is used to search for SEO filter
     */
    public function searchSeoFilter(Request $request): JsonResponse
    {
        try {
            $search_website = [];
            if ($request->search_website != '') {
                $search_website = ['website', 'LIKE', '%'.$request->search_website.'%'];
            }
            $websites = StoreWebsite::where([['id', '!=', ''], $search_website])->pluck('website', 'id');
            $siteAudits = $domainOverview = $backlinkreports = [];
            foreach ($websites as $websiteId => $website) {
                $siteAuditDetail = SiteAudit::where(['store_website_id' => $websiteId])->orderByDesc('id')->first();
                if ($siteAuditDetail != null) {
                    $siteAudits[$websiteId]['errors'] = $siteAuditDetail['errors'];
                    $siteAudits[$websiteId]['warnings'] = $siteAuditDetail['warnings'];
                    $siteAudits[$websiteId]['pages_crawled'] = $siteAuditDetail['pages_crawled'];
                }
                $overview = DomainOverview::where(['store_website_id' => $websiteId, 'tool_id' => 1])->orderByDesc('id')->first();
                if ($overview != null) {
                    $domainOverview[$websiteId]['organic_keywords'] = $this->restyle_text($overview['organic_keywords']);
                    $domainOverview[$websiteId]['organic_traffic'] = $this->restyle_text($overview['organic_traffic']);
                    $domainOverview[$websiteId]['organic_cost'] = $this->restyle_text($overview['organic_cost']);
                }
                $backlinkreport = BacklinkOverview::where(['store_website_id' => $websiteId, 'tool_id' => 1])->orderByDesc('id')->first();
                if ($backlinkreport != null) {
                    $backlinkreports[$websiteId]['ascore'] = $this->restyle_text($backlinkreport['ascore']);
                    $backlinkreports[$websiteId]['follows_num'] = $this->restyle_text($backlinkreport['follows_num']);
                    $backlinkreports[$websiteId]['nofollows_num'] = $this->restyle_text($backlinkreport['nofollows_num']);
                }
            }

            return response()->json([
                'tbody' => view('seo-tools.index-search', compact('websites', 'siteAudits', 'domainOverview', 'backlinkreports'))->render(),
            ], 200);
        } catch (Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

    public function restyle_text($input)
    {
        $input = number_format($input);
        $input_count = substr_count($input, ',');

        switch ($input_count) {
            case 0:
                return $input; // No formatting needed
            case 1:
                return substr($input, 0, -4).' K'; // Thousands
            case 2:
                return substr($input, 0, -8).' M'; // Millions
            case 3:
                return substr($input, 0, -12).' B'; // Billions
            default:
                return null; // For cases beyond billions

        }
    }

    public function fetchDetails()
    {
        //fetch domain reports
        $storeWebsites = StoreWebsite::select('id', 'website')->get();
        foreach ($storeWebsites as $storeWebsite) {
            $inputs['website'] = $storeWebsite['website'];
            $inputs['websiteId'] = $websiteId = $storeWebsite['id'];
            $database = 'us';
            if (isset($_GET['database'])) {
                $database = $_GET['database'];
            }
            $toolId = 1;
            $now = Carbon::now()->format('Y-m-d');

            $semrushBacklinkOverviewResponse = $semrushBacklinkAnchorResponse = $semrushBacklinkDomainResponse = $semrushCompetitorResponse = [];
            //Semrush Domain apis start
            $semrushDomainReportApis = (new DomainSearchKeyword)->domainKeywordSearchSemrushApis($inputs['website'], $database);
            foreach ($semrushDomainReportApis as $column => $api) {
                $domainSearchKeywords = DomainSearchKeyword::where('store_website_id', $websiteId)->where('created_at', 'like', '%'.$now.'%')->where('subtype', $column)->first();
                if ($domainSearchKeywords == null) {
                    $semrushDomainResponse = $this->semrushCurlRequests('domain_search_keywords', $column, $api, 1);
                    foreach (json_decode($semrushDomainResponse, true) as $value) { //dd($value);
                        if ($column == 'organic') {
                            $url = $value['url'];
                            $traffic = $value['traffic_cost_'];
                        } else {
                            $url = $value['visible_url'];
                            $traffic = $value['traffic_cost'];
                        }
                        $dataToInsert = ['store_website_id' => $inputs['websiteId'], 'tool_id' => $toolId, 'database' => $database, 'subtype' => $column, 'database' => $database,
                            'keyword' => $value['keyword'], 'position' => $value['position'], 'previous_position' => $value['previous_position'],
                            'position_difference' => $value['position_difference'], 'search_volume' => $value['search_volume'], 'cpc' => $value['cpc'], 'url' => $url, 'traffic_percentage' => $value['traffic_'], 'traffic_cost' => $traffic,
                            'competition' => $value['competition'], 'number_of_results' => $value['number_of_results'], 'trends' => $value['trends'], ];
                        DomainSearchKeyword::create($dataToInsert);
                    }
                }
            }

            //Semrush Domain Overview apis start
            $semrushDomainReportApi = (new DomainOverview)->domainOverviewSemrushApis($inputs['website'], $database, 'overview_all');
            $domainOverviewData = DomainOverview::where(['store_website_id' => $inputs['websiteId'], 'tool_id' => $toolId, 'database' => $database])->where('created_at', 'like', '%'.$now.'%')->first();
            if ($domainOverviewData == null) {
                $semrushDomainOverviewResponse = $this->semrushCurlRequests('domain_overview', 'overview_all', $semrushDomainReportApi, 1);
                foreach (json_decode($semrushDomainOverviewResponse, true) as $value) {
                    $dataToInsert = ['store_website_id' => $inputs['websiteId'], 'tool_id' => $toolId, 'database' => $database, 'rank' => $value['rank'], 'organic_keywords' => $value['organic_keywords'], 'organic_traffic' => $value['organic_traffic'], 'organic_cost' => $value['organic_cost'], 'adwords_keywords' => $value['adwords_keywords'], 'adwords_traffic' => $value['adwords_traffic'], 'adwords_cost' => $value['adwords_cost'], 'pla_keywords' => $value['pla_keywords'], 'pla_uniques' => $value['pla_uniques']];
                    DomainOverview::create($dataToInsert);
                }
            }

            //Semrush Backlink apis Start
            $semrushBacklinkOverviewReportApi = (new BacklinkOverview)->backlinkoverviewSemrushApis($inputs['website'], $database);
            $backlinkOverviewData = BacklinkOverview::where(['store_website_id' => $inputs['websiteId'], 'tool_id' => $toolId, 'database' => $database])->where('created_at', 'like', '%'.$now.'%')->first();
            if ($backlinkOverviewData == null) {
                $semrushBacklinkOverviewResponse = $this->semrushCurlRequests('backlink_overview', 'overview', $semrushBacklinkOverviewReportApi, 1);
                if ($semrushBacklinkOverviewResponse != '') {
                    foreach (json_decode($semrushBacklinkOverviewResponse, true) as $value) {
                        $dataToInsert = ['store_website_id' => $inputs['websiteId'], 'tool_id' => $toolId, 'database' => $database, 'ascore' => $value['ascore'], 'total' => $value['total'], 'domains_num' => $value['domains_num'], 'urls_num' => $value['urls_num'], 'ips_num' => $value['ips_num'], 'ipclassc_num' => $value['ipclassc_num'], 'follows_num' => $value['follows_num'], 'nofollows_num' => $value['nofollows_num'], 'sponsored_num' => $value['sponsored_num'], 'ugc_num' => $value['ugc_num'], 'texts_num' => $value['texts_num'], 'images_num' => $value['images_num'], 'forms_num' => $value['forms_num'], 'frames_num' => $value['frames_num']];
                        BacklinkOverview::create($dataToInsert);
                    }
                }
            }

            //Semrush Backlink Anchor Api Start
            $semrushBacklinkAnchorReportApi = (new BacklinkAnchors)->backlinkanchorsSemrushApis($inputs['website'], $database);
            $backlinkAnchorData = BacklinkAnchors::where(['store_website_id' => $inputs['websiteId'], 'tool_id' => $toolId, 'database' => $database])->where('created_at', 'like', '%'.$now.'%')->first();
            if ($backlinkAnchorData == null) {
                $semrushBacklinkAnchorResponse = $this->semrushCurlRequests('back_link_anchors', 'anchor', $semrushBacklinkAnchorReportApi, 1);
                if ($semrushBacklinkAnchorResponse != '') {
                    foreach (json_decode($semrushBacklinkAnchorResponse, true) as $value) {
                        $dataToInsert = ['store_website_id' => $inputs['websiteId'], 'tool_id' => $toolId, 'database' => $database, 'anchor' => $value['anchor'], 'domains_num' => $value['domains_num'], 'backlinks_num' => $value['backlinks_num']];
                        BacklinkAnchors::create($dataToInsert);
                    }
                }
            }

            //Semrush Backlink Domain Api Start
            $semrushBacklinkDomainReportApis = (new BacklinkDomains)->backlinkdomainsSemrushApis($inputs['website'], $database);
            foreach ($semrushBacklinkDomainReportApis as $column => $api) {
                $backlinkDomainsData = BacklinkDomains::where(['store_website_id' => $inputs['websiteId'], 'tool_id' => $toolId, 'database' => $database, 'subtype' => $column])->where('created_at', 'like', '%'.$now.'%')->first();
                if ($backlinkDomainsData == null) {
                    $semrushBacklinkDomainResponse = $this->semrushCurlRequests('back_link_domains', $column, $api, 1);
                    if ($semrushBacklinkDomainResponse != '') {
                        foreach (json_decode($semrushBacklinkDomainResponse, true) as $value) {
                            $dataToInsert = ['store_website_id' => $inputs['websiteId'], 'tool_id' => $toolId, 'database' => $database, 'subtype' => $column, 'domain' => $value['domain'], 'domain_ascore' => $value['domain_ascore'], 'backlinks_num' => $value['backlinks_num']];
                            BacklinkDomains::create($dataToInsert);
                        }
                    }
                }
            }

            //Semrush Competitor Api Start
            $semrushCompetitorReportApis = (new Competitor)->competitorSemrushApis($inputs['website'], $database);
            foreach ($semrushCompetitorReportApis as $column => $api) {
                $competitorData = Competitor::where(['store_website_id' => $inputs['websiteId'], 'tool_id' => $toolId, 'database' => $database, 'subtype' => $column])->where('created_at', 'like', '%'.$now.'%')->first();
                if ($competitorData == null) {
                    $semrushCompetitorResponse = $this->semrushCurlRequests('competitors', $column, $api, 1);
                    if ($semrushCompetitorResponse != '') {
                        foreach (json_decode($semrushCompetitorResponse, true) as $value) {
                            if ($column == 'paid') {
                                $dataToInsert = ['store_website_id' => $inputs['websiteId'], 'tool_id' => $toolId, 'database' => $database, 'subtype' => $column, 'domain' => $value['domain'], 'common_keywords' => $value['common_keywords'], 'keywords' => $value['adwords_keywords'], 'traffic' => $value['adwords_traffic']];
                            } else {
                                $dataToInsert = ['store_website_id' => $inputs['websiteId'], 'tool_id' => $toolId, 'database' => $database, 'subtype' => $column, 'domain' => $value['domain'], 'common_keywords' => $value['common_keywords'], 'keywords' => $value['organic_keywords'], 'traffic' => $value['organic_traffic']];
                            }
                            Competitor::create($dataToInsert);
                        }
                    }
                }
            }

            //Semrush OrganicPage Api Start
            $semrushOrganicPageApi = (new DomainOrganicPage)->organicPageSemrushApi($inputs['website'], $database);
            $organicPageData = DomainOrganicPage::where(['store_website_id' => $inputs['websiteId'], 'tool_id' => $toolId, 'database' => $database])->where('created_at', 'like', '%'.$now.'%')->first();
            if ($organicPageData == null) {
                $semrushOrganicPageResponse = $this->semrushCurlRequests('organic_page', 'organic_page', $semrushOrganicPageApi, 1);
                if ($semrushOrganicPageResponse != '') {
                    foreach (json_decode($semrushOrganicPageResponse, true) as $value) {
                        $dataToInsert = ['store_website_id' => $inputs['websiteId'], 'tool_id' => $toolId,
                            'database' => $database, 'url' => $value['url'], 'number_of_keywords' => $value['number_of_keywords'], 'traffic' => $value['traffic'], 'traffic_percentage' => $value['traffic_'], ];
                        DomainOrganicPage::create($dataToInsert);
                    }
                }
            }

            //Semrush Indexed Page Api Start
            $semrushIndexedPageApi = (new BacklinkIndexedPage)->indexedPageSemrushApi($inputs['website'], $database, 'indexed_page');
            $indexedPageData = BacklinkIndexedPage::where(['store_website_id' => $inputs['websiteId'], 'tool_id' => $toolId, 'database' => $database])->where('created_at', 'like', '%'.$now.'%')->first();
            if ($indexedPageData == null) {
                $semrushIndexedPageResponse = $this->semrushCurlRequests('indexed_page', 'indexed_page', $semrushIndexedPageApi, 1);
                foreach (json_decode($semrushIndexedPageResponse, true) as $value) {
                    $dataToInsert = ['store_website_id' => $inputs['websiteId'], 'tool_id' => $toolId,
                        'database' => $database, 'source_url' => $value['source_url'], 'source_title' => $value['source_title'],
                        'response_code' => $value['response_code'], 'backlinks_num' => $value['backlinks_num'],
                        'domains_num' => $value['domains_num'], 'last_seen' => $value['last_seen'], 'external_num' => $value['external_num'], 'internal_num' => $value['internal_num'], ];
                    BacklinkIndexedPage::create($dataToInsert);
                }
            }
            //Semrush Landing Page Api Start
            $semrushLandingPageApi = (new DomainLandingPage)->landingPageSemrushApi($inputs['website'], $database, 'landing_page');
            $LandingPageData = DomainLandingPage::where(['store_website_id' => $inputs['websiteId'], 'tool_id' => $toolId, 'database' => $database])->where('created_at', 'like', '%'.$now.'%')->first();
            if ($LandingPageData == null) {
                $semrushLandingPageResponse = $this->semrushCurlRequests('landing_page', 'landing_page', $semrushLandingPageApi, 1);
                if ($semrushLandingPageResponse != '') {
                    foreach (json_decode($semrushLandingPageResponse, true) as $value) {
                        $dataToInsert = ['store_website_id' => $inputs['websiteId'], 'tool_id' => $toolId,
                            'database' => $database, 'target_url' => $value['target_url'], 'first_seen' => $value['first_seen'],
                            'last_seen' => $value['last_seen'], 'times_seen' => $value['times_seen'],
                            'ads_count' => $value['ads_count'], ];
                        DomainLandingPage::create($dataToInsert);
                    }
                }
            }

            //Semrush siteaudit api start
            $websiteId = $inputs['websiteId'];
            $projectId = 1;
            if ($projectId != null) {

                $auditInfoResponse = SiteAudit::semrushApiResponses('site_audit_info');
                $siteIssuesResponse = SiteAudit::semrushApiResponses('site_issues');

                $data = json_decode($auditInfoResponse, true);
                $siteAudit = SiteAudit::where('store_website_id', $websiteId)->where('created_at', 'like', '%'.$now.'%')->first();
                if ($siteAudit == null) {
                    $data = ['project_id' => $projectId, 'store_website_id' => $websiteId] + $data;
                    unset($data['id']);
                    $data['defects'] = json_encode($data['defects']);
                    $data['depths'] = json_encode($data['depths']);
                    $data['markups'] = json_encode($data['markups']);
                    $data['depths'] = json_encode($data['depths']);
                    $data['mask_allow'] = json_encode($data['mask_allow']);
                    $data['mask_disallow'] = json_encode($data['mask_disallow']);
                    $data['removedParameters'] = json_encode($data['removedParameters']);
                    if ($data['excluded_checks'] == null) {
                        $data['excluded_checks'] = 0;
                    }
                }
                $siteAuditIssues = json_decode($siteIssuesResponse, true);
                foreach ($siteAuditIssues['issues'] as $siteAuditIssuesData) { //dd($siteAuditIssuesData);
                    $dataToSave = ['project_id' => $projectId, 'store_website_id' => $websiteId];
                    $dataToSave['title'] = $siteAuditIssuesData['title'];
                    $dataToSave['desc'] = $siteAuditIssuesData['desc'];
                    $dataToSave['title_page'] = $siteAuditIssuesData['title_page'];
                    SiteIssue::create($dataToSave);
                }
            }
        }

        return ['status_code' => 200, 'response' => 'Data fetched'];
    }

    public function semrushCurlRequests($type, $column, $api, $keyValuePair = 0, $env = 'test')
    {
        try {
            if ($env == 'test') {
                if ($type == 'domain_search_keywords') {
                    $response = DomainSearchKeyword::domainKeywordSearchSemrushResponse($column);
                } elseif ($type == 'domain_overview') {
                    $response = DomainOverview::domainOverviewSemrushResponse($column);
                } elseif ($type == 'backlink_overview') {
                    $response = BacklinkOverview::backlinkoverviewSemrushResponse($column);
                } elseif ($type == 'back_link_anchors') {
                    $response = BacklinkAnchors::backlinkanchorsSemrushResponse($column);
                } elseif ($type == 'back_link_domains') {
                    $response = BacklinkDomains::backlinkdomainsSemrushResponse($column);
                } elseif ($type == 'competitors') {
                    $response = Competitor::competitorSemrushResponse($column);
                } elseif ($type == 'organic_page') {
                    $response = DomainOrganicPage::organicPageSemrushResponse($column);
                } elseif ($type == 'indexed_page') {
                    $response = BacklinkIndexedPage::indexedPageSemrushResponse($column);
                } elseif ($type == 'landing_page') {
                    $response = DomainLandingPage::landingPageSemrushResponse($column);
                }
            } else {
                $startTime = date('Y-m-d H:i:s', LARAVEL_START);
                $curl = curl_init();
                curl_setopt_array($curl, [
                    CURLOPT_URL => $api,
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_ENCODING => '',
                    CURLOPT_MAXREDIRS => 10,
                    CURLOPT_TIMEOUT => 0,
                    CURLOPT_FOLLOWLOCATION => true,
                    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                    CURLOPT_CUSTOMREQUEST => 'GET',
                ]);
                $response = curl_exec($curl);
                $httpcode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
                LogRequest::log($startTime, $api, 'GET', json_encode([]), json_decode($response), $httpcode, SeoToolController::class, 'semrushCurlRequests');
                curl_close($curl);
            }
            if ($response != 'ERROR 50 :: NOTHING FOUND') {
                if ($keyValuePair == 1) {
                    return $this->parseSemrushResponseKeyValue($response);
                } else {
                    return $this->parseSemrushResponse($response);
                }
            }
        } catch (Exception $e) {
            return '';
        }
    }

    public function parseSemrushResponse($response, $heading = 0)
    {
        $response1 = explode("\n", $response);
        $final = [];
        foreach ($response1 as $key => $new) {
            if ($heading == 0) {
                if ($key > 0) {
                    $new = explode(';', $new);
                    $final[] = $new;
                }
            } else {
                $new = explode(';', $new);
                $final[] = $new;
            }
        }

        return json_encode($final);
    }

    public function parseSemrushResponseKeyValue($response)
    {
        $response1 = explode("\n", $response);
        $final = [];
        foreach ($response1 as $new) {
            $new = explode(';', $new);
            $final[] = $new;
        }
        $arrayToBeUsed = [];
        $heading = $final[0];
        unset($final[0]);
        $i = 0;
        foreach ($final as $keydata) {
            if (count($keydata) > 1) {
                foreach ($keydata as $key => $value) {
                    $headingKey = str_replace(' ', '_', $heading[$key]);
                    $headingKey = strtolower(preg_replace('/[^A-Za-z0-9\_]/', '', $headingKey));
                    $arrayToBeUsed[$i][$headingKey] = $value;
                }
            }
            $i++;
        }

        return json_encode($arrayToBeUsed);
    }

    public function saveKeyword(Request $request): RedirectResponse
    {
        $response = '{
			"url": "mysite.com",
			"keywords": [
			{
			"keyword": "search tool",
			"tags": ["search"],
			"timestamp": 1391517755
			},
			{
			"keyword": "search engine",
			"tags": ["search"],
			"timestamp": 1391517755
			},
			{
			"keyword": "seo",
			"tags": ["seo"],
			"timestamp": 1491517755
			},
			{
			"keyword": "seotool",
			"tags": ["seo"],
			"timestamp": 1491517755
			}
			],
			"competitors": ["google.com","ebay.com","bing.com"],
			"tools": [],
			"project_id": 643526670283248,
			"project_name": "my old project"
			}';
        $result = json_decode($response, true);
        foreach ($result['keywords'] as $keywordDetail) {
            $keywordDetailNew = SemrushKeyword::firstOrCreate(['keyword' => $keywordDetail['keyword']], ['keyword' => $keywordDetail['keyword']]);
            foreach ($keywordDetail['tags'] as $tag) {
                $tagDetail = SemrushTag::firstOrCreate(['tag' => $tag], ['tag' => $tag]);
                KeywordTag::firstOrCreate(['keyword_id' => $keywordDetailNew['id'], 'tag_id' => $tagDetail['id']],
                    ['keyword_id' => $keywordDetailNew['id'], 'tag_id' => $tagDetail['id']]);
            }

            ProjectKeyword::firstOrCreate(['keyword_id' => $keywordDetailNew['id'], 'project_id' => $request->projectId], ['keyword_id' => $keywordDetailNew['id'], 'project_id' => $request->projectId]);
        }

        return redirect()->to(url('seo/project-list'));
    }

    public function projectList(): View
    {
        $auditInfoResponse = SiteAudit::semrushApiResponses('project_list');

        $project = json_decode($auditInfoResponse, true);

        return view('seo-tools.projects', compact('project'));
    }
}
