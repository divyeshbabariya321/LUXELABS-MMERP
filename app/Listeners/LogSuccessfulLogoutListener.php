<?php

namespace App\Listeners;

use App\Http\Controllers\ActivityConroller;

class LogSuccessfulLogoutListener
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param object $event
     */
    public function handle(): void
    {
        ActivityConroller::create(0, 'User', 'Logout');
    }
}
