<?php

namespace App\Observers;

use App\ContentManageentEmail;
use App\Email;
use App\EmailAddress;
use App\Events\EmailReceivedAlert;
use App\GmailDataList;
use App\GmailDataMedia;
use App\Models\BlogCentralize;
use App\Models\EmailReceiverMaster;
use App\ResourceImage;
use Exception;

class EmailObserver
{
    /**
     * Handle the email "created" event.
     */
    public function created(Email $email): void
    {
        $this->checkEmailAlert($email);
        //Email Receiver Module
        $this->emailReceive($email);

        $this->gmailData($email);
    }

    /**
     * Handle the email "updated" event.
     */
    public function updated(Email $email): void
    {
        //
    }

    /**
     * Handle the email "deleted" event.
     */
    public function deleted(Email $email): void
    {
        //
    }

    /**
     * Handle the email "restored" event.
     */
    public function restored(Email $email): void
    {
        //
    }

    /**
     * Handle the email "force deleted" event.
     */
    public function forceDeleted(Email $email): void
    {
        //
    }

    private function emailReceive(Email $email)
    {
        //Resources
        try {
            $emailReceivRec = EmailReceiverMaster::where('module_name', 'resource')->first();
            if ($emailReceivRec && trim(strtolower($emailReceivRec->email)) == trim(strtolower($email->to))) {
                $json_configs = $emailReceivRec->configs;
                if ($json_configs) {
                    $configs = json_decode($json_configs);
                    if ($configs && $configs->cat) {
                        $resourceimg = new ResourceImage;
                        $resourceimg->cat_id = $configs->cat;
                        $resourceimg->sub_cat_id = $configs->sub_cat ? $configs->sub_cat : 0;
                        $resourceimg->images = '';
                        $resourceimg->url = '';
                        $resourceimg->description = $email->message;
                        $resourceimg->subject = $email->subject;
                        $resourceimg->sender = $email->from;
                        $resourceimg->created_at = date('Y-m-d H:i:s');
                        $resourceimg->updated_at = date('Y-m-d H:i:s');
                        $resourceimg->created_by = 'Email Receiver';
                        $resourceimg->is_pending = 1;
                        $resourceimg->save();
                    }
                }
            }
            //Resources
        } catch (Exception $e) {

            return response()->json(['status' => 400, 'message' => 'Opps! Something went wrong, Please try again.']);

        }
        //Blog
        try {
            $emailReceivRec = EmailReceiverMaster::where('module_name', 'blog')->first();
            if ($emailReceivRec && trim(strtolower($emailReceivRec->email)) == trim(strtolower($email->to))) {
                $centralBlog = new BlogCentralize;

                $centralBlog->title = $email->subject;
                $centralBlog->content = $email->message;
                $centralBlog->receive_from = $email->from;
                $centralBlog->created_at = date('Y-m-d H:i:s');
                $centralBlog->updated_at = date('Y-m-d H:i:s');
                $centralBlog->created_by = 'Email Receiver';
                $centralBlog->save();
            }

            //Blog
        } catch (Exception $e) {

            return response()->json(['status' => 400, 'message' => 'Opps! Something went wrong, Please try again.']);

        }
    }

    public function gmailData(Email $email)
    {
        $receiver_email = $email->to;
        $content_management_email = ContentManageentEmail::first();
        if ($content_management_email) {
            if ($receiver_email == $content_management_email->email) {
                $a = preg_match_all('/<a[^>]+href=([\'"])(?<href>.+?)\1[^>]*>/i', $email->message, $aTags);
                $img = preg_match_all('/<img[^>]+src=([\'"])(?<src>.+?)\1[^>]*>/i', $email->message, $imgTags);

                if ($a > 0) {
                    $gmail = new GmailDataList;
                    $gmail->sender = $email->from;
                    $gmail->domain = substr($email->from, strpos($email->from, '@') + 1);
                    $gmail->received_at = $email->created_at->format('m/d/Y');
                    $gmail->save();

                    for ($i = 0; $i < count($imgTags[0]); $i++) {
                        if (file_get_contents($imgTags['src'][$i]) != '') {
                            $gmail_media = new GmailDataMedia;
                            $gmail_media->gmail_data_list_id = $gmail->id;
                            $gmail_media->page_url = $aTags['href'][$i];
                            $gmail_media->images = $imgTags['src'][$i];
                            $gmail_media->save();
                        }
                    }
                }
            }
        }
    }

    private function checkEmailAlert(Email $email)
    {
        try {
            //Email Alerts
            $enabledAlertEmails = EmailAddress::where('email_alert', 1)->pluck('from_address')->toArray();
            if (count($enabledAlertEmails) && in_array($email->to, $enabledAlertEmails)) {
                EmailReceivedAlert::dispatch($email);
            }

            createEmailAlertLog($email);
        } catch (\Throwable $th) {
            //throw $th;
        }
    }
}
