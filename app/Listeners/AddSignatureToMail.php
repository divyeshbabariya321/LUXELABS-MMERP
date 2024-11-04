<?php

namespace App\Listeners;

use App\EmailAddress;

class AddSignatureToMail
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(object $event): void
    {
        $message   = $event->message;
        $fromemail = $message->getFrom('email');
        foreach ($fromemail as $key => $val) {
            $femail = $key;
        }

        $body      = $message->getBody();
        $signature = '';
        $email     = EmailAddress::where('from_address', $femail)->first();
        if ($email) {
            if ($email->signature_name != '') {
                $signature .= '<br>' . $email->signature_name;
            }

            if ($email->signature_title != '') {
                $signature .= '<br>' . $email->signature_title;
            }

            if ($email->signature_email != '') {
                $signature .= '<br>' . $email->signature_email;
            }

            if ($email->signature_phone != '') {
                $signature .= '<br>' . $email->signature_phone;
            }

            if ($email->signature_website != '') {
                $signature .= '<br>' . $email->signature_website;
            }

            if ($email->signature_address != '') {
                $signature .= '<br>' . $email->signature_address;
            }

            if ($email->signature_logo != '') {
                $signature .= "<br><img src='" . url('/') . '/uploads/' . $email->signature_logo . "'>";
            }

            if ($email->signature_image != '') {
                $signature .= "<br><img src='" . url('/') . '/uploads/' . $email->signature_image . "'>";
            }

            if ($email->signature_social != '') {
                $signature .= '<br>' . $email->signature_social;
            }
        }

        // $event->message->setBody($body . $signature);
        $event->message->setBody($body); // String value not supported for signature $signature setbody method
    }
}
