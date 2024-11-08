<?php

namespace App\Console\Commands;

use App\Account;
use App\Http\Controllers\InstagramPostsController;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Http\Request;

class InstagramHandler extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'instagram:handler';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'post images, likes, send request, accept request for instagram account';

    /**
     * Create a new command instance.
     */
    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $query = Account::query();
        $accounts = $query->orderByDesc('id')->get();
        foreach ($accounts as $key => $account) {
            $diff_in_minutes = 0;
            if (! empty($account->last_cron_time)) {
                $to = Carbon::createFromFormat('Y-m-d H:i:s', Carbon::now());
                $from = Carbon::createFromFormat('Y-m-d H:i:s', $account->last_cron_time);
                $diff_in_minutes = $to->diffInMinutes($from);
            }
            if ($diff_in_minutes > $account->frequency || $diff_in_minutes == 0) {
                $myRequest = new Request;
                $myRequest->setMethod('POST');
                $myRequest->request->add(['account_id' => $account->id]);
                $this->info($myRequest->account_id);
                $InstagramPostsController = new InstagramPostsController;
                $InstagramPostsController->likeUserPost($myRequest);
                $InstagramPostsController->sendRequest($myRequest);
                $InstagramPostsController->acceptRequest($myRequest);
                $get_images = [];
                $get_caption = [];
                $selected_images = [];
                $selected_caption = [];
                if (! empty($get_caption)) {
                    $selected_caption[] = $get_caption[0]['id'];
                }
                $images_selected_no = 2;
                foreach ($get_images as $key => $images) {
                    if ($key <= ($images_selected_no - 1)) {
                        $selected_images[] = $images;
                    }
                }
                $myRequest->request->add(['imageURI' => $selected_images]);
                $myRequest->request->add(['captions' => $selected_caption]);
                $account->last_cron_time = Carbon::createFromFormat('Y-m-d H:i:s', Carbon::now());
                $account->save();
            }
        }
    }
}
