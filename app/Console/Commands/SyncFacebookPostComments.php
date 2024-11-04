<?php

namespace App\Console\Commands;

use App\CronJob;
use Carbon\Carbon;
use App\CronJobReport;
use App\Social\SocialPost;
use Illuminate\Console\Command;
use Exception;

class SyncFacebookPostComments extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'facebook:sync-comments {post_id}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fetch the facebook post comments based on post id';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        try {
            $report = CronJobReport::create([
                'signature'  => $this->signature,
                'start_time' => Carbon::now(),
            ]);
            $postId = $this->argument('post_id');
            $post   = SocialPost::where('ref_post_id', $postId)->with('account')->firstOrFail();

            $pageInfoParams = [
                'endpoint_path' => $postId . '/comments',
                'fields'        => '',
                'access_token'  => $post->account->page_token,
                'request_type'  => 'GET',
            ];

            $response = getFacebookResults($pageInfoParams);

            if (isset($response['data']['data'])) {
                $comments = $response['data']['data'];
                foreach ($comments as $comment) {
                    $post->comments()->updateOrCreate(['comment_ref_id' => $comment['id']], [
                        'message'           => $comment['message'] ?? '',
                        'commented_by_id'   => $comment['from']['id'],
                        'commented_by_user' => $comment['from']['name'],
                        'ref_post_id'       => $post['id'],
                        'config_id'         => $post->account->id,
                        'created_at'        => Carbon::parse($comment['created_time']),
                    ]);
                }
            }

            $report->update(['end_time' => Carbon::now()]);
        } catch (Exception $e) {
            CronJob::insertLastError($this->signature, $e->getMessage());
        }
    }
}
