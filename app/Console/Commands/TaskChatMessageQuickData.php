<?php

namespace App\Console\Commands;

use App\ChatMessagesQuickData;
use App\Task;
use Illuminate\Console\Command;

class TaskChatMessageQuickData extends Command
{
    /***
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'task:chat-message-quick-data';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Get task last message and store it into new table';

    /**
     * Create a new command instance.
     */

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        Task::with(['allMessages' => function ($qr) {
            $qr->orderByDesc('created_at');
        }])->chunk(100, function ($tasks) {
            foreach ($tasks as $task) {
                if (count($task->allMessages)) {
                    foreach ($task->allMessages as $item1) {
                        $data['last_unread_message'] = ($item1->status == 0) ? $item1->message : null;
                        $data['last_unread_message_at'] = ($item1->status == 0) ? $item1->created_at : null;
                        $data['last_communicated_message'] = ($item1->status > 0) ? $item1->message : null;

                        $data['last_communicated_message_at'] = ($item1->status > 0) ? $item1->created_at : null;
                        $data['last_unread_message_id'] = null;
                        $data['last_communicated_message_id'] = null;

                        if (! empty($data['last_unread_message'])) {
                            $data['last_unread_message_id'] = $item1->id;
                        }
                        if (! empty($data['last_communicated_message'])) {
                            $data['last_communicated_message_id'] = $item1->id;
                        }

                        if (! empty($data['last_unread_message']) || ! empty($data['last_communicated_message'])) {
                            ChatMessagesQuickData::updateOrCreate([
                                'model' => Task::class,

                                'model_id' => $item1->task_id,
                            ], $data);
                            break;
                        }
                    }
                }
            }
        });
    }
}
