<?php

declare(strict_types=1);

namespace App\Models\Zabbix;

use App\Zabbix\Zabbix;

class Host
{
    /**
     * @var Zabbix
     */
    private $zabbix;

    public function __construct()
    {
        $this->zabbix = new Zabbix();
    }

    public function save(array $params, $action = 'create'): void
    {
        $this->zabbix->saveHost($params, $action);
    }

    public function delete(int $id): void
    {
        $this->zabbix->deleteHost($id);
    }

    public function getById(int $hostId)
    {
        return $this->zabbix->getHostById($hostId);
    }
}
