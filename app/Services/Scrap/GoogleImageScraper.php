<?php

namespace App\Services\Scrap;

use Wa72\HtmlPageDom\HtmlPageCrawler;

class GoogleImageScraper extends Scraper
{
    private const GOOGLE_IMAGE_SEARCH_URL = [
        'https://www.google.com/search?tbs=isz:l&tbm=isch&source=lnms&q={query_string}&&chips=q:{query_string},g_1:{chip_value}',
    ];

    public function scrapGoogleImages($q, $chip_value, $outputCount)
    {
        $query = str_replace('{query_string}', $q, self::GOOGLE_IMAGE_SEARCH_URL[0]);
        $query = str_replace('{chip_value}', $chip_value, $query);
        $body  = $this->getContent($query);

        $c = new HtmlPageCrawler($body);

        // check if google html page has td with id "e3goi"
        $google_div_id = 'td.e3goi';
        if ($c->filter('body')->filter($google_div_id)->getInnerHtml()) {
            $imageJson = $c->filter('body')->filter($google_div_id)->filter('img');
            $images    = [];
            foreach ($imageJson as $key => $image) {
                foreach ($image->attributes as $att => $image) {
                    if ($image->name == 'src') {
                        $images[] = $image->value ?? null;
                    }
                }

                if ($key + 1 >= $outputCount) {
                    break;
                }
            }

            return $images;
        } else {
            return false;
        }
    }
}
