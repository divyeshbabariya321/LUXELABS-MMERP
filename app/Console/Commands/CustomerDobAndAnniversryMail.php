<?php

namespace App\Console\Commands;

use App\CronJob;
use App\Customer;
use App\Email;
use App\EmailAddress;
use App\Jobs\SendEmail;
use App\Mail\DobAndAnniversaryMail;
use App\MailinglistTemplate;
use App\MailinglistTemplateCategory;
use App\Order;
use Exception;
use Illuminate\Console\Command;

class CustomerDobAndAnniversryMail extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'customer:dob-and-anniversary-mail';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send mail to customer on birthdays and anniversaries';

    /**
     * Create a new command instance.
     */

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        //Birthdays
        $customerBirtdays = Customer::whereDay('dob', date('d'))
            ->WhereMonth('dob', date('m'))
            ->get();

        $mailingListCategory = MailinglistTemplateCategory::where('title', 'Birthday')->first();
        if ($mailingListCategory) {
            foreach ($customerBirtdays as $customer) {
                try {
                    if ($customer->store_website_id) {
                        $templateData = MailinglistTemplate::where('category_id', $mailingListCategory->id)->where('store_website_id', $customer->store_website_id)->first();
                        $storeEmailAddress = EmailAddress::where('store_website_id', $customer->store_website_id)->first();
                    } else {
                        $templateData = MailinglistTemplate::where('category_id', $mailingListCategory->id)->first();
                        $storeEmailAddress = EmailAddress::first();
                    }
                    if ($templateData && $storeEmailAddress && $customer->email) {
                        if ($templateData->static_template) {
                            $arrToReplace = ['{FIRST_NAME}'];
                            $valToReplace = [$customer->name];
                            $bodyText = str_replace($arrToReplace, $valToReplace, $templateData->static_template);
                        } else {
                            $bodyText = @(string) view($templateData->mail_tpl);
                        }
                        $emailData['subject'] = $templateData->subject;
                        $emailData['template'] = $bodyText;
                        $emailData['from'] = $storeEmailAddress->from_address;
                        $emailClass = (new DobAndAnniversaryMail($emailData))->build();
                        $order = Order::where('customer_id', $customer->id)->latest()->first();
                        $email = \App\Email::create([
                            'model_id' => $customer->id,
                            'model_type' => \App\Customer::class,
                            'from' => $emailClass->fromMailer,
                            'to' => $customer->email,
                            'subject' => $templateData->subject,
                            'message' => $emailClass->render(),
                            'template' => 'birthday-mail',
                            'additional_data' => $order->id,
                            'status' => 'pre-send',
                            'is_draft' => 1,
                        ]);

                        SendEmail::dispatch($email)->onQueue('send_email');
                    }
                } catch (Exception $e) {
                    CronJob::insertLastError($this->signature, $e->getMessage());

                    continue;
                }
            }
        }
        //Anniversaries
        $customerAnniversaries = Customer::whereDay('wedding_anniversery', date('d'))
            ->WhereMonth('wedding_anniversery', date('m'))
            ->get();
        $mailingListCategory = MailinglistTemplateCategory::where('title', 'Wedding Anniversary')->first();
        if ($mailingListCategory) {
            foreach ($customerAnniversaries as $customer) {
                try {
                    if ($customer->store_website_id) {
                        $templateData = MailinglistTemplate::where('category_id', $mailingListCategory->id)->where('store_website_id', $customer->store_website_id)->first();
                        $storeEmailAddress = EmailAddress::where('store_website_id', $customer->store_website_id)->first();
                    } else {
                        $templateData = MailinglistTemplate::where('category_id', $mailingListCategory->id)->first();
                        $storeEmailAddress = EmailAddress::first();
                    }
                    if ($templateData && $storeEmailAddress && $customer->email) {
                        if ($templateData->static_template) {
                            $arrToReplace = ['{FIRST_NAME}'];
                            $valToReplace = [$customer->name];
                            $bodyText = str_replace($arrToReplace, $valToReplace, $templateData->static_template);
                        } else {
                            $bodyText = @(string) view($templateData->mail_tpl);
                        }
                        $emailData['subject'] = $templateData->subject;
                        $emailData['template'] = $bodyText;
                        $emailData['from'] = $storeEmailAddress->from_address;

                        $emailClass = (new DobAndAnniversaryMail($emailData))->build();

                        $email = Email::create([
                            'model_id' => $customer->id,
                            'model_type' => Customer::class,
                            'from' => $emailClass->fromMailer,
                            'to' => $customer->email,
                            'subject' => $templateData->subject,
                            'message' => $emailClass->render(),
                            'template' => 'wedding-anniversery-mail',
                            'additional_data' => $order->id,
                            'status' => 'pre-send',
                            'is_draft' => 1,
                        ]);

                        SendEmail::dispatch($email)->onQueue('send_email');
                    }
                } catch (Exception $e) {
                    CronJob::insertLastError($this->signature, $e->getMessage());

                    continue;
                }
            }
        }
    }
}
