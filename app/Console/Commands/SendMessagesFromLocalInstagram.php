<?php

namespace App\Console\Commands;

use App\LogRequest;
use Illuminate\Console\Command;
use InstagramAPI\Instagram;

class SendMessagesFromLocalInstagram extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'instagram:process-users-message';

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
        $ch = curl_init();
        $username = Config('instagram.admin_account');
        $startTime = date('Y-m-d H:i:s', LARAVEL_START);

        $url = 'https://erp.theluxuryunlimited.com/api/instagram/get-comments-list/'.$username;

        // set url
        curl_setopt($ch, CURLOPT_URL, $url);

        //return the transfer as a string
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

        // $output contains the output string
        $output = curl_exec($ch);
        $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        LogRequest::log($startTime, $url, 'POST', [], json_decode($output), $httpcode, Commands\SendMessagesFromLocalInstagram::class, 'handle');

        // close curl resource to free up system resources
        curl_close($ch);

        $messages = json_decode($output);

        if ($messages->result == true) {
            $instagram = new Instagram;

            $username = Config('instagram.admin_account');
            $password = Config('instagram.admin_password');

            if ($username != '' && $password != '') {
                $instagram->login($username, $password);
            } else {
                dd(response()->json([
                    'status' => 'Not Able To Connect',
                ]));
            }

            $comments = $messages->comments;

            foreach ($comments as $comment) {
                $instagram->media->comment($comment->post_id, $comment->message);

                //Send Request to ERP
                $detail = [
                    'id' => $comment->id,
                ];

                $ch = curl_init('https://erp.theluxuryunlimited.com/api/instagram/comment-sent');
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_POSTFIELDS, $detail);

                // execute!
                $response = curl_exec($ch);
                $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                LogRequest::log($startTime, $url, 'POST', json_encode($detail), json_decode($response), $httpcode, SendMessagesFromLocalInstagram::class, 'handle');

                // close the connection, release resources used
                curl_close($ch);
            }
        } else {
            dump('No Messsges Found');
        }
    }
}
