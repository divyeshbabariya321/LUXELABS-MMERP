<?php

use App\Http\Controllers\Marketing\MailinglistController;
use App\Jobs\CheckAppointment;
use App\Jobs\CheckHeaderIconNotifications;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote')->hourly();

Schedule::call(function () {
    MailinglistController::sendAutoEmails();
})->hourly();

//Telescope Remove Logs Every 72Hrs
Schedule::command('telescope:prune --hours=72')->daily();
Schedule::command('reindex:messages')->dailyAt('00:00');
Schedule::command('store:zabbix')->everyFiveMinutes();
Schedule::command('zabbix:problem')->everyFiveMinutes();
Schedule::command('store:zabbixhostitems')->everyFiveMinutes();
Schedule::command('insert-sonar-qube')->dailyAt('23:58');
Schedule::command('insert-varnish-records')->everyFiveMinutes();
Schedule::command('compare-scrapper-images')->dailyAt('23:58');
//Execute inventory update on hourly basis.
Schedule::command('inventory:update')->hourly();

Schedule::call(function () {
    dispatch(new CheckAppointment());
    dispatch(new CheckHeaderIconNotifications());
})->everyMinute();
