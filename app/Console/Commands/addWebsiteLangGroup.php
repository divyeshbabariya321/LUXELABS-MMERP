<?php

namespace App\Console\Commands;

use App\Http\Controllers\LiveChatController;
use App\WebsiteStoreView;
use Illuminate\Console\Command;

class addWebsiteLangGroup extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'addWebsiteLangGroup';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'addWebsiteLangGroup';

    /**
     * Create a new command instance.
     */

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $websiteStoreViews = WebsiteStoreView::whereNull('store_group_id')->get();
        foreach ($websiteStoreViews as $v) {
            dump($v->name.'_'.$v->code);

            $postURL = 'https://api.livechatinc.com/v3.2/configuration/action/create_group';

            $postData = [
                'name' => $v->name.'_'.$v->code,
                'agent_priorities' => [
                    'buying@amourint.com' => 'normal',
                ],
            ];

            $postData = json_encode($postData, true);

            $result = app(LiveChatController::class)->curlCall($postURL, $postData, 'application/json', true, 'POST');
            if ($result['err']) {
                dump(['status' => 'errors', 'errorMsg' => $result['err']], 403);
            } else {
                $response = json_decode($result['response']);
                if (isset($response->error)) {
                    dump(['status' => 'errors', $response], 403);
                } else {
                    $websiteStoreView = WebsiteStoreView::where('id', $v->id)->first();

                    if ($websiteStoreView) {
                        $websiteStoreView->store_group_id = $response->id;
                        $websiteStoreView->save();
                    }
                    dump(['status' => 'success', 'responseData' => $response, 'code' => 200], 200);
                }
            }
        }

        $websiteStoreViews = WebsiteStoreView::whereNull('store_group_id')->get();
        foreach ($websiteStoreViews as $v) {
            $ref_websiteStoreView = WebsiteStoreView::where('name', $v->name)->where('code', $v->code)->first();
            if ($ref_websiteStoreView) {
                WebsiteStoreView::where('id', $v->id)->update(['store_group_id' => $ref_websiteStoreView->store_group_id]);
                dump($v->id.' is updated');
            }
        }
    }
}
