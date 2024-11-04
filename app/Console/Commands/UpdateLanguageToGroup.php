<?php

namespace App\Console\Commands;

use App\Http\Controllers\LiveChatController;
use Illuminate\Console\Command;

class UpdateLanguageToGroup extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'UpdateLanguageToGroup';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     */

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $postURL = 'https://api.livechatinc.com/v3.2/configuration/action/list_groups';

        $postData = [
            'fields' => ['agent_priorities', 'routing_status'],
        ];
        $postData = json_encode($postData, true);
        $result = app(LiveChatController::class)->curlCall($postURL, $postData, 'application/json', true, 'POST');

        if ($result['err']) {
            dump(['status' => 'errors', 'errorMsg' => $result['err']], 403);
        } else {
            $response = json_decode($result['response']);
            $existing_themes = ['General', 'Lussolicious', 'o-labels.com', 'Luxury Space', 'Italybrandoutlets', 'AvoirChic', 'Brands & Labels', 'Shades Shop', 'Sololuxury', 'VeraLusso', 'Suv&Nat', 'TheFitEdit', 'Upeau'];
            $changed_themes = [];
            foreach ($response as $g) {
                dump($g->name);
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
                    $postURL = 'https://api.livechatinc.com/v2/properties/group/'.$g->id;
                    $postData = [
                        'language' => $lang_code,
                    ];
                    $postData = json_encode($postData, true);
                    $result = app(LiveChatController::class)->curlCall($postURL, $postData, 'application/json', true, 'PUT');
                    $response = json_decode($result['response']);
                    if (! isset($response->error)) {
                        dump($g->id.' '.$g->name.' == '.$lang_code.' lang updated. ', $response);
                    } else {
                        dump([$g->id.' '.$g->name.' == '.$lang_code.' lang error.', $response]);
                    }
                }
            }
            if (isset($response->error)) {
                dump(['status' => 'errors', $response], 403);
            } else {
                dump(['status' => 'success', 'responseData' => $changed_themes], 200);
            }
        }

        dd('languages have been updated.');
    }
}
