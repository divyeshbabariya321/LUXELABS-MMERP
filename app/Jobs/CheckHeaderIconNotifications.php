<?php

namespace App\Jobs;
use App\Events\HeaderIconNotificationsFound;

use Illuminate\Queue\SerializesModels;
use Illuminate\Foundation\Bus\Dispatchable;

class CheckHeaderIconNotifications
{
    use Dispatchable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        for ($i = 0; $i < 10; $i++) {
            $notifications = [];
            $notifications = $this->getScriptsExecutionHistory($notifications);

            if (!empty($notifications)) {
                event(new HeaderIconNotificationsFound($notifications));
            }

            sleep(8);
        }
    }

    private function getScriptsExecutionHistory($notifications)
    {
        $count = 0;
        $count = getScriptsExecutionHistory()->count();

        if ($count) {
            array_push($notifications, ['ScriptsExecutionHistory' => ['count' => $count]]);
        }

        return $notifications;
    }
}
