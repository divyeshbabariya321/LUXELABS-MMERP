<?php

namespace App\Console\Commands;

use App\ChatMessage;
use App\CronJob;
use App\CustomerBasketProduct;
use App\DeveloperTask;
use App\Email;
use App\ErpLeads;
use App\Flow;
use App\FlowAction;
use App\FlowCondition;
use App\FlowMessage;
use App\Helpers\LogHelper;
use App\Loggers\FlowLog;
use App\Loggers\FlowLogMessages;
use App\Mail\ScheduledEmail;
use App\Mailinglist;
use App\Order;
use App\ScrapLog;
use App\Task;
use App\TaskCategory;
use Carbon\Carbon;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ScheduleEmails extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:schedule_emails';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Schedule Emails';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public $log;

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        try {
            LogHelper::createCustomLogForCron($this->signature, ['message' => 'Cron was started to run']);

            $created_date = Carbon::now();
            $modalType = '';
            $leads = [];

            $flows = Flow::select('id', 'flow_name as name')->get();
            FlowLog::log(['flow_id' => 0, 'messages' => 'Flow action started to check and found total flows : '.$flows->count()]);

            LogHelper::createCustomLogForCron($this->signature, ['message' => 'Flow model query was finished']);

            foreach ($flows as $flow) {
                $flowconditions = FlowCondition::where('flow_name', $flow['name'])->where('status', 1)->get();
                $allflowconditions = [];
                if (! empty($flowconditions)) {
                    foreach ($flowconditions as $key => $flowcondition) {
                        $allflowconditions[$key] = $flowcondition['condition_name'];
                    }
                }

                $flowActions = FlowAction::join('flow_paths', 'flow_actions.path_id', '=', 'flow_paths.id')
                    ->join('flows', 'flow_paths.flow_id', '=', 'flows.id')
                    ->join('flow_types', 'flow_types.id', '=', 'flow_actions.type_id')
                    ->join('store_websites', 'store_websites.id', '=', 'flows.store_website_id')
                    ->select('flows.store_website_id', 'flows.id', 'store_websites.website', 'flows.flow_description', 'flow_actions.id as action_id', 'flow_actions.time_delay', 'flow_actions.message_title', 'flow_actions.condition', 'flow_types.type', 'flow_actions.time_delay_type', 'flows.flow_name')
                    ->where('flows.id', '=', $flow['id'])->whereNull('flow_paths.parent_action_id')->orderBy('flow_actions.rank')
                    ->get();

                LogHelper::createCustomLogForCron($this->signature, ['message' => 'FlowAction model query was finished']);

                $flowlog = FlowLog::log(['flow_id' => $flow['id'], 'messages' => $flow['name'].' has found total Action  : '.$flowActions->count()]);

                if ($flowActions != null) {
                    LogHelper::createCustomLogForCron($this->signature, ['message' => 'Flow action records found']);

                    $created_date = Carbon::now();
                    foreach ($flowActions as $key => $flowAction) {
                        if ($flowAction['type'] == 'Time Delay') {
                            if ($flowAction['time_delay_type'] == 'days') {
                                $created_date = $created_date->addDays($flowAction['time_delay']);
                            } elseif ($flowAction['time_delay_type'] == 'hours') {
                                $created_date = $created_date->addHours($flowAction['time_delay']);
                            } elseif ($flowAction['time_delay_type'] == 'minutes') {
                                $created_date = $created_date->addMinutes($flowAction['time_delay']);
                            }
                        }

                        $leadsFlowArray = ['add_to_cart', 'add to cart', 'attach_images_for_product', 'dispatch_send_price', 'new_erp_lead', 'out_of_stock_subscribe', 'payment_failed'];

                        if ($key == 0 && (in_array($flow['name'], $leadsFlowArray))) {
                            $nameInDB = str_replace('_', '-', $flow['name']);
                            $leads = ErpLeads::select(
                                'erp_leads.id',
                                'erp_leads.customer_id',
                                'erp_leads.created_at as order_date',
                                'customers.name as customer_name',
                                'customers.email as customer_email',
                                'customers.id as customer_id'
                            )
                                ->leftJoin('customers', 'erp_leads.customer_id', '=', 'customers.id')
                                ->where('erp_leads.created_at', 'like', Carbon::now()->format('Y-m-d').'%')
                                ->where('customers.store_website_id', $flow['store_website_id'])
                                ->whereIn('type', [$flow['name'], $nameInDB])
                                ->whereNotNull('customers.email')
                                ->get();
                            $i = 1;

                            $modalType = ErpLeads::class;

                            LogHelper::createCustomLogForCron($this->signature, ['message' => 'ErpLeads model query finished']);
                        } elseif ($key == 0 and $flow['name'] == 'wishlist') {
                            $leads = CustomerBasketProduct::join('customer_baskets as cb', 'cb.id', 'customer_basket_products.customer_basket_id');
                            $leads = $leads->where('cb.store_website_id', $flow['store_website_id']);
                            if (in_array('wishlist_customer_basket_products_created_at', $allflowconditions)) {
                                $leads = $leads->where('customer_basket_products.created_at', 'like', Carbon::now()->format('Y-m-d').'%');
                            }

                            $leads = $leads->select('customer_basket_products.id', 'cb.customer_name', 'cb.customer_email', 'cb.customer_id')
                                ->get();
                            $modalType = CustomerBasketProduct::class;

                            LogHelper::createCustomLogForCron($this->signature, ['message' => 'CustomerBasketProduct model query finished']);
                        } elseif ($key == 0 and $flow['name'] == 'delivered') {
                            $leads = Order::leftJoin('customers', 'orders.customer_id', '=', 'customers.id');
                            $leads = $leads->where('customers.store_website_id', $flow['store_website_id']);
                            if (in_array('delivered_order_orders_order_status', $allflowconditions)) {
                                $leads = $leads->whereIn('orders.order_status', ['delivered', 'Delivered']);
                            }
                            if (in_array('delivered_order_orders_date_of_delivery', $allflowconditions)) {
                                $leads = $leads->where('orders.date_of_delivery', 'like', Carbon::now()->format('Y-m-d').'%');
                            }
                            $leads = $leads->select('orders.id', 'customers.name as customer_name', 'customers.email as customer_email', 'customers.id as customer_id')
                                ->get();
                            $modalType = Order::class;

                            LogHelper::createCustomLogForCron($this->signature, ['message' => 'Order model query finished']);
                        } elseif ($key == 0 and $flow['name'] == 'newsletters') {
                            $leads = Mailinglist::leftJoin('list_contacts', 'list_contacts.list_id', '=', 'mailinglists.id')
                                ->leftJoin('customers', 'customers.id', '=', 'list_contacts.customer_id');
                            $leads = $leads->where('mailinglists.website_id', $flow['store_website_id']);

                            if (in_array('newsletters_list_contacts_created_at', $allflowconditions)) {
                                $leads = $leads->$leads->where('list_contacts.created_at', 'like', Carbon::now()->format('Y-m-d').'%');
                            }
                            if (in_array('newsletters_customers_newsletter', $allflowconditions)) {
                                $leads = $leads->where('customers.newsletter', 1);
                            }

                            $leads = $leads->select('mailinglists.id', 'customers.email as customer_email', 'customers.id as customer_id')->get();
                            $modalType = Mailinglist::class;

                            LogHelper::createCustomLogForCron($this->signature, ['message' => 'Mailinglist model query finished']);
                        } elseif ($key == 0 and $flow['name'] == 'customer_win_back') {
                            $leads = Order::leftJoin('customers', 'orders.customer_id', '=', 'customers.id');
                            $leads = $leads->where('customers.store_website_id', $flow['store_website_id']);
                            if (in_array('customer_win_back_orders_order_status', $allflowconditions)) {
                                $leads = $leads->whereIn(DB::raw('lower(orders.order_status)'), ['Follow up for advance', 'Prepaid']);
                            }
                            if (in_array('customer_win_back_orders_created_at', $allflowconditions)) {
                                $leads = $leads->where('orders.created_at', 'like', Carbon::now()->format('Y-m-d').'%');
                            }

                            $leads = $leads->select('orders.id', 'customers.name as customer_name', 'customers.email as customer_email', 'customers.id as customer_id')
                                ->get();
                            $modalType = Order::class;

                            LogHelper::createCustomLogForCron($this->signature, ['message' => 'Orders model query finished']);
                        } elseif ($key == 0 and $flow['name'] == 'order_reviews') {
                            $leads = Order::leftJoin('customers', 'orders.customer_id', '=', 'customers.id');
                            if (in_array('order_reviews_customers_store_website_id', $allflowconditions)) {
                                $leads = $leads->where('customers.store_website_id', $flow['store_website_id']);
                            }
                            if (in_array('order_reviews_orders_order_status', $allflowconditions)) {
                                $leads = $leads->whereIn('orders.order_status', ['delivered', 'Delivered']);
                            }
                            if (in_array('order_reviews_orders_date_of_delivery', $allflowconditions)) {
                                $leads = $leads->where('orders.date_of_delivery', 'like', Carbon::now()->format('Y-m-d').'%');
                            }

                            $leads = $leads->select('orders.id', 'customers.name as customer_name', 'customers.email as customer_email', 'customers.id as customer_id')->get();
                            $modalType = Order::class;

                            LogHelper::createCustomLogForCron($this->signature, ['message' => 'Orders model query finished']);
                        } elseif ($key == 0 && $flow['name'] == 'customer_post_purchase') { //
                            $leads = [];
                            $modalType = Order::class;
                        } elseif ($key == 0 && $flow['name'] == 'task_pr') { //
                            $leads = [];
                            $modalType = DeveloperTask::class;
                        } elseif ($key == 0 && $flow['name'] == 'site_dev') { //
                            $leads = [];
                            $modalType = Task::class;
                        } elseif ($key == 0 && in_array($flow['name'], ['order received', 'product shipped to client', 'cancel', 'Refund to be processed', 'Refund Dispatched', 'Refund Credited'])) {
                            //To manage For all the orders Status
                            $leads = Order::leftJoin('customers', 'orders.customer_id', '=', 'customers.id')
                                ->where('customers.store_website_id', $flow['store_website_id'])
                                ->where('orders.order_status', $flow['name'])
                                ->select('orders.id', 'orders.order_status', 'customers.name as customer_name', 'customers.email as customer_email', 'customers.id as customer_id')->get();

                            $modalType = Order::class;

                            LogHelper::createCustomLogForCron($this->signature, ['message' => 'Orders model query finished']);
                        }

                        LogHelper::createCustomLogForCron($this->signature, ['message' => 'Starting the process']);
                        $this->doProcess($flowAction, $modalType, $leads, $flow['store_website_id'], $created_date, $flowlog['id'], 'customer', $allflowconditions, $flow);
                    }
                }
            }
        } catch (Exception $e) {
            LogHelper::createCustomLogForCron($this->signature, ['Exception' => $e->getTraceAsString(), 'message' => $e->getMessage()]);

            CronJob::insertLastError($this->signature, $e->getMessage());
        }
    }

    public function doProcess($flowAction, $modalType, $leads, $store_website_id, $created_date, $flow_log_id, $leadType = 'customer', $allflowconditions = null, $flow = null)
    {
        $scraper_id = 0;
        //Remove older emails and messages

        if ($flowAction['type'] == 'Send Message') {
            $message = FlowMessage::where('action_id', $flowAction['action_id'])->first();

            if ($message != null) {
                foreach ($leads as $lead) {
                    if (isset($lead['scraper_id'])) {
                        $scraper_id = $lead['scraper_id'];
                    }
                    if ($message['mail_tpl'] != '') {
                        $bodyText = @(string) view($message['mail_tpl']);
                    } else {
                        $arrToReplace = ['{FIRST_NAME}'];
                        $valToReplace = [$lead->customer_name];
                        $bodyText = str_replace($arrToReplace, $valToReplace, $message['html_content']);
                    }
                    $emailData['subject'] = $message['subject'];
                    $emailData['template'] = $bodyText;
                    $emailData['from'] = $message['sender_email_address'];

                    $emailClass = (new ScheduledEmail($emailData))->build();
                    $flow_id = $flowAction['id'];
                    if (isset($flow) && isset($flow['id'])) {
                        $flow_id = $flow['id'];
                    }
                    $params = [
                        'model_id' => $lead->id ?? $lead['id'],
                        'model_type' => $modalType,
                        'type' => 'outgoing',
                        'seen' => 0,
                        'from' => $emailClass->sendFrom,
                        'to' => $lead['customer_email'],
                        'subject' => $message['subject'],
                        'message' => $emailClass->render(),
                        'template' => 'flow#'.$flow_id,
                        'schedule_at' => $created_date,
                        'is_draft' => 1,
                        'order_id' => $lead['id'] ?? '',
                        'order_status' => $lead['order_status'] ?? '',
                    ];

                    Email::where('order_id', $lead['id'])->delete();

                    Email::create($params);
                    $flowLogMessage = FlowLogMessages::where([
                        'flow_action' => $flowAction['type'],
                        'modalType' => $modalType,
                        'leads' => $lead->customer_id,
                        'store_website_id' => $store_website_id,
                        'messages' => $bodyText.' ('.$created_date.')',
                        'flow_log_id' => $flow_log_id,
                        'scraper_id' => $scraper_id,
                    ])->first();

                    if ($flowLogMessage == null) {
                        FlowLogMessages::log([
                            'flow_action' => $flowAction['type'],
                            'modalType' => $modalType,
                            'leads' => $lead->customer_id,
                            'store_website_id' => $store_website_id,
                            'messages' => $bodyText.' ('.$created_date.')',
                            'flow_log_id' => $flow_log_id,
                            'scraper_id' => $scraper_id,
                        ]);
                    }
                }
            } else {
                FlowLogMessages::log([
                    'flow_action' => $flowAction['type'],
                    'modalType' => $modalType,
                    'leads' => '',
                    'store_website_id' => $store_website_id,
                    'messages' => 'flow Message is not found for email - '.$flowAction['action_id'],
                    'flow_log_id' => $flow_log_id,
                    'scraper_id' => $scraper_id,
                ]);
            }
        } elseif ($flowAction['type'] == 'Whatsapp' || $flowAction['type'] == 'SMS') {
            $messageApplicationId = 4;
            if ($flowAction['type'] == 'SMS') {
                $messageApplicationId = 3;
            }
            foreach ($leads as $lead) {
                if (isset($lead['scraper_id'])) {
                    $scraper_id = $lead['scraper_id'];
                }

                $extraParams = [];
                if ($modalType == ErpLeads::class) {
                    $extraParams = ['lead_id' => $lead->id];
                } elseif ($modalType == CustomerBasketProduct::class) {
                    $extraParams = ['customer_id' => $lead->id];
                } elseif ($modalType == 'App\Orders') {
                    $extraParams = ['order_id' => $lead->id];
                } elseif ($modalType == DeveloperTask::class) {
                    $extraParams = ['developer_task_id' => $lead->id];
                } elseif ($modalType == Task::class) {
                    $extraParams = ['task_id' => $lead->id];
                } elseif ($modalType == Mailinglist::class) {
                    $extraParams = ['email_id' => $lead->id];
                }

                if ($leadType == 'customer') {
                    $insertParams = [
                        'message' => $flowAction['message_title'],
                        'status' => 1,
                        'is_queue' => 1,
                        'approved' => 0,
                        'number' => null,
                        'message_application_id' => $messageApplicationId,
                        'scheduled_at' => $created_date,
                        'flow_exit' => 1,  /* if the message is coming from flow */
                        'order_id' => $lead['id'] ?? '',
                        'order_status' => $lead['order_status'] ?? '',
                    ];
                    $order_id = $lead['id'] ?? '';
                    $order_status = $lead['order_status'] ?? '';
                    $createParams = array_merge($extraParams, $insertParams);
                    ChatMessage::updateOrCreate(['customer_id' => $lead->customer_id,
                        'message' => $flowAction['message_title'], 'status' => 1, 'is_queue' => 1,
                        'approved' => 1, 'message_application_id' => $messageApplicationId, 'order_id' => $order_id, 'order_status' => $order_status, ], $createParams);
                } else {
                    $order_id = $lead['id'] ?? '';
                    $order_status = $lead['order_status'] ?? '';

                    $insertParams = [
                        'user_id' => $lead->customer_id,
                        'message' => $flowAction['message_title'],
                        'status' => 1,
                        'is_queue' => 1,
                        'approved' => 0,
                        'number' => null,
                        'message_application_id' => $messageApplicationId,
                        'scheduled_at' => $created_date,
                        'flow_exit' => 1,  /* if the message is coming from flow */
                        'order_id' => $order_id,
                        'order_status' => $order_status,

                    ];

                    $createParams = array_merge($extraParams, $insertParams);
                    ChatMessage::updateOrCreate(['user_id' => $lead->customer_id, 'message' => $flowAction['message_title'],

                        'status' => 1, 'is_queue' => 1, 'approved' => 1, 'message_application_id' => $messageApplicationId, 'order_id' => $order_id, 'order_status' => $order_status, ], $createParams);
                }

                $flowLogMessage = FlowLogMessages::where([
                    'flow_action' => $flowAction['type'],
                    'modalType' => $modalType,
                    'leads' => $lead->customer_id,
                    'store_website_id' => $store_website_id,
                    'messages' => $flowAction['message_title'].' ('.$created_date.')',
                    'flow_log_id' => $flow_log_id,
                    'scraper_id' => $scraper_id,
                ])->first();

                if ($flowLogMessage == null) {
                    FlowLogMessages::log([
                        'flow_action' => $flowAction['type'],
                        'modalType' => $modalType,
                        'leads' => $lead->customer_id,
                        'store_website_id' => $store_website_id,
                        'messages' => $flowAction['message_title'].' ('.$created_date.')',
                        'flow_log_id' => $flow_log_id,
                        'scraper_id' => $scraper_id,
                    ]);
                }
            }
        } elseif ($flowAction['type'] == 'Condition') {
            if ($flowAction['condition'] == 'customer has ordered before') {
                $flowPathsNew = FlowAction::join('flow_paths', 'flow_actions.path_id', '=', 'flow_paths.id')
                    ->join('flows', 'flow_paths.flow_id', '=', 'flows.id')
                    ->join('flow_types', 'flow_types.id', '=', 'flow_actions.type_id')
                    ->select('flows.store_website_id', 'flow_actions.id as action_id', 'flow_actions.time_delay', 'flow_actions.message_title', 'flow_types.type', 'flow_actions.time_delay_type', 'flows.flow_name');
                if (in_array('customer_has_ordered_before_flow_paths_parent_action_id', $allflowconditions)) {
                    $flowPathsNew = $flowPathsNew->where('flow_paths.parent_action_id', '=', $flowAction['action_id']);
                }

                $flowPathsNew = $flowPathsNew->orderBy('flow_actions.rank')->get()->groupBy('path_for');
                foreach ($flowPathsNew as $path_for => $flowActiosnNew) {
                    if ($path_for == 'yes') {
                        $leads = Order::leftJoin('customers', 'orders.customer_id', '=', 'customers.id')->where('customers.store_website_id', $store_website_id)
                            ->whereIn('orders.order_status', ['delivered', 'Delivered'])
                            ->where('orders.date_of_delivery', 'like', Carbon::now()->format('Y-m-d').'%')
                            ->select('orders.id', 'orders.customer_id', 'customers.name as customer_name', 'customers.email as customer_email', 'customers.id as customer_id', DB::raw('count(*) as duplicate'))
                            ->groupBy('orders.customer_id')->having(DB::raw('count(*)'), '>', 1)->get();
                    } else {
                        $leads = Order::leftJoin('customers', 'orders.customer_id', '=', 'customers.id')
                            ->where('customers.store_website_id', $store_website_id)
                            ->whereIn('orders.order_status', ['delivered', 'Delivered'])
                            ->where('orders.date_of_delivery', 'like', Carbon::now()->format('Y-m-d').'%')
                            ->select('orders.id', 'orders.customer_id', 'customers.name as customer_name', 'customers.email as customer_email', 'customers.id as customer_id', DB::raw('count(*) as duplicate'))
                            ->groupBy('orders.customer_id')->having(DB::raw('count(*)'), 1)->get();
                    }
                    foreach ($flowActiosnNew as $flowActionNew) {
                        $this->doProcess($flowActionNew, $modalType, $leads, $store_website_id, $created_date, $flow_log_id, '', $allflowconditions);
                    }
                }
            } elseif ($flowAction['condition'] == 'check_if_pr_merged') {
                $flowPathsNew = FlowAction::join('flow_paths', 'flow_actions.path_id', '=', 'flow_paths.id')
                    ->join('flows', 'flow_paths.flow_id', '=', 'flows.id')
                    ->join('flow_types', 'flow_types.id', '=', 'flow_actions.type_id')
                    ->select('flows.store_website_id', 'flow_actions.id as action_id', 'flow_actions.time_delay', 'flow_actions.message_title', 'flow_types.type', 'flow_actions.condition', 'flow_actions.time_delay_type', 'flows.flow_name', 'flow_paths.path_for');
                $flowPathsNew = $flowPathsNew->where('flow_paths.parent_action_id', '=', $flowAction['action_id']);
                $flowPathsNew = $flowPathsNew->orderBy('flow_actions.rank')->get()->groupBy('path_for');

                foreach ($flowPathsNew as $path_for => $flowActiosnNew) {
                    if ($path_for == 'yes') {
                        $leads = DeveloperTask::leftJoin('users', 'users.id', '=', 'developer_tasks.assigned_to');
                        if (in_array('check_if_pr_merged_yes_flow_paths_developer_tasks_created_at', $allflowconditions)) {
                            $leads = $leads->whereDate('developer_tasks.created_at', '<=', $created_date);
                        }
                        if (in_array('check_if_pr_merged_yes_flow_paths_scraper_id', $allflowconditions)) {
                            $leads = $leads->where('scraper_id', '<>', 0)->whereNotNull('scraper_id');
                        }
                        if (in_array('check_if_pr_merged_yes_flow_paths_is_pr_merged', $allflowconditions)) {
                            $leads = $leads->where('is_pr_merged', 1);
                        }

                        $leads = $leads->select('developer_tasks.id', 'developer_tasks.scraper_id', 'users.name as customer_name', 'users.email as customer_email', 'users.id as customer_id')->orderByDesc('developer_tasks.id')->first();
                    } else {
                        $leads = DeveloperTask::leftJoin('users', 'users.id', '=', 'developer_tasks.assigned_to');
                        if (in_array('check_if_pr_merged_no_flow_paths_developer_tasks_created_at', $allflowconditions)) {
                            $leads = $leads->whereDate('developer_tasks.created_at', '<=', $created_date);
                        }
                        if (in_array('check_if_pr_merged_no_flow_paths_scraper_id', $allflowconditions)) {
                            $leads = $leads->where('scraper_id', '<>', 0)->whereNotNull('scraper_id');
                        }
                        if (in_array('check_if_pr_merged_no_flow_paths_is_pr_not_merged', $allflowconditions)) {
                            $leads = $leads->where('is_pr_merged', 0);
                        }

                        $leads = $leads->select('developer_tasks.id', 'developer_tasks.scraper_id', 'users.name as customer_name', 'users.email as customer_email', 'users.id as customer_id')->orderByDesc('developer_tasks.id')->first();
                    }

                    foreach ($flowActiosnNew as $flowActionNew) {
                        $this->doProcess($flowActionNew, $modalType, $leads, $store_website_id, $created_date, $flow_log_id, 'user', $allflowconditions);
                    }
                }
            } elseif ($flowAction['condition'] == 'check_scrapper_error_logs') {
                $leads = [];
                $flowPathsNew = FlowAction::join('flow_paths', 'flow_actions.path_id', '=', 'flow_paths.id')
                    ->join('flows', 'flow_paths.flow_id', '=', 'flows.id')
                    ->join('flow_types', 'flow_types.id', '=', 'flow_actions.type_id')
                    ->select('flows.store_website_id', 'flow_actions.id as action_id', 'flow_actions.time_delay', 'flow_actions.message_title', 'flow_types.type', 'flow_actions.time_delay_type', 'flows.flow_name')
                    ->where('flow_paths.parent_action_id', '=', $flowAction['action_id'])
                    ->where('path_for', 'yes')
                    ->get()->groupBy('path_for');
                foreach ($flowPathsNew as $path_for => $flowActiosnNew) {
                    $leads = DeveloperTask::leftJoin('users', 'users.id', '=', 'developer_tasks.assigned_to')
                        ->whereDate('developer_tasks.created_at', '<=', $created_date);
                    if (in_array('check_if_srapper_error_task_status_not_done', $allflowconditions)) {
                        $leads = $leads->where('developer_tasks.status', '<>', 'Done');
                    }
                    $leads = $leads->where('scraper_id', '<>', 0)
                        ->whereNotNull('scraper_id')
                        ->where('is_pr_merged', 1)
                        ->select('developer_tasks.id', 'developer_tasks.scraper_id', 'users.name as customer_name', 'users.email as customer_email', 'users.id as customer_id')->get();

                    foreach ($leads as $key => $scrapperTask) {
                        $log = ScrapLog::where('scraper_id', $scrapperTask['scraper_id'])->orderByDesc('id')->first();
                        if ($log['status'] == 'success') {
                            unset($leads[$key]);
                            DeveloperTask::where('id', $scrapperTask['id'])->update(['status' => 'Done']);
                        }
                    }
                    foreach ($flowActiosnNew as $flowActionNew) {
                        $this->doProcess($flowActionNew, $modalType, $leads, $store_website_id, $created_date, $flow_log_id, 'user', $allflowconditions);
                    }
                }
            } elseif ($flowAction['condition'] == 'check_if_design_task_done') {
                $leads = [];
                $flowPathsNew = FlowAction::join('flow_paths', 'flow_actions.path_id', '=', 'flow_paths.id')
                    ->join('flows', 'flow_paths.flow_id', '=', 'flows.id')
                    ->join('flow_types', 'flow_types.id', '=', 'flow_actions.type_id')
                    ->select('flows.store_website_id', 'flow_actions.id as action_id', 'flow_actions.time_delay', 'flow_actions.message_title', 'flow_types.type', 'flow_actions.condition', 'flow_actions.time_delay_type', 'flows.flow_name', 'flow_paths.path_for')
                    ->where('flow_paths.parent_action_id', '=', $flowAction['action_id'])->orderBy('flow_actions.rank')
                    ->get()->groupBy('path_for');

                foreach ($flowPathsNew as $path_for => $flowActiosnNew) {
                    $designCategoryId = TaskCategory::where('title', 'like', 'Design%')->pluck('id')->first();
                    if ($path_for == 'yes') {
                        $parentTaskIds = Task::whereNotNull('parent_task_id')->pluck('parent_task_id')->toArray();
                        $tasks = Task::leftJoin('users', 'users.id', '=', 'tasks.assign_to')
                            ->whereDate('tasks.is_completed', '<=', $created_date)
                            ->where('category', $designCategoryId)
                            ->whereNotNull('is_completed')
                            ->whereNotIn('tasks.id', $parentTaskIds)
                            ->select('tasks.id', 'tasks.task_subject', 'tasks.task_details', 'tasks.site_developement_id', 'users.name as customer_name', 'users.email as customer_email', 'users.id as customer_id')->get();

                        $devCategoryId = TaskCategory::where('title', 'like', 'Site Devel%')->pluck('id')->first();
                        foreach ($tasks as $task) {
                            $ifAlreadyCreated = Task::where('parent_task_id', $task['id'])->first();
                            if ($ifAlreadyCreated == null) {
                                $requests = [
                                    'task_subject' => $task['task_subject'].' Development',
                                    'task_detail' => $task['task_details'],
                                    'task_asssigned_to' => 6,
                                    'task_asssigned_from' => 6,
                                    'category_id' => $devCategoryId,
                                    'site_id' => $task['site_developement_id'],
                                    'task_type' => 0,
                                    'repository_id' => null,
                                    'cost' => null,
                                    'task_id' => null,
                                    'customer_id' => null,
                                    'parent_task_id' => $task['id'],
                                ];
                                (new Task)->createTaskFromSortcuts($requests);
                            }
                        }
                    } else {
                        $leads = Task::leftJoin('users', 'users.id', '=', 'tasks.assign_to')->where('category', $designCategoryId)
                            ->whereDate('tasks.created_at', '<=', $created_date)
                            ->where('category', $designCategoryId)
                            ->select('tasks.id', 'users.name as customer_name', 'users.email as customer_email', 'users.id as customer_id')->get();
                    }
                    foreach ($flowActiosnNew as $flowActionNew) {
                        $this->doProcess($flowActionNew, $modalType, $leads, $store_website_id, $created_date, $flow_log_id, '', $allflowconditions);
                    }
                }
            } elseif ($flowAction['condition'] == 'check_if_development_task_done') {
                $leads = [];
                $flowPathsNew = FlowAction::join('flow_paths', 'flow_actions.path_id', '=', 'flow_paths.id')
                    ->join('flows', 'flow_paths.flow_id', '=', 'flows.id')
                    ->join('flow_types', 'flow_types.id', '=', 'flow_actions.type_id')
                    ->select('flows.store_website_id', 'flow_actions.id as action_id', 'flow_actions.time_delay', 'flow_actions.message_title', 'flow_types.type', 'flow_actions.condition', 'flow_actions.time_delay_type', 'flows.flow_name', 'flow_paths.path_for')
                    ->where('flow_paths.parent_action_id', '=', $flowAction['action_id'])->orderBy('flow_actions.rank')
                    ->get()->groupBy('path_for');
                $flowPathsNew['qa_task'] = [];
                foreach ($flowPathsNew as $path_for => $flowActiosnNew) {
                    $devCategoryId = TaskCategory::where('title', 'like', 'Site Devel%')->pluck('id')->first();
                    if ($path_for == 'no') {
                        $leads = Task::leftJoin('users', 'users.id', '=', 'tasks.assign_to')->where('category', $devCategoryId)
                            ->whereDate('tasks.created_at', '<', $created_date)->whereNull('is_completed')
                            ->select('tasks.id', 'users.name as customer_name', 'users.email as customer_email', 'users.id as customer_id')->get();
                    } else {
                        $parentTaskIds = Task::whereNotNull('parent_task_id')->pluck('parent_task_id')->toArray();
                        $tasks = Task::leftJoin('users', 'users.id', '=', 'tasks.assign_to')
                            ->whereDate('tasks.is_completed', '<=', $created_date)
                            ->where('category', $devCategoryId)
                            ->whereNotNull('is_completed')
                            ->whereNotIn('tasks.id', $parentTaskIds)
                            ->select('tasks.id', 'tasks.task_subject', 'tasks.task_details', 'tasks.site_developement_id', 'users.name as customer_name', 'users.email as customer_email', 'users.id as customer_id')->get();
                        $qaCatId = TaskCategory::where('title', 'like', 'qa%')->orWhere('title', 'like', '%testing%')->pluck('id')->first();
                        if ($qaCatId != null) {
                            foreach ($tasks as $task) {
                                $ifAlreadyCreated = Task::where('parent_task_id', $task['id'])->first();
                                if ($ifAlreadyCreated == null) {
                                    $requests = [
                                        'task_subject' => $task['task_subject'],
                                        'task_subject' => $task['task_subject'].' QA',
                                        'task_detail' => $task['task_details'],
                                        'task_asssigned_to' => 6,
                                        'task_asssigned_from' => 6,
                                        'category_id' => $qaCatId,
                                        'site_id' => $task['site_developement_id'],
                                        'task_type' => 0,
                                        'repository_id' => null,
                                        'cost' => null,
                                        'task_id' => null,
                                        'customer_id' => null,
                                        'parent_task_id' => $task['id'],
                                    ];
                                    $check = (new Task)->createTaskFromSortcuts($requests);
                                }
                            }
                        } else {
                            $leads = Task::leftJoin('users', 'users.id', '=', 'tasks.assign_to')->where('category', $devCategoryId)
                                ->whereDate('tasks.created_at', '<', $created_date)->whereNull('is_completed')
                                ->select('tasks.id', 'users.name as customer_name', 'users.email as customer_email', 'users.id as customer_id')->get();
                        }
                    }
                    foreach ($flowActiosnNew as $flowActionNew) {
                        $this->doProcess($flowActionNew, $modalType, $leads, $store_website_id, $created_date, $flow_log_id, '', $allflowconditions);
                    }
                }
            }
        }
    }
}
