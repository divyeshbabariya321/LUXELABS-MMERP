<?php

namespace App\Console\Commands;

use App\ChatMessage;
use App\CronJob;
use App\CronJobReport;
use App\DatabaseHistoricalRecord;
use App\DatabaseTableHistoricalRecord;
use App\Helpers\LogHelper;
use App\Http\Controllers\WhatsAppController;
use Carbon\Carbon;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class AddDatabaseHistoricalData extends Command
{
    const MAX_REACH_LIMIT = 100;

    const MAX_REACH_TOTAL_LIMIT = 4096;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'database:historical-data';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Insert historical data';

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
            LogHelper::createCustomLogForCron($this->signature, ['message' => 'Cron was started to run']);

            $report = CronJobReport::create([
                'signature' => $this->signature,
                'start_time' => Carbon::now(),
            ]);
            LogHelper::createCustomLogForCron($this->signature, ['message' => 'report was added.']);

            // get the historical data and store into the new table
            $db = DB::select('SELECT table_schema as "db_name",Round(Sum(data_length + index_length) / 1024 / 1024, 1) as "db_size"
                FROM information_schema.tables  where table_schema = "'.config('settings.db_database').'" GROUP  BY table_schema'
            );
            LogHelper::createCustomLogForCron($this->signature, ['message' => 'table schema query was finished.']);

            $lastDb = DatabaseHistoricalRecord::where('database_name', config('settings.db_database'))->latest()->first();
            LogHelper::createCustomLogForCron($this->signature, ['message' => 'Database historical record query was finished.']);
            if (! empty($db)) {
                foreach ($db as $d) {
                    // check the last db size and current size and manage with it
                    LogHelper::createCustomLogForCron($this->signature, ['message' => 'Comparing the database name & size']);

                    if ($lastDb) {
                        if ($lastDb->database_name == $d->db_name) {
                            if (($d->db_size - $lastDb->size) >= self::MAX_REACH_LIMIT) {
                                CronJob::insertLastError($this->signature,
                                    'Database is reached to the max limit : '.self::MAX_REACH_LIMIT.' MB'
                                );
                            } elseif ($d->db_size > self::MAX_REACH_TOTAL_LIMIT) {
                                CronJob::insertLastError($this->signature,
                                    'Database is reached to the max total limit : '.self::MAX_REACH_TOTAL_LIMIT.' MB'
                                );
                            }
                        }
                    }

                    $database_recent_entry = DatabaseHistoricalRecord::create([
                        'database_name' => $d->db_name,
                        'size' => $d->db_size,
                    ]);
                    LogHelper::createCustomLogForCron($this->signature, ['message' => 'Database historical record query was finished.']);
                    $db_table = DB::select('SELECT TABLE_NAME as "db_table_name", Round(Sum(data_length + index_length) / 1024, 1) as "db_size" FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_TYPE = "BASE TABLE" AND TABLE_SCHEMA="'.$d->db_name.'" GROUP  BY TABLE_NAME'
                    );
                    foreach ($db_table as $d_table) {
                        $databaseTableHistoricalRecord = DatabaseTableHistoricalRecord::where('database_name', $d_table->db_table_name)->where('database_id', $database_recent_entry->id)->orderBy('created_at')->first();
                        LogHelper::createCustomLogForCron($this->signature, ['message' => 'Database table historical record query was finished.']);
                        if ($databaseTableHistoricalRecord) {
                            $v1 = $databaseTableHistoricalRecord->size;
                            $v2 = $d_table->db_size;
                            $differance = (($v1 - $v2) / (($v1 + $v2) / 2)) * 100;
                            if ($differance > 10) {
                                $user_id = 6;
                                $message = $d->db_name.'.'.$d_table->db_table_name.' Database table increased size more than 10%.';
                                $params = [];
                                $params['message'] = $message;
                                $params['erp_user'] = $user_id;
                                $params['user_id'] = $user_id;
                                $params['approved'] = 1;
                                $params['status'] = 2;
                                $params['message_application_id'] = 10001;
                                $chat_message = ChatMessage::create($params);

                                LogHelper::createCustomLogForCron($this->signature, ['message' => 'Saved chat message record by ID:'.$chat_message->id]);

                                $requestData = new Request;
                                $requestData->setMethod('POST');
                                $requestData->request->add(['user_id' => $user_id, 'message' => $message, 'status' => 1]);
                                app(WhatsAppController::class)->sendMessage($requestData, 'user');

                                LogHelper::createCustomLogForCron($this->signature, ['message' => 'Message sent successfully.']);
                            }
                        }
                        DatabaseTableHistoricalRecord::create([
                            'database_name' => $d_table->db_table_name,
                            'size' => $d_table->db_size,
                            'database_id' => $database_recent_entry->id,
                        ]);
                        LogHelper::createCustomLogForCron($this->signature, ['message' => 'Database table historical record query was finished.']);
                    }
                }
            }

            $report->update(['end_time' => Carbon::now()]);
            LogHelper::createCustomLogForCron($this->signature, ['message' => 'report endtime was updated.']);
        } catch (Exception $e) {
            LogHelper::createCustomLogForCron($this->signature, ['Exception' => $e->getTraceAsString(), 'message' => $e->getMessage()]);

            CronJob::insertLastError($this->signature, $e->getMessage());
        }
    }
}
