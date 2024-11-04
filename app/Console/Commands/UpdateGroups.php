<?php

namespace App\Console\Commands;

use App\StoreWebsite;
use App\WebsiteStoreView;
use Illuminate\Console\Command;

class UpdateGroups extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'UpdateGroups';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'delete old groups and add new groups.';

    /**
     * Create a new command instance.
     */

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $this->deleteOldGroups();
        $groupArray = $this->createNewGroups();
        $this->assignGroupsToStores($groupArray);
        $this->assignGroupsToRemainedStores($groupArray);
    }

    private function deleteOldGroups(): void
    {
        $existingThemes = [
            'General', 'AvoirChic', 'Brands & Labels', 'Shades Shop', 'ShadesShop',
            'Sololuxury', 'VeraLusso', 'Suv&Nat', 'TheFitEdit', 'Upeau',
            'o-labels.com', 'Luxury Space', 'TheFitEdit', 'Italybrandoutlets', 'Lussolicious',
        ];

        $postURL = 'https://api.livechatinc.com/v3.2/configuration/action/list_groups';
        $postData = json_encode(['fields' => ['agent_priorities', 'routing_status']], true);

        $result = $this->callLiveChatApi($postURL, $postData, 'POST');
        if ($result['err']) {
            dump(['status' => 'errors', 'errorMsg' => $result['err']], 403);

            return;
        }

        $response = json_decode($result['response']);
        foreach ($response as $group) {
            if (in_array(str_replace('theme_', '', $group->name), $existingThemes)) {
                dump($group->name.' not deleted');
            } else {
                $this->deleteGroup($group);
            }
        }
    }

    private function deleteGroup($group): void
    {
        $postURL = 'https://api.livechatinc.com/v3.2/configuration/action/delete_group';
        $postData = json_encode(['id' => $group->id], true);
        $result = $this->callLiveChatApi($postURL, $postData, 'POST');

        if ($result['err']) {
            dd(response()->json(['status' => 'errors', 'errorMsg' => $result['err']], 403));
        }

        $response = json_decode($result['response']);
        if (isset($response->error)) {
            dd(response()->json(['status' => 'errors', $response], 403));
        }

        dump($group->name.' '.$group->id.' deleted');
    }

    private function createNewGroups(): array
    {
        $groupArray = [];
        $websiteStoreViews = WebsiteStoreView::get();
        foreach ($websiteStoreViews as $w) {
            if ($this->shouldSkipWebsiteStoreView($w)) {
                continue;
            }

            $webName = $this->generateWebName($w);
            if (array_key_exists($webName, $groupArray)) {
                dump($webName.' group already exists');

                continue;
            }

            $groupId = $this->createGroup($webName);
            if ($groupId) {
                $groupArray[$webName] = $groupId;
                $this->updateWebsiteStoreViewGroup($w, $groupId);
            }
        }

        return $groupArray;
    }

    private function createGroup(string $webName): ?string
    {
        $postURL = 'https://api.livechatinc.com/v3.2/configuration/action/create_group';
        $postData = json_encode([
            'name' => $webName,
            'agent_priorities' => ['buying@amourint.com' => 'normal'],
        ], true);

        $result = $this->callLiveChatApi($postURL, $postData, 'POST');
        if ($result['err']) {
            dump(['name' => $webName, 'status' => 'errors', 'errorMsg' => $result['err']], 403);

            return null;
        }

        $response = json_decode($result['response']);
        if (isset($response->error)) {
            dump(['name' => $webName, 'status' => 'errors', $response], 403);

            return null;
        }

        dump(['name' => $webName, 'status' => 'success', 'responseData' => $response, 'code' => 200], 200);

        return $response->id;
    }

    private function assignGroupsToStores(array $groupArray): void
    {
        $websiteStoreViews = WebsiteStoreView::whereNull('store_group_id')->get();
        foreach ($websiteStoreViews as $w) {
            if ($this->shouldSkipWebsiteStoreView($w)) {
                continue;
            }

            $webName = $this->generateWebName($w);
            if (array_key_exists($webName, $groupArray)) {
                dump(['websiteStoreView-id' => $w->id]);
            }
        }
    }

    private function assignGroupsToRemainedStores(array $groupArray): void
    {
        $websiteStoreViews = WebsiteStoreView::whereNull('store_group_id')->get();
        foreach ($websiteStoreViews as $w) {
            if ($this->shouldSkipWebsiteStoreView($w)) {
                continue;
            }

            $webName = $this->generateWebName($w, true);
            if (array_key_exists($webName, $groupArray)) {
                $this->listAndAssignGroup($webName, $w);
            }
        }
    }

    private function listAndAssignGroup(string $webName, $w): void
    {
        $postURL = 'https://api.livechatinc.com/v3.2/configuration/action/list_groups';
        $postData = json_encode(['fields' => ['agent_priorities', 'routing_status']], true);
        $result = $this->callLiveChatApi($postURL, $postData, 'POST');

        if ($result['err']) {
            dump(['status' => 'errors', 'errorMsg' => $result['err']], 403);

            return;
        }

        $response = json_decode($result['response']);
        foreach ($response as $g) {
            if ($g->name == $webName) {
                dump([$g->name, $w->id, $g->id]);
            }
        }
    }

    private function callLiveChatApi(string $url, string $data, string $method)
    {
        return app(\App\Http\Controllers\LiveChatController::class)->curlCall($url, $data, 'application/json', true, $method);
    }

    private function shouldSkipWebsiteStoreView($w): bool
    {
        return $w->websiteStore == null ||
            $w->websiteStore->website == null ||
            $w->websiteStore->website->store_website_id == null ||
            $w->code == 1;
    }

    private function generateWebName($w, bool $capitalize = false): string
    {
        $storeWeb = StoreWebsite::withTrashed()->find($w->websiteStore->website->store_website_id);
        $code = explode('-', $w->code)[1];
        $title = $capitalize ? ucwords(strtolower($storeWeb->title)) : $storeWeb->title;

        return $title.'_'.$code;
    }

    private function updateWebsiteStoreViewGroup($w, string $groupId): void
    {
        $websiteStoreView = WebsiteStoreView::where('id', $w->id)->first();
        if ($websiteStoreView) {
            $websiteStoreView->store_group_id = $groupId;
            $websiteStoreView->save();
        }
    }
}
