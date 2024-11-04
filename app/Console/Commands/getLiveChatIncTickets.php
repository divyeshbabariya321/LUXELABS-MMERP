<?php

namespace App\Console\Commands;

use App\CronJob;
use App\CronJobReport;
use App\Customer;
use App\Email;
use App\Helpers\LogHelper;
use App\Jobs\SendEmail;
use App\LogRequest;
use App\Mails\Manual\TicketAck;
use App\Tickets;
use App\TicketStatuses;
use Carbon\Carbon;
use Exception;
use Illuminate\Console\Command;

class getLiveChatIncTickets extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'livechat:tickets';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Get tickets from livechat inc and put them as unread messages';

    /**Created By : Maulik jadvani
    tickets store in tickets table
     *Get tickets from livechat inc and put them as unread messages
     */

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $startTime = date('Y-m-d H:i:s', LARAVEL_START);
        LogHelper::createCustomLogForCron($this->signature, ['message' => 'cron was started.']);
        try {
            $report = CronJobReport::create([
                'signature' => $this->signature,
                'start_time' => Carbon::now(),
            ]);
            LogHelper::createCustomLogForCron($this->signature, ['message' => 'report was added.']);
            $curl = curl_init();
            $url = 'https://api.livechatinc.com/v2/tickets';

            curl_setopt_array($curl, [
                CURLOPT_URL => $url,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'GET',
                CURLOPT_HTTPHEADER => [
                    'Authorization: Basic NmY0M2ZkZDUtOTkwMC00OWY4LWI4M2ItZThkYzg2ZmU3ODcyOmRhbDp0UkFQdWZUclFlLVRkQUI4Y2pFajNn',
                ],
            ]);

            $response = curl_exec($curl);
            $httpcode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
            LogRequest::log($startTime, $url, 'GET', json_encode([]), json_decode($response), $httpcode, getLiveChatIncTickets::class, 'handle');
            $result = json_decode($response, true);
            LogHelper::createCustomLogForCron($this->signature, ['message' => 'CURL api call finished. => https://api.livechatinc.com/v2/tickets']);

            if (! empty($result['tickets'])) {
                $result = $result['tickets'];
            }

            if (isset($result) && count($result) > 0) {
                foreach ($result as $row) {
                    $event = (isset($row['events'][0])) ? $row['events'][0] : [];
                    $author = (isset($event['author'])) ? $event['author'] : [];

                    $email = (isset($author['id'])) ? $author['id'] : '';
                    $name = (isset($author['name'])) ? $author['name'] : '';

                    $customer = Customer::where('email', $email)->first();
                    LogHelper::createCustomLogForCron($this->signature, ['message' => 'Customer query finished.']);
                    if (isset($customer->id) && ($customer->id) > 0) {
                        $customer_id = $customer->id;
                    } else {
                        $customer = new Customer;
                        $customer->name = $name;
                        $customer->email = $email;
                        $customer->save();
                        LogHelper::createCustomLogForCron($this->signature, ['message' => 'Customer saved.']);
                        $customer_id = $customer->id;
                    }

                    $ticket_id = (isset($row['id'])) ? $row['id'] : '';
                    $subject = (isset($row['subject'])) ? $row['subject'] : '';
                    $message = (isset($event['message'])) ? $event['message'] : '';
                    $date = (isset($event['date'])) ? $event['date'] : date();

                    $status = TicketStatuses::where('name', $row['status'])->first();
                    LogHelper::createCustomLogForCron($this->signature, ['message' => 'Ticket status query finished.']);
                    if (! $status) {
                        $status = new TicketStatuses;
                        $status->name = $row['status'];
                        $status->save();
                        LogHelper::createCustomLogForCron($this->signature, ['message' => 'Ticket status was added.']);
                    }

                    $Tickets_data = [
                        'ticket_id' => $ticket_id,
                        'subject' => $subject,
                        'message' => $message,
                        'date' => $date,
                        'customer_id' => $customer_id,
                        'name' => $name,
                        'email' => $email,
                        'status_id' => $status->id,
                    ];

                    $ticketObj = Tickets::where('ticket_id', $ticket_id)->first();
                    LogHelper::createCustomLogForCron($this->signature, ['message' => 'Ticket query finished.']);
                    if (! $ticketObj) {
                        $ticketObj = Tickets::create($Tickets_data);
                        LogHelper::createCustomLogForCron($this->signature, ['message' => 'Ticket was added.']);
                        $emailClass = (new TicketAck($ticketObj))->build();

                        if ($ticketObj) {
                            $email = Email::create([
                                'model_id' => $customer_id,
                                'model_type' => Customer::class,
                                'from' => $emailClass->fromMailer,
                                'to' => $email,
                                'subject' => $emailClass->subject,
                                'message' => $emailClass->render(),
                                'template' => 'Ticket ACK',
                                'additional_data' => '',
                                'status' => 'pre-send',
                                'is_draft' => 1,
                            ]);
                            LogHelper::createCustomLogForCron($this->signature, ['message' => 'Email was created.']);
                            SendEmail::dispatch($email)->onQueue('send_email');
                            LogHelper::createCustomLogForCron($this->signature, ['message' => 'Email sent.']);
                        }
                    }
                }
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
