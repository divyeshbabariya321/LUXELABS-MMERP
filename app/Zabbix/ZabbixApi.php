<?php

namespace App\Zabbix;

use IntelliTrend\Zabbix\ZabbixApi as CoreZabbixApi;

class ZabbixApi extends CoreZabbixApi
{
    public function __construct()
    {
        parent::__construct();
        $this->login(config('settings.zabbix_host'), config('settings.zabbix_username'), config('settings.zabbix_password'));
    }
}
