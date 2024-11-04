<?php

namespace App\Console\Commands;

use App\CronJob;
use App\CronJobReport;
use App\Email;
use App\Helpers\LogHelper;
use App\Purchase;
use App\Supplier;
use Carbon\Carbon;
use Exception;
use Illuminate\Console\Command;
use seo2websites\ErpExcelImporter\ErpExcelImporter;
use Webklex\PHPIMAP\ClientManager;

class FetchEmails extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fetch:emails';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    const DATE_FORMATE = 'Y-m-d H:i:s';

    const PREG_REPLACE = '/[^a-z0-9\_\-\.]/i';

    const FILE_EMAIL_ATTACHMENTS = 'app/files/email-attachments/';

    const EMAIL_ATTACHMENTS = 'email-attachments/';

    const ERPEXCELLMPORTER = '\\seo2websites\\ErpExcelImporter\\ErpExcelImporter';

    const EMAIL_ADD = 'Email added.';

    /**
     * Create a new command instance.
     */

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        LogHelper::createCustomLogForCron($this->signature, ['message' => 'cron was started.']);
        try {
            $report = CronJobReport::create([
                'signature' => $this->signature,
                'start_time' => Carbon::now(),
            ]);
            LogHelper::createCustomLogForCron($this->signature, ['message' => 'report added.']);
            $cm = new ClientManager;
            $imap = $cm->make([
                'host' => config('settings.imap_host_purchase'),
                'port' => config('settings.imap_port_purchase'),
                'encryption' => config('settings.imap_encryption_purchase'),
                'validate_cert' => config('settings.imap_validate_cert_purchase'),
                'username' => config('settings.imap_username_purchase'),
                'password' => config('settings.imap_password_purchase'),
                'protocol' => config('settings.imap_protocol_purchase'),
            ]);

            $imap->connect();
            LogHelper::createCustomLogForCron($this->signature, ['message' => 'Client manager connected.']);

            $suppliers = Supplier::whereHas('Agents')->orWhereNotNull('email')->get();
            LogHelper::createCustomLogForCron($this->signature, ['message' => 'Supplier query finished.']);

            dump(count($suppliers));

            $types = [
                'inbox' => [
                    'inbox_name' => 'INBOX',
                    'direction' => 'from',
                    'type' => 'incoming',
                ],
                'sent' => [
                    'inbox_name' => 'INBOX.Sent',
                    'direction' => 'to',
                    'type' => 'outgoing',
                ],
            ];

            foreach ($suppliers as $supplier) {
                foreach ($types as $type) {
                    dump($type['type']);
                    $inbox = $imap->getFolder($type['inbox_name']);
                    $latest_email = Email::where('type', $type['type'])->where('model_id', $supplier->id)->where(function ($query) {
                        $query->where('model_type', Supplier::class)->orWhere('model_type', Purchase::class);
                    })->latest()->first();

                    if ($latest_email) {
                        $latest_email_date = Carbon::parse($latest_email->created_at);
                    } else {
                        $latest_email_date = Carbon::parse('1990-01-01');
                    }

                    dump($latest_email_date);

                    if ($supplier->agents()->count() > 0) {
                        if ($supplier->agents()->count() > 1) {
                            dump('Multiple Agents');

                            foreach ($supplier->agents as $key => $agent) {
                                if ($key == 0) {
                                    $emails = $inbox->messages()->where($type['direction'], $agent->email)->where([
                                        ['SINCE', $latest_email_date->format('d M y H:i')],
                                    ]);

                                    $emails = $emails->leaveUnread()->get();

                                    foreach ($emails as $email) {
                                        if ($email->hasHTMLBody()) {
                                            $content = $email->getHTMLBody();
                                        } else {
                                            $content = $email->getTextBody();
                                        }

                                        if ($email->getDate()->format(self::DATE_FORMATE) > $latest_email_date->format(self::DATE_FORMATE)) {
                                            dump('NEW EMAIL First');
                                            $attachments_array = [];
                                            $attachments = $email->getAttachments();

                                            $attachments->each(function ($attachment) use (&$attachments_array, $supplier) {
                                                $attachment->name = preg_replace(self::PREG_REPLACE, '', $attachment->name);
                                                file_put_contents(storage_path(self::FILE_EMAIL_ATTACHMENTS.$attachment->name), $attachment->content);
                                                $path = self::EMAIL_ATTACHMENTS.$attachment->name;

                                                if ($attachment->getExtension() == 'xlsx' || $attachment->getExtension() == 'xls') {
                                                    if (class_exists(self::ERPEXCELLMPORTER)) {
                                                        $excel = $supplier->getSupplierExcelFromSupplierEmail();
                                                        ErpExcelImporter::excelFileProcess($attachment->name, $excel, $supplier->email);
                                                    }
                                                } elseif ($attachment->getExtension() == 'zip') {
                                                    if (class_exists(self::ERPEXCELLMPORTER)) {
                                                        $excel = $supplier->getSupplierExcelFromSupplierEmail();
                                                        $attachments = ErpExcelImporter::excelZipProcess($attachment, $attachment->name, $excel, $supplier->email, $attachments_array);
                                                        $attachments_array = $attachments;
                                                    }
                                                }

                                                $attachments_array[] = $path;
                                            });

                                            $emailData = explode('@', $email->getFrom()[0]->mail);
                                            $name = $emailData[0];

                                            $params = [
                                                'model_id' => $supplier->id,
                                                'model_type' => Supplier::class,
                                                'type' => $type['type'],
                                                'seen' => $email->getFlags()['seen'],
                                                'from' => $email->getFrom()[0]->mail,
                                                'to' => array_key_exists(0, $email->getTo()) ? $email->getTo()[0]->mail : $email->getReplyTo()[0]->mail,
                                                'subject' => $email->getSubject(),
                                                'message' => $content,
                                                'template' => 'customer-simple',
                                                'additional_data' => json_encode(['attachment' => $attachments_array]),
                                                'created_at' => $email->getDate(),
                                                'name' => $name,
                                            ];

                                            Email::create($params);
                                            LogHelper::createCustomLogForCron($this->signature, ['message' => self::EMAIL_ADD]);
                                        }
                                    }
                                } else {
                                    $additional = $inbox->messages()->where($type['direction'], $agent->email)->since(Carbon::parse($latest_email_date)->format(self::DATE_FORMATE));

                                    $additional = $additional->leaveUnread()->get();

                                    foreach ($additional as $email) {
                                        if ($email->hasHTMLBody()) {
                                            $content = $email->getHTMLBody();
                                        } else {
                                            $content = $email->getTextBody();
                                        }

                                        if ($email->getDate()->format(self::DATE_FORMATE) > $latest_email_date->format(self::DATE_FORMATE)) {
                                            dump('NEW EMAIL Second');

                                            $attachments_array = [];
                                            $attachments = $email->getAttachments();

                                            $attachments->each(function ($attachment) use (&$attachments_array, $supplier) {
                                                $attachment->name = preg_replace(self::PREG_REPLACE, '', $attachment->name);
                                                file_put_contents(storage_path(self::FILE_EMAIL_ATTACHMENTS.$attachment->name), $attachment->content);
                                                $path = self::EMAIL_ATTACHMENTS.$attachment->name;

                                                if ($attachment->getExtension() == 'xlsx' || $attachment->getExtension() == 'xls') {
                                                    if (class_exists(self::ERPEXCELLMPORTER)) {
                                                        $excel = $supplier->getSupplierExcelFromSupplierEmail();
                                                        ErpExcelImporter::excelFileProcess($attachment->name, $excel, $supplier->email);
                                                    }
                                                } elseif ($attachment->getExtension() == 'zip') {
                                                    if (class_exists(self::ERPEXCELLMPORTER)) {
                                                        $excel = $supplier->getSupplierExcelFromSupplierEmail();
                                                        $attachments = ErpExcelImporter::excelZipProcess($attachment, $attachment->name, $excel, $supplier->email, $attachments_array);
                                                        $attachments_array = $attachments;
                                                    }
                                                }

                                                $attachments_array[] = $path;
                                            });

                                            $emailData = explode('@', $email->getFrom()[0]->mail);
                                            $name = $emailData[0];

                                            $params = [
                                                'model_id' => $supplier->id,
                                                'model_type' => Supplier::class,
                                                'type' => $type['type'],
                                                'seen' => $email->getFlags()['seen'],
                                                'from' => $email->getFrom()[0]->mail,
                                                'to' => array_key_exists(0, $email->getTo()) ? $email->getTo()[0]->mail : $email->getReplyTo()[0]->mail,
                                                'subject' => $email->getSubject(),
                                                'message' => $content,
                                                'template' => 'customer-simple',
                                                'additional_data' => json_encode(['attachment' => $attachments_array]),
                                                'created_at' => $email->getDate(),
                                                'name' => $name,
                                            ];

                                            Email::create($params);
                                            LogHelper::createCustomLogForCron($this->signature, ['message' => self::EMAIL_ADD]);
                                        }
                                    }

                                    $emails = $emails->merge($additional);
                                }
                            }
                        } elseif ($supplier->agents()->count() == 1) {
                            dump('1 Agent');

                            $emails = $inbox->messages()->where($type['direction'], $supplier->agents[0]->email)->since(Carbon::parse($latest_email_date)->format(self::DATE_FORMATE));

                            $emails = $emails->leaveUnread()->get();

                            foreach ($emails as $email) {
                                if ($email->hasHTMLBody()) {
                                    $content = $email->getHTMLBody();
                                } else {
                                    $content = $email->getTextBody();
                                }

                                if ($email->getDate()->format(self::DATE_FORMATE) > $latest_email_date->format(self::DATE_FORMATE)) {
                                    dump('NEW EMAIL third');

                                    $attachments_array = [];
                                    $attachments = $email->getAttachments();

                                    $attachments->each(function ($attachment) use (&$attachments_array, $supplier) {
                                        $attachment->name = preg_replace(self::PREG_REPLACE, '', $attachment->name);

                                        file_put_contents(storage_path(self::FILE_EMAIL_ATTACHMENTS.$attachment->name), $attachment->content);
                                        $path = self::EMAIL_ATTACHMENTS.$attachment->name;

                                        if ($attachment->getExtension() == 'xlsx' || $attachment->getExtension() == 'xls') {
                                            if (class_exists(self::ERPEXCELLMPORTER)) {
                                                $excel = $supplier->getSupplierExcelFromSupplierEmail();
                                                ErpExcelImporter::excelFileProcess($attachment->name, $excel, $supplier->email);
                                            }
                                        } elseif ($attachment->getExtension() == 'zip') {
                                            if (class_exists(self::ERPEXCELLMPORTER)) {
                                                $excel = $supplier->getSupplierExcelFromSupplierEmail();
                                                $attachments = ErpExcelImporter::excelZipProcess($attachment, $attachment->name, $excel, $supplier->email, $attachments_array);
                                                $attachments_array = $attachments;
                                            }
                                        }
                                        $attachments_array[] = $path;
                                    });

                                    $emailData = explode('@', $email->getFrom()[0]->mail);
                                    $name = $emailData[0];

                                    $params = [
                                        'model_id' => $supplier->id,
                                        'model_type' => Supplier::class,
                                        'type' => $type['type'],
                                        'seen' => $email->getFlags()['seen'],
                                        'from' => $email->getFrom()[0]->mail,
                                        'to' => array_key_exists(0, $email->getTo()) ? $email->getTo()[0]->mail : $email->getReplyTo()[0]->mail,
                                        'subject' => $email->getSubject(),
                                        'message' => $content,
                                        'template' => 'customer-simple',
                                        'additional_data' => json_encode(['attachment' => $attachments_array]),
                                        'created_at' => $email->getDate(),
                                        'name' => $name,
                                    ];

                                    Email::create($params);
                                    LogHelper::createCustomLogForCron($this->signature, ['message' => 'Email added']);
                                }
                            }
                        } else {
                            dump('No Agents Emails');

                            $emails = $inbox->messages()->where($type['direction'], 'nonexisting@email.com');
                            $emails = $emails->setFetchFlags(false)
                                ->setFetchBody(false)
                                ->setFetchAttachment(false)->leaveUnread()->get();
                        }
                    } else {
                        dump('No Agent just Supplier emails');
                        if ($inbox) {
                            $emails = $inbox->messages()->where($type['direction'], $supplier->email)->since(Carbon::parse($latest_email_date)->format(self::DATE_FORMATE));

                            $emails = $emails->leaveUnread()->get();

                            foreach ($emails as $email) {
                                if ($email->hasHTMLBody()) {
                                    $content = $email->getHTMLBody();
                                } else {
                                    $content = $email->getTextBody();
                                }

                                if ($email->getDate()->format(self::DATE_FORMATE) > $latest_email_date->format(self::DATE_FORMATE)) {
                                    $attachments_array = [];
                                    $attachments = $email->getAttachments();

                                    $attachments->each(function ($attachment) use (&$attachments_array, $supplier) {
                                        $attachment->name = preg_replace(self::PREG_REPLACE, '', $attachment->name);
                                        file_put_contents(storage_path(self::FILE_EMAIL_ATTACHMENTS.$attachment->name), $attachment->content);
                                        $path = self::EMAIL_ATTACHMENTS.$attachment->name;

                                        if ($attachment->getExtension() == 'xlsx' || $attachment->getExtension() == 'xls') {
                                            if (class_exists(self::ERPEXCELLMPORTER)) {
                                                $excel = $supplier->getSupplierExcelFromSupplierEmail();
                                                ErpExcelImporter::excelFileProcess($attachment->name, $excel, $supplier->email);
                                            }
                                        } elseif ($attachment->getExtension() == 'zip') {
                                            if (class_exists(self::ERPEXCELLMPORTER)) {
                                                $excel = $supplier->getSupplierExcelFromSupplierEmail();
                                                $attachments = ErpExcelImporter::excelZipProcess($attachment, $attachment->name, $excel, $supplier->email, $attachments_array);
                                                $attachments_array = $attachments;
                                            }
                                        }

                                        $attachments_array[] = $path;
                                    });

                                    $emailData = explode('@', $email->getFrom()[0]->mail);
                                    $name = $emailData[0];

                                    $params = [
                                        'model_id' => $supplier->id,
                                        'model_type' => Supplier::class,
                                        'type' => $type['type'],
                                        'seen' => $email->getFlags()['seen'],
                                        'from' => $email->getFrom()[0]->mail,
                                        'to' => array_key_exists(0, $email->getTo()) ? $email->getTo()[0]->mail : $email->getReplyTo()[0]->mail,
                                        'subject' => $email->getSubject(),
                                        'message' => $content,
                                        'template' => 'customer-simple',
                                        'additional_data' => json_encode(['attachment' => $attachments_array]),
                                        'created_at' => $email->getDate(),
                                        'name' => $name,
                                    ];

                                    Email::create($params);
                                    LogHelper::createCustomLogForCron($this->signature, ['message' => self::EMAIL_ADD]);
                                }
                            }
                        } else {
                            dump('empty inbox');
                        }
                    }
                }

                dump('__________');
            }

            $report->update(['end_time' => Carbon::now()]);
            LogHelper::createCustomLogForCron($this->signature, ['message' => 'Report endtime was updated.']);
            LogHelper::createCustomLogForCron($this->signature, ['message' => 'cron was ended.']);
        } catch (Exception $e) {
            LogHelper::createCustomLogForCron($this->signature, ['Exception' => $e->getTraceAsString(), 'message' => $e->getMessage()]);

            CronJob::insertLastError($this->signature, $e->getMessage());
        }
    }
}
