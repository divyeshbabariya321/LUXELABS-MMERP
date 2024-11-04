<?php

namespace App\Jobs;
use App\Waybillinvoice;
use App\Waybill;
use App\Vendor;
use App\User;
use App\Tickets;
use App\Supplier;
use App\Helpers\MessageHelper;
use App\Customer;
use App\CronJob;
use App\ChatMessage;

use Illuminate\Support\Facades\Log;
use App\Email;
use App\CashFlow;
use Carbon\Carbon;
use App\EmailRunHistories;
use Illuminate\Bus\Queueable;
use Webklex\PHPIMAP\ClientManager;
use Illuminate\Queue\SerializesModels;
use EmailReplyParser\Parser\EmailParser;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Exception;

class FetchEmail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 5;

    public $backoff = 5;

    /**
     * Create a new job instance.
     *
     * @param public $emailAddress
     *
     * @return void
     */
    public function __construct(public $emailAddress)
    {
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(): void
    {
        $emailAddress = $this->emailAddress;
        try {
            $cm   = new ClientManager();
            $imap = $cm->make([
                'host'          => $emailAddress->host,
                'port'          => $emailAddress->port,
                'encryption'    => $emailAddress->encryption,
                'validate_cert' => false,
                'username'      => $emailAddress->username,
                'password'      => $emailAddress->password,
                'protocol'      => 'imap',
            ]);

            $imap->connect();

            $types = [
                'inbox' => [
                    'inbox_name' => 'INBOX',
                    'direction'  => 'from',
                    'type'       => 'incoming',
                ],
                'sent' => [
                    'inbox_name' => 'INBOX.Sent',
                    'direction'  => 'to',
                    'type'       => 'outgoing',
                ],
            ];

            $is_module_available = 0;
            $available_models    = [
                'supplier' => Supplier::class, 'vendor' => Vendor::class,
                'customer' => Customer::class, 'users' => User::class,
            ];
            $email_list = [];
            foreach ($available_models as $key => $value) {
                $email_list[$value] = $value::whereNotNull('email')->pluck('id', 'email')->unique()->all();
            }

            foreach ($types as $type) {
                dump('Getting emails for: ' . $type['type']);

                $inbox = $imap->getFolder($type['inbox_name']);
                if ($type['type'] == 'incoming') {
                    $latest_email = Email::select('created_at')->where('to', $emailAddress->from_address)->where('type', $type['type'])->latest()->first();
                } else {
                    $latest_email = Email::select('created_at')->where('from', $emailAddress->from_address)->where('type', $type['type'])->latest()->first();
                }

                $latest_email_date = $latest_email ? Carbon::parse($latest_email->created_at) : false;

                dump('Last received at: ' . ($latest_email_date ?: 'never'));

                if ($latest_email_date) {
                    $emails = ($inbox) ? $inbox->messages()->where('SINCE', $latest_email_date->subDays(1)->format('d-M-Y')) : '';
                } else {
                    $emails = ($inbox) ? $inbox->messages() : '';
                }
                if ($emails) {
                    $emails = $emails->all()->get();
                    foreach ($emails as $email) {
                        try {
                            $reference_id = $email->references;
                            $origin_id    = $email->message_id;

                            // Skip if message is already stored
                            if (Email::where('origin_id', $origin_id)->count() > 0) {
                                continue;
                            }

                            // check if email has already been received

                            $textContent = $email->getTextBody();
                            if ($email->hasHTMLBody()) {
                                $content = $email->getHTMLBody();
                            } else {
                                $content = $email->getTextBody();
                            }

                            $email_subject = $email->getSubject();
                            Log::channel('customer')->info('Subject  => ' . $email_subject);

                            $attachments_array = [];
                            $attachments       = $email->getAttachments();
                            $fromThis          = $email->getFrom()[0]->mail;
                            $attachments->each(function ($attachment) use (&$attachments_array, $fromThis, $email_subject) {
                                $attachment->name = preg_replace("/[^a-z0-9\_\-\.]/i", '', $attachment->name);
                                file_put_contents(storage_path('app/files/email-attachments/' . $attachment->name), $attachment->content);
                                $path = 'email-attachments/' . $attachment->name;

                                $attachments_array[] = $path;

                                /*start 3215 attachment fetch from DHL mail */
                                Log::channel('customer')->info('Match Start  => ' . $email_subject);

                                $findFromEmail = explode('@', $fromThis);
                                if (strpos(strtolower($email_subject), 'your copy invoice') !== false && isset($findFromEmail[1]) && (strtolower($findFromEmail[1]) == 'dhl.com')) {
                                    Log::channel('customer')->info('Match Found  => ' . $email_subject);
                                    $this->getEmailAttachedFileData($attachment->name);
                                }
                                /*end 3215 attachment fetch from DHL mail */
                            });

                            $from = $email->getFrom()[0]->mail;
                            $to   = array_key_exists(0, $email->getTo()->toArray()) ? $email->getTo()[0]->mail : $email->getReplyTo()[0]->mail;

                            // Model is sender if its incoming else its receiver if outgoing
                            if ($type['type'] == 'incoming') {
                                $model_email = $from;
                            } else {
                                $model_email = $to;
                            }

                            // Get model id and model type

                            extract($this->getModel($model_email, $email_list));

                            $subject = explode('#', $email_subject);
                            if (isset($subject[1]) && ! empty($subject[1])) {
                                $findTicket = Tickets::where('ticket_id', $subject[1])->first();
                                if ($findTicket) {
                                    $model_id   = $findTicket->id;
                                    $model_type = Tickets::class;
                                }
                            }

                            $mailData = explode('@', $from);
                            $name     = $mailData['0'];

                            $params = [
                                'model_id'        => $model_id,
                                'model_type'      => $model_type,
                                'origin_id'       => $origin_id,
                                'reference_id'    => $reference_id,
                                'type'            => $type['type'],
                                'seen'            => isset($email->getFlags()['seen']) ? $email->getFlags()['seen'] : 0,
                                'from'            => $email->getFrom()[0]->mail,
                                'to'              => array_key_exists(0, $email->getTo()->toArray()) ? $email->getTo()[0]->mail : $email->getReplyTo()[0]->mail,
                                'subject'         => $email->getSubject(),
                                'message'         => $content,
                                'template'        => 'customer-simple',
                                'additional_data' => json_encode(['attachment' => $attachments_array]),
                                'created_at'      => $email->getDate(),
                                'name'            => $name,
                            ];
                            $email_id = Email::insertGetId($params);

                            if ($type['type'] == 'incoming') {
                                $message = trim($textContent);

                                $reply = (new EmailParser())->parse($message);

                                $fragment = current($reply->getFragments());

                                $pattern = '(On[^abc,]*, (Jan(uary)?|Feb(ruary)?|Mar(ch)?|Apr(il)?|May|Jun(e)?|Jul(y)?|Aug(ust)?|Sep(tember)?|Oct(ober)?|Nov(ember)?|Dec(ember)?)\s+\d{1,2},\s+\d{4}, (1[0-2]|0?[1-9]):([0-5][0-9]) ([AaPp][Mm]))';

                                $reply = strip_tags($fragment);

                                $reply = preg_replace($pattern, ' ', $reply);

                                $mailFound = false;
                                if ($reply) {
                                    $customer = Customer::where('email', $from)->first();
                                    if (! empty($customer)) {
                                        // store the main message
                                        $params = [
                                            'number'      => $customer->phone,
                                            'message'     => $reply,
                                            'media_url'   => null,
                                            'approved'    => 0,
                                            'status'      => 0,
                                            'contact_id'  => null,
                                            'erp_user'    => null,
                                            'supplier_id' => null,
                                            'task_id'     => null,
                                            'dubizzle_id' => null,
                                            'vendor_id'   => null,
                                            'customer_id' => $customer->id,
                                            'is_email'    => 1,
                                            'from_email'  => $from,
                                            'to_email'    => $to,
                                            'email_id'    => $email_id,
                                        ];
                                        $messageModel = ChatMessage::create($params);
                                        MessageHelper::whatsAppSend($customer, $reply, null, null, $isEmail = true);
                                        MessageHelper::sendwatson($customer, $reply, null, $messageModel, $params, $isEmail = true);
                                        $mailFound           = true;
                                        $is_module_available = 1;
                                    }

                                    if (! $mailFound) {
                                        $vandor = Vendor::where('email', $from)->first();
                                        if ($vandor) {
                                            $params = [
                                                'number'      => $vandor->phone,
                                                'message'     => $reply,
                                                'media_url'   => null,
                                                'approved'    => 0,
                                                'status'      => 0,
                                                'contact_id'  => null,
                                                'erp_user'    => null,
                                                'supplier_id' => null,
                                                'task_id'     => null,
                                                'dubizzle_id' => null,
                                                'vendor_id'   => $vandor->id,
                                                'is_email'    => 1,
                                                'from_email'  => $from,
                                                'to_email'    => $to,
                                                'email_id'    => $email_id,
                                            ];
                                            $messageModel        = ChatMessage::create($params);
                                            $mailFound           = true;
                                            $is_module_available = 1;
                                        }
                                    }

                                    if (! $mailFound) {
                                        $supplier = Supplier::where('email', $from)->first();
                                        if ($supplier) {
                                            $params = [
                                                'number'      => $supplier->phone,
                                                'message'     => $reply,
                                                'media_url'   => null,
                                                'approved'    => 0,
                                                'status'      => 0,
                                                'contact_id'  => null,
                                                'erp_user'    => null,
                                                'supplier_id' => $supplier->id,
                                                'task_id'     => null,
                                                'dubizzle_id' => null,
                                                'is_email'    => 1,
                                                'from_email'  => $from,
                                                'to_email'    => $to,
                                                'email_id'    => $email_id,
                                            ];
                                            $messageModel        = ChatMessage::create($params);
                                            $mailFound           = true;
                                            $is_module_available = 1;
                                        }
                                    }

                                    // add entry in chat message even if email is from any other modules
                                    if (! $mailFound) {
                                        $params = [
                                            'number'       => null,
                                            'message'      => $reply,
                                            'media_url'    => null,
                                            'approved'     => 0,
                                            'status'       => 0,
                                            'contact_id'   => null,
                                            'erp_user'     => null,
                                            'supplier_id'  => null,
                                            'task_id'      => null,
                                            'dubizzle_id'  => null,
                                            'is_email'     => 1,
                                            'from_email'   => $from,
                                            'to_email'     => $to,
                                            'email_id'     => $email_id,
                                            'message_type' => 'email',
                                        ];
                                        $messageModel = ChatMessage::create($params);
                                        $mailFound    = true;

                                        Log::info('Incoming Email is not in our sysetm : ' . $from);
                                    }

                                    if ($is_module_available == 0) {
                                        $email                   = Email::where('id', $email_id)->first();
                                        $email->is_unknow_module = 1;
                                        $email->save();
                                    }
                                }
                            }
                        } catch (Exception $e) {
                            Log::error('error while fetching some emails for ' . $emailAddress->username . ' Error Message: ' . $e->getMessage());
                            $historyParam = [
                                'email_address_id' => $emailAddress->id,
                                'is_success'       => 0,
                                'message'          => 'error while fetching some emails for ' . $emailAddress->username . ' Error Message: ' . $e->getMessage(),
                            ];
                            EmailRunHistories::create($historyParam);
                        }
                    }
                }
            }

            $historyParam = [
                'email_address_id' => $emailAddress->id,
                'is_success'       => 1,
            ];

            EmailRunHistories::create($historyParam);

            dump('__________');
        } catch (Exception $e) {
            $exceptionMessage = $e->getMessage();

            if ($e->getPrevious() !== null) {
                $previousMessage  = $e->getPrevious()->getMessage();
                $exceptionMessage = $previousMessage . ' | ' . $exceptionMessage;
            }

            Log::channel('customer')->info($exceptionMessage);
            $historyParam = [
                'email_address_id' => $emailAddress->id,
                'is_success'       => 0,
                'message'          => $exceptionMessage,
            ];
            EmailRunHistories::create($historyParam);
            CronJob::insertLastError('fetch:all_emails', $exceptionMessage);
        }
    }

    public function tags()
    {
        return ['FetchEmail', $this->emailAddress->id];
    }

    private function getModel($email, $email_list)
    {
        $model_id   = null;
        $model_type = null;

        // Traverse all models
        foreach ($email_list as $key => $value) {
            // If email exists in the DB
            if (isset($value[$email])) {
                $model_id   = $value[$email];
                $model_type = $key;
                break;
            }
        }

        return compact('model_id', 'model_type');
    }

    public function getEmailAttachedFileData($fileName = '')
    {
        $file = fopen(storage_path('app/files/email-attachments/' . $fileName), 'r');

        $skiprowupto           = 1; //skip first line
        $rowincrement          = 1;
        $attachedFileDataArray = [];
        while (($data = fgetcsv($file, 4000, ',')) !== false) {
            if ($rowincrement > $skiprowupto) {
                if (isset($data[0]) && ! empty($data[0])) {
                    try {
                        $due_date              = date('Y-m-d', strtotime($data[9]));
                        $attachedFileDataArray = [
                            'line_type'                       => $data[0],
                            'billing_source'                  => $data[1],
                            'original_invoice_number'         => $data[2],
                            'invoice_number'                  => $data[3],
                            'invoice_identifier'              => $data[5],
                            'invoice_type'                    => $data[6],
                            'invoice_currency'                => $data[69],
                            'invoice_amount'                  => $data[70],
                            'invoice_date'                    => $data[7],
                            'payment_terms'                   => $data[8],
                            'due_date'                        => $due_date,
                            'billing_account'                 => $data[11],
                            'billing_account_name'            => $data[12],
                            'billing_account_name_additional' => $data[13],
                            'billing_address_1'               => $data[14],
                            'billing_postcode'                => $data[17],
                            'billing_city'                    => $data[18],
                            'billing_state_province'          => $data[19],
                            'billing_country_code'            => $data[20],
                            'billing_contact'                 => $data[21],
                            'shipment_number'                 => $data[23],
                            'shipment_date'                   => $data[24],
                            'product'                         => $data[30],
                            'product_name'                    => $data[31],
                            'pieces'                          => $data[32],
                            'origin'                          => $data[33],
                            'orig_name'                       => $data[34],
                            'orig_country_code'               => $data[35],
                            'orig_country_name'               => $data[36],
                            'senders_name'                    => $data[37],
                            'senders_city'                    => $data[42],
                            'created_at'                      => Carbon::now(),
                            'updated_at'                      => Carbon::now(),
                        ];
                        if (! empty($attachedFileDataArray)) {
                            $attachresponse = Waybillinvoice::create($attachedFileDataArray);

                            // check that way bill exist not then create
                            $wayBill = Waybill::where('awb', $attachresponse->shipment_number)->first();
                            if (! $wayBill) {
                                $wayBill      = new Waybill;
                                $wayBill->awb = $attachresponse->shipment_number;

                                $wayBill->from_customer_name      = $data[45];
                                $wayBill->from_city               = $data[42];
                                $wayBill->from_country_code       = $data[44];
                                $wayBill->from_customer_address_1 = $data[38];
                                $wayBill->from_customer_address_2 = $data[39];
                                $wayBill->from_customer_pincode   = $data[41];
                                $wayBill->from_company_name       = $data[39];

                                $wayBill->to_customer_name      = $data[50];
                                $wayBill->to_city               = $data[55];
                                $wayBill->to_country_code       = $data[57];
                                $wayBill->to_customer_phone     = '';
                                $wayBill->to_customer_address_1 = $data[51];
                                $wayBill->to_customer_address_2 = $data[52];
                                $wayBill->to_customer_pincode   = $data[54];
                                $wayBill->to_company_name       = '';

                                $wayBill->actual_weight = $data[68];
                                $wayBill->volume_weight = $data[66];

                                $wayBill->cost_of_shipment = $data[70];
                                $wayBill->package_slip     = $attachresponse->shipment_number;
                                $wayBill->pickup_date      = date('Y-m-d', strtotime($data[24]));
                                $wayBill->save();
                            }

                            $cash_flow = new CashFlow();
                            $cash_flow->fill([
                                'date'                => $attachresponse->due_date ? $attachresponse->due_date : null,
                                'type'                => 'pending',
                                'description'         => 'Waybill invoice details',
                                'cash_flow_able_id'   => $attachresponse->id,
                                'cash_flow_able_type' => Waybillinvoice::class,
                            ])->save();
                        }
                    } catch (Exception $e) {
                        Log::error('Error from the dhl invoice : ' . $e->getMessage());
                    }
                }
            }
            $rowincrement++;
        }
        fclose($file);
    }
}
