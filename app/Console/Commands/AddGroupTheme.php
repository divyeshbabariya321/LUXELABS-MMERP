<?php

namespace App\Console\Commands;

use App\Http\Controllers\LiveChatController;
use App\WebsiteStoreView;
use Illuminate\Console\Command;

class AddGroupTheme extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'AddGroupTheme';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'AddGroupTheme';

    const CONTENT_TYPE_JSON = 'application/json';

    /**
     * Create a new command instance.
     */

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $existing_themes_ids = [];
        $all_themes_ids = [];

        // Part - 1

        $postURL = 'https://api.livechatinc.com/v3.2/configuration/action/list_groups';

        $postData = [
            'fields' => ['agent_priorities', 'routing_status'],
        ];
        $postData = json_encode($postData, true);
        $result = app(LiveChatController::class)->curlCall($postURL, $postData, self::CONTENT_TYPE_JSON, true, 'POST');

        if ($result['err']) {
            dump(['status' => 'errors', 'errorMsg' => $result['err']], 403);
        } else {
            $response = json_decode($result['response']);
            $existing_themes = ['General', 'Lussolicious', 'o-labels.com', 'Luxury Space', 'Italybrandoutlets', 'AvoirChic', 'Brands & Labels', 'Shades Shop', 'Sololuxury', 'VeraLusso', 'Suv&Nat', 'TheFitEdit', 'Upeau'];
            $changed_themes = [];
            foreach ($response as $g) {
                dump($g->name);
                $all_themes_ids[$g->name] = $g->id;
                if (in_array(str_replace('theme_', '', $g->name), $existing_themes)) {
                    $postURL = 'https://api.livechatinc.com/v3.2/configuration/action/update_group';
                    $postData = [
                        'id' => $g->id,
                        'name' => 'theme_'.str_replace('theme_', '', $g->name),
                        'language_code' => $g->language_code,
                        'agent_priorities' => $g->agent_priorities,
                        'routing_status' => $g->routing_status,
                    ];
                    $postData = json_encode($postData, true);
                    app(\App\Http\Controllers\LiveChatController::class)->curlCall($postURL, $postData, self::CONTENT_TYPE_JSON, true, 'POST');
                    if (! isset($response->error)) {
                        $changed_themes[] = 'theme_'.str_replace('theme_', '', $g->name);
                        $existing_themes_ids[$g->name] = $g->id;
                    }
                }
            }
            if (isset($response->error)) {
                dump(['status' => 'errors', $response], 403);
            } else {
                dump(['status' => 'success', 'responseData' => $changed_themes], 200);
            }
        }
        dump(['existing_themes_ids' => $existing_themes_ids, 'all_themes_ids' => $all_themes_ids, 'changed_themes' => $changed_themes]);

        // Part - 2

        $ref_themes = [
            'General' => 0,
            'Lussolicious' => 1,
            'theme_VeraLusso' => 2,
            'theme_AvoirChic' => 3,
            'o-labels.com' => 4,
            'theme_Sololuxury' => 5,
            'theme_Suv&Nat' => 6,
            'theme_Brands & Labels' => 7,
            'Luxury Space' => 8,
            'theme_Shades Shop' => 9,
            'theme_Upeau' => 10,
            'theme_TheFitEdit' => 11,
            'Italybrandoutlets' => 12,
        ];
        foreach ($all_themes_ids as $key => $t) {
            if (in_array($key, array_keys($ref_themes))) {
                dump($key.' ref theme');
            } else {
                $theme_name = substr($key, 0, -3);

                if ($theme_name == 'Vera Lusso' || $theme_name == 'VERA LUSSO') {
                    $theme_name = 'theme_VeraLusso';
                } elseif ($theme_name == 'Brands & Labels') {
                    $theme_name = 'theme_Brands & Labels';
                } elseif ($theme_name == 'AvoirChic') {
                    $theme_name = 'theme_AvoirChic';
                } elseif ($theme_name == 'SOLO LUXURY') {
                    $theme_name = 'theme_Sololuxury';
                } elseif ($theme_name == 'VeraLusso') {
                    $theme_name = 'theme_VeraLusso';
                } elseif ($theme_name == 'Suv&Nat') {
                    $theme_name = 'theme_Suv&Nat';
                } elseif ($theme_name == 'o-labels') {
                    $theme_name = 'o-labels.com';
                } elseif ($theme_name == 'Italy brand outlets') {
                    $theme_name = 'Italybrandoutlets';
                } elseif ($theme_name == 'Shades Shop') {
                    $theme_name = 'theme_Shades Shop';
                } elseif ($theme_name == 'TheFitEdit') {
                    $theme_name = 'theme_TheFitEdit';
                } elseif ($theme_name == 'Upeau') {
                    $theme_name = 'theme_Upeau';
                } else {
                    dump($theme_name.' theme not exist');

                    continue;
                }

                $ref_group_id = $ref_themes[$theme_name];
                $postURL1 = 'https://api.livechatinc.com/v2/properties/group/'.$ref_group_id;
                $result1 = app(LiveChatController::class)->curlCall($postURL1, [], self::CONTENT_TYPE_JSON, true, 'GET');
                $postURL2 = 'https://api.livechatinc.com/v2/properties/group/'.$t;

                app(LiveChatController::class)->curlCall($postURL2, $result1['response'], self::CONTENT_TYPE_JSON, true, 'PUT');

                WebsiteStoreView::where('store_group_id', $t)->update(['ref_theme_group_id' => $ref_group_id]);
                dump($key.' '.$t.' '.$theme_name.' is updated '.$ref_group_id);
            }
        }
        dd('ref groups updated');
    }
}
