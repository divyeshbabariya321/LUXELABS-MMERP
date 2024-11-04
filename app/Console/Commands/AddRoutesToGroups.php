<?php

namespace App\Console\Commands;

use App\Http\Controllers\LiveChatController;
use App\Models\GroupRoute;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class AddRoutesToGroups extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'AddRoutesToGroups';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    const CONTENT_TYPE_JSON = 'application/json';

    /**
     * Create a new command instance.
     */

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        // Part-1
        $existing_routes = GroupRoute::get();
        foreach ($existing_routes as $r) {
            dump($r->route_id);
            $postURL = 'https://api.livechatinc.com/v3.3/configuration/action/delete_auto_access';

            $postData = [
                'id' => $r->route_id,
            ];
            $postData = json_encode($postData, true);
            $result = app(LiveChatController::class)->curlCall($postURL, $postData, self::CONTENT_TYPE_JSON, true, 'POST');
            $existing_route = DB::table('group_routes')->where('route_id', $r->route_id)->delete();

            dump([$result, $existing_route]);
        }
        dump('routes deleted');
        // Part-2 Create routes and update langauages to group
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
                $all_themes_ids[$g->name] = $g->id;
                if (! in_array(str_replace('theme_', '', $g->name), $existing_themes)) {
                    $data = explode('_', $g->name);
                    if (count($data) != 2) {
                        dump($g->name.' skipped');

                        continue;
                    }
                    $lang_code = $data[1];
                    if ($lang_code == 'kr') {
                        $lang_code = 'ko';
                    } elseif ($lang_code == 'jp') {
                        $lang_code = 'ja';
                    } elseif ($lang_code == 'ge') {
                        $lang_code = 'ka';
                    }
                    $web_name = $data[0];
                    if ($web_name == 'Vera Lusso') {
                        $web_name = 'veralusso';
                    } elseif ($web_name == 'Brands & Labels') {
                        $web_name = 'brands-labels';
                    } elseif ($web_name == 'AvoirChic') {
                        $web_name = 'avoir-chic';
                    } elseif ($web_name == 'SOLO LUXURY') {
                        $web_name = 'sololuxury';
                    } elseif ($web_name == 'Suv&Nat') {
                        $web_name = 'suvandnat';
                    } elseif ($web_name == 'o-labels') {
                        $web_name = 'o-labels';
                    } elseif ($web_name == 'Italy brand outlets') {
                        $web_name = 'italybrandoutlets.myshopify.com';
                    } elseif ($web_name == 'Shades Shop') {
                        $web_name = 'the-shades-shop-com.myshopify.com';
                    } elseif ($web_name == 'TheFitEdit') {
                        $web_name = 'thefitedit';
                    } elseif ($web_name == 'Upeau') {
                        $web_name = 'upeau';
                    }
                    dump($web_name);
                    // Update language to group
                    $postURL = 'https://api.livechatinc.com/v2/properties/group/'.$g->id;
                    $postData = [
                        'language' => $lang_code,
                    ];
                    $postData = json_encode($postData, true);
                    $result = app(LiveChatController::class)->curlCall($postURL, $postData, self::CONTENT_TYPE_JSON, true, 'PUT');
                    $response = json_decode($result['response']);
                    if (! isset($response->error)) {
                        dump($g->id.' '.$g->name.' == '.$lang_code.' lang updated.');
                    } else {
                        dump([$g->id.' '.$g->name.' == '.$lang_code.' lang error.', $response]);
                    }
                    //Create route fo group
                    $postURL = 'https://api.livechatinc.com/v3.3/configuration/action/add_auto_access';
                    $domain_values['value'] = $web_name;
                    $url_values['value'] = '-'.$data[1];
                    $postData = [
                        'description' => $g->name,
                        'access' => [
                            'groups' => [$g->id],
                        ],
                        'conditions' => [
                            'domain' => [
                                'values' => [$domain_values],
                            ],
                            'url' => [
                                'values' => [$url_values],
                            ],
                        ],
                        'next_id' => '310b71d0e6c6dd5809f8535a6f055b17',
                    ];
                    $postData = json_encode($postData, true);
                    $result = app(LiveChatController::class)->curlCall($postURL, $postData, self::CONTENT_TYPE_JSON, true, 'POST');
                    $response = json_decode($result['response']);
                    dump($response);
                    if (! isset($response->error)) {
                        dump($g->id.' '.$g->name.' == '.$lang_code.' route updated.');
                        GroupRoute::updateOrInsert([
                            'group_id' => $g->id,
                        ], [
                            'group_id' => $g->id,
                            'route_id' => $response->id,
                            'route_name' => $g->name,
                            'domain' => $domain_values['value'],
                            'url' => $url_values['value'],
                        ]);
                    }
                } else {
                    dump($g->name.' skipped');
                }
            }
            if (isset($response->error)) {
                dump(['status' => 'errors', $response], 403);
            } else {
                dump(['status' => 'success', 'responseData' => $changed_themes], 200);
            }
        }
        dump(['existing_themes_ids' => $existing_themes_ids, 'all_themes_ids' => $all_themes_ids, 'changed_themes' => $changed_themes]);
    }
}
