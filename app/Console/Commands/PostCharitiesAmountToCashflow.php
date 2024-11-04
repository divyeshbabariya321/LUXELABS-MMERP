<?php

namespace App\Console\Commands;

use App\CashFlow;
use App\Customer;
use App\CustomerCharity;
use App\Email;
use App\Jobs\SendEmail;
use App\MailinglistTemplate;
use App\Order;
use App\OrderProduct;
use Illuminate\Console\Command;

class PostCharitiesAmountToCashflow extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'Post Charities Order Amount To Cashflow';

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
        $date2 = date('Y-m-d');
        $date1 = date('Y-m-d', mktime(0, 0, 0, date('m'), date('d') - 7, date('Y')));
        $co = OrderProduct::select('order_products.*', 'customer_charities.id as charity_id ')
            ->join('customer_charities', 'customer_charities.product_id', 'order_products.product_id')
            ->join('orders', 'orders.id', 'order_products.order_id')
            ->whereRaw("date(orders.order_date) between date('$date1') and date('$date2')")
            ->get();
        foreach ($co as $o) {
            $total = $o->order_price;
            if ($total > 0) {
                $total = $total * 2;
                $date = date('Y-m-d');
                $total = number_format($total, 2);
                $user_id = ! empty(auth()->id()) ? auth()->id() : 6;
                $cf = CashFlow::where('cash_flow_able_id', $o->charity_id)->where('cash_flow_able_type', 'CustomerCharity::class')->first();
                if (! $cf) {
                    CashFlow::create([
                        'date' => $date,
                        'amount' => $total,
                        'type' => 'pending',
                        'currency' => $o->currency,
                        'status' => 1,
                        'order_status' => 'pending',
                        'user_id' => $o->charity_id,
                        'updated_by' => $user_id,
                        'cash_flow_able_id' => $o->charity_id,
                        'cash_flow_able_type' => CustomerCharity::class,
                        'description' => 'charities payment',
                    ]);

                    $template = MailinglistTemplate::getMailTemplate('Charity Confirmation');
                    $order = Order::where('id', $o->order_id)->first();
                    $emailClass = (new CharityConfirmation($order))->build();

                    if ($template) {
                        $email = Email::create([
                            'model_id' => $order->customer_id,
                            'model_type' => Customer::class,
                            'from' => $emailClass->fromMailer,
                            'to' => $email,
                            'subject' => $emailClass->subject,
                            'message' => $emailClass->render(),
                            'template' => 'Charity Confirmation',

                            'additional_data' => '',
                            'status' => 'pre-send',
                            'is_draft' => 1,
                        ]);
                        SendEmail::dispatch($email)->onQueue('send_email');
                    }
                }
            }
        }
    }
}
