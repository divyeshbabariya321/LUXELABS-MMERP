<?php

namespace App\Console\Commands;

use App\CronJob;
use App\CronJobReport;
use App\Document;
use Carbon\Carbon;
use Exception;
use Illuminate\Console\Command;
use Webklex\PHPIMAP\ClientManager;

class DocumentReciever extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'document:email';

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
        try {
            $report = CronJobReport::create([
                'signature' => $this->signature,
                'start_time' => Carbon::now(),
            ]);
            $cm = new ClientManager;
            $oClient = $cm->make([
                'host' => config('settings.imap_host_document'),
                'port' => config('settings.imap_port_document'),
                'encryption' => config('settings.imap_encryption_document'),
                'validate_cert' => config('settings.imap_validate_cert_document'),
                'username' => config('settings.imap_username_document'),
                'password' => config('settings.imap_password_document'),
                'protocol' => config('settings.imap_protocol_document'),
            ]);

            $oClient->connect();

            $folder = $oClient->getFolder('INBOX');

            $message = $folder->query()->unseen()->setFetchBody(true)->get()->all();
            if (count($message) == 0) {
                echo 'No New Mail Found';
                echo '<br>';
                exit();
            }

            foreach ($message as $messages) {
                $subject = $messages->getSubject();
                $subject = strtolower($subject);
                if (session()->has('email.subject')) {
                    session()->forget('email.subject');
                    session()->push('email.subject', $subject);
                } else {
                    session()->push('email.subject', $subject);
                }

                if ($messages->hasAttachments()) {
                    $aAttachment = $messages->getAttachments();
                    $aAttachment->each(function ($oAttachment) {
                        $name = $oAttachment->getName();
                        $oAttachment->save(storage_path('app/files/documents/'), $name);
                        $document = new Document;
                        $subject = session()->get('email.subject');
                        $document->name = $subject[0];
                        $document->filename = $name;
                        $document->version = 1;
                        $document->from_email = 1;
                        $document->save();
                        echo 'Document Saved in Pending';
                    });
                }
            }

            $report->update(['end_time' => Carbon::now()]);
        } catch (Exception $e) {
            CronJob::insertLastError($this->signature, $e->getMessage());
        }
    }
}
