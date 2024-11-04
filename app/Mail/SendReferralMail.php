<?php

namespace App\Mail;
use App\MailinglistTemplate;
use App\EmailAddress;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use App\Http\Controllers\Controller;
use Illuminate\Queue\SerializesModels;

class SendReferralMail extends Mailable
{
    use Queueable, SerializesModels;

    const STORE_ERP_WEBSITE = 15;

    public $referlink;

    public $referrer_email;

    public $referee_coupon;

    public $store_website_id;

    public $title;

    public $website;

    public $subject;

    public $fromMailer;

    public $controller;

    /**
     * Create a new message instance.
     *
     * @param mixed $data
     *
     * @return void
     */
    public function __construct($data)
    {
        $this->subject          = 'Refer A friend - Luxury Erp';
        $this->fromMailer       = 'customercare@sololuxury.co.in';
        $this->referlink        = isset($data['referlink']) ? $data['referlink'] : '';
        $this->referrer_email   = isset($data['referrer_email']) ? $data['referrer_email'] : '';
        $this->referee_coupon   = isset($data['referee_coupon']) ? $data['referee_coupon'] : '';
        $this->store_website_id = isset($data['store_website_id']) ? $data['store_website_id'] : '';
        $this->title            = ! empty($data['title']) ? $data['title'] : '';
        $this->website          = ! empty($data['website']) ? $data['website'] : '';

        $this->Controller = new Controller();
    }

    /**
     * Build the message.
     */
    public function build(): static
    {
        if ($this->store_website_id) {
            $emailAddress = EmailAddress::where('store_website_id', $this->store_website_id)->first();
            if ($emailAddress) {
                $this->fromMailer = $emailAddress->from_address;
            }
        } else {
            $emailAddress = EmailAddress::where('store_website_id', self::STORE_ERP_WEBSITE)->first();
            if ($emailAddress) {
                $this->fromMailer = $emailAddress->from_address;
            }
        }

        $data['title']          = $this->title;
        $data['referlink']      = $this->referlink;
        $data['referee_coupon'] = $this->referee_coupon;
        $data['website']        = $this->website;

        $template = MailinglistTemplate::getReferAFirendTemplate($this->store_website_id);
        if ($template) {
            if (! empty($template->mail_tpl)) {
                // need to fix the all email address
                return $this->from($this->fromMailer)
                    ->subject($template->subject)
                    ->view($template->mail_tpl, compact(
                        'data'
                    ));
            } else {
                $content = $template->static_template;

                return $this->from($this->fromMailer)
                    ->subject($this->subject)
                    ->view('emails.blank_content', compact(
                        'data'
                    ));
            }
        }
    }
}
