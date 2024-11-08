<?php

namespace App\Http\Controllers;

use Illuminate\View\View;
use Illuminate\Support\Facades\DB;
use App\SERanking;

class SERankingController extends Controller
{
    private $apiKey;

    /**
     * Set the API Key for SERankingController Class
     */
    public function __construct()
    {
        $this->apiKey = '66122f8ad1adb1c075c75aba3bd503a4a559fc7f';
    }

    /**
     * Get Results
     *
     * @param mixed $url
     */
    public function getResults($url)
    {
        $context = stream_context_create([
            'http' => [
                'method'        => 'GET',
                'ignore_errors' => true,
                'header'        => [
                    "Authorization: Token $this->apiKey",
                    'Content-Type: application/json; charset=utf-8',
                ],
            ],
        ]);
        $results    = file_get_contents('https://api4.seranking.com/' . $url, 0, $context);
        if (isset($http_response_header)) {
            preg_match('`HTTP/[0-9\.]+\s+([0-9]+)`', $http_response_header[0], $matches);
        }
        if (! $results) {
            echo 'Request failed!';
        } else {
            $results = json_decode($results);
        }

        return $results;
    }

    /**
     * Get Sites
     */
    public function getSites(): View
    {
        // $site_id = 1083512;
        $sites = $this->getResults('sites');

        return View(
            'se-ranking.sites',
            compact('sites')
        );
    }

    /**
     * Get KeyWords
     */
    public function getKeyWords(): View
    {
        $site_id = 1083512;
        if (! empty($_GET['keyword'])) {
            $keyword  = $_GET['keyword'];
            $keywords = SERanking::where('name', 'like', '%' . $keyword . '%')->get();
        } else {
            $keywords = $this->getResults('sites/' . $site_id . '/keywords');
        }
        SERanking::truncate();
        foreach ($keywords as $new_item) {
            SERanking::insert(
                [
                    'id'               => $new_item->id,
                    'name'             => $new_item->name,
                    'group_id'         => $new_item->group_id,
                    'link'             => $new_item->link,
                    'first_check_date' => $new_item->first_check_date,
                ]
            );
        }
        $keyword_stats = $this->getResults('sites/' . $site_id . '/positions');

        return View(
            'se-ranking.keywords',
            compact('keywords', 'keyword_stats')
        );
    }

    /**
     * Get Competitors
     *
     * @param mixed $id
     */
    public function getCompetitors($id = ''): View
    {
        $site_id           = 1083512;
        $keywords_pos_data = [];
        $competitors       = $this->getResults('competitors/site/' . $site_id);
        if (! empty($id)) {
            $keywords_pos_data = $this->getResults('competitors/' . $id . '/positions');

            return View(
                'se-ranking.comp-key-pos',
                compact('competitors', 'keywords_pos_data')
            );
        }

        return View(
            'se-ranking.competitors',
            compact('competitors', 'keywords_pos_data')
        );
    }

    /**
     * Get Analytics
     */
    public function getAnalytics(): View
    {
        $site_id   = 1083512;
        $analytics = $this->getResults('analytics/' . $site_id . '/potential');

        return View(
            'se-ranking.analytics',
            compact('analytics')
        );
    }

    /**
     * Get BackLinks
     */
    public function getBacklinks(): View
    {
        $site_id   = 1083512;
        $backlinks = $this->getResults('backlinks/' . $site_id . '/stat');

        return View(
            'se-ranking.backlinks',
            compact('backlinks')
        );
    }

    /**
     * Get Research Data
     */
    public function getResearchData(): View
    {
        $r_data = $this->getResults('research/overview?domain=sololuxury.co.in');

        return View(
            'se-ranking.research-data',
            compact('r_data')
        );
    }

    /**
     * Get Site Audit
     */
    public function getSiteAudit(): View
    {
        $site_id = 1083512;
        $audit   = $this->getResults('audit/' . $site_id . '/report');

        return View(
            'se-ranking.audit',
            compact('audit')
        );
    }
}
