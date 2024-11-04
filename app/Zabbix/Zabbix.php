<?php

namespace App\Zabbix;

use Carbon\Carbon;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;
use Throwable;

class Zabbix
{
    const ZABBIX_ID = 1;

    public function __construct()
    {
        $this->curl = new Client([
            'base_uri' => config('settings.zabbix_host').'/api_jsonrpc.php',
        ]);
    }

    public function getLoginApi(): int
    {
        $url = config('settings.zabbix_host').'/api_jsonrpc.php';
        $curl = curl_init($url);
        $data = [
            'jsonrpc' => '2.0',
            'method' => 'user.login',
            'params' => [
                'username' => config('settings.zabbix_username'),
                'password' => config('settings.zabbix_password'),
            ],
            'id' => 1,
        ];

        $datas = json_encode([$data]);

        curl_setopt($curl, CURLOPT_POSTFIELDS, $datas);
        curl_setopt($curl, CURLOPT_HTTPHEADER, ['Content-Type:application/json']);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        $result = curl_exec($curl);
        curl_close($curl);

        $results = json_decode($result);

        if (! isset($results[0])) {
            Log::channel('general')->info('Response error: '.Carbon::now().' '.json_encode($results));

            return 0;
        }

        try {
            if (isset($results[0]->result)) {
                return $results[0]->result;
            } else {
                Log::channel('general')->info(Carbon::now().$results[0]->error->data);

                return 0;
            }
        } catch (Throwable $e) {
            return 0;
        }
    }

    /**
     * @return mixed
     *
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getAllUsers()
    {
        $request = $this->curl->post('', [
            'json' => [
                'jsonrpc' => '2.0',
                'method' => 'user.get',
                'params' => [

                ],
                'auth' => $this->getLoginApi(),
                'id' => self::ZABBIX_ID,
            ],
        ]);

        $body = json_decode((string) $request->getBody(), true);

        return $body['result'];
    }

    /**
     * @param  mixed  $id
     * @return mixed|null
     *
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getUserByIds($id)
    {
        $request = $this->curl->post('', [
            'json' => [
                'jsonrpc' => '2.0',
                'method' => 'user.get',
                'params' => [
                    'userids' => $id,
                ],
                'auth' => $this->getLoginApi(),
                'id' => self::ZABBIX_ID,
            ],
        ]);

        $body = json_decode((string) $request->getBody(), true);

        return ! empty($body['result']) ? $body['result'][0] : null;
    }

    public function getRoleByIds($id)
    {
        $request = $this->curl->post('', [
            'json' => [
                'jsonrpc' => '2.0',
                'method' => 'role.get',
                'params' => [
                    'roleids' => $id,
                ],
                'auth' => $this->getLoginApi(),
                'id' => self::ZABBIX_ID,
            ],
        ]);

        $body = json_decode((string) $request->getBody(), true);

        if (! empty($body['error'])) {
            throw new ZabbixException($body['error']['data']);
        }

        return ! empty($body['result']) ? $body['result'][0] : null;
    }

    /**
     * @param  mixed  $id
     * @return mixed|null
     *
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getItemByIds($id)
    {
        $request = $this->curl->post('', [
            'json' => [
                'jsonrpc' => '2.0',
                'method' => 'item.get',
                'params' => [
                    'itemids' => $id,
                ],
                'auth' => $this->getLoginApi(),
                'id' => self::ZABBIX_ID,
            ],
        ]);

        $body = json_decode((string) $request->getBody(), true);

        return ! empty($body['result']) ? $body['result'][0] : null;
    }

    public function getHostById($id)
    {
        $request = $this->curl->post('', [
            'json' => [
                'jsonrpc' => '2.0',
                'method' => 'hostinterface.get',
                'params' => [
                    'output' => 'extend',
                    'hostids' => $id,
                ],
                'auth' => $this->getLoginApi(),
                'id' => self::ZABBIX_ID,
            ],
        ]);

        $body = json_decode((string) $request->getBody(), true);

        return ! empty($body['result']) ? $body['result'][0] : null;
    }

    /**
     * @return mixed
     *
     * @throws ZabbixException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function saveUser(array $params = [])
    {
        $request = $this->curl->post('', [
            'json' => [
                'jsonrpc' => '2.0',
                'method' => 'user.create',
                'params' => $params,
                'auth' => $this->getLoginApi(),
                'id' => self::ZABBIX_ID,
            ],
        ]);

        $body = json_decode((string) $request->getBody(), true);

        if (! empty($body['error'])) {
            throw new ZabbixException($body['error']['data']);
        }

        return $body['result'];
    }

    public function deleteUser($id)
    {
        if ($id == 1) {
            throw new ZabbixException('Cannot remove Admin user.');
        }
        $request = $this->curl->post('', [
            'json' => [
                'jsonrpc' => '2.0',
                'method' => 'user.delete',
                'params' => [$id],
                'auth' => $this->getLoginApi(),
                'id' => self::ZABBIX_ID,
            ],
        ]);

        $body = json_decode((string) $request->getBody(), true);

        if (! empty($body['error'])) {
            throw new ZabbixException($body['error']['data']);
        }

        return $body['result'];
    }

    public function deleteHost($id)
    {
        $request = $this->curl->post('', [
            'json' => [
                'jsonrpc' => '2.0',
                'method' => 'host.delete',
                'params' => [$id],
                'auth' => $this->getLoginApi(),
                'id' => self::ZABBIX_ID,
            ],
        ]);

        $body = json_decode((string) $request->getBody(), true);

        if (! empty($body['error'])) {
            throw new ZabbixException($body['error']['data']);
        }

        return $body['result'];
    }

    public function saveRole(array $params = [], $action = 'create')
    {
        $request = $this->curl->post('', [
            'json' => [
                'jsonrpc' => '2.0',
                'method' => "role.$action",
                'params' => $params,
                'auth' => $this->getLoginApi(),
                'id' => self::ZABBIX_ID,
            ],
        ]);

        $body = json_decode((string) $request->getBody(), true);

        if (! empty($body['error'])) {
            throw new ZabbixException($body['error']['data']);
        }

        return $body['result'];
    }

    public function saveHost(array $params = [], $action = 'create')
    {
        $request = $this->curl->post('', [
            'json' => [
                'jsonrpc' => '2.0',
                'method' => "host.$action",
                'params' => $params,
                'auth' => $this->getLoginApi(),
                'id' => self::ZABBIX_ID,
            ],
        ]);

        $body = json_decode((string) $request->getBody(), true);

        if (! empty($body['error'])) {
            throw new ZabbixException($body['error']['data']);
        }

        return $body['result'];
    }

    public function deleteItem($id)
    {
        $request = $this->curl->post('', [
            'json' => [
                'jsonrpc' => '2.0',
                'method' => 'item.delete',
                'params' => [$id],
                'auth' => $this->getLoginApi(),
                'id' => self::ZABBIX_ID,
            ],
        ]);

        $body = json_decode((string) $request->getBody(), true);

        if (! empty($body['error'])) {
            throw new ZabbixException($body['error']['data']);
        }

        return $body['result'];
    }

    /**
     * @return mixed
     *
     * @throws ZabbixException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function updateUser(array $params = [])
    {
        $request = $this->curl->post('', [
            'json' => [
                'jsonrpc' => '2.0',
                'method' => 'user.update',
                'params' => $params,
                'auth' => $this->getLoginApi(),
                'id' => self::ZABBIX_ID,
            ],
        ]);

        $body = json_decode((string) $request->getBody(), true);

        if (! empty($body['error'])) {
            throw new ZabbixException($body['error']['data']);
        }

        return $body['result'];
    }

    public function saveTrigger(array $params = [])
    {
        $request = $this->curl->post('', [
            'json' => [
                'jsonrpc' => '2.0',
                'method' => 'trigger.create',
                'params' => $params,
                'auth' => $this->getLoginApi(),
                'id' => self::ZABBIX_ID,
            ],
        ]);

        $body = json_decode((string) $request->getBody(), true);

        if (! empty($body['error'])) {
            throw new ZabbixException($body['error']['data']);
        }

        return $body['result'];
    }

    public function saveItem($params)
    {
        $request = $this->curl->post('', [
            'json' => [
                'jsonrpc' => '2.0',
                'method' => 'item.create',
                'params' => $params,
                'auth' => $this->getLoginApi(),
                'id' => self::ZABBIX_ID,
            ],
        ]);

        $body = json_decode((string) $request->getBody(), true);

        if (! empty($body['error'])) {
            throw new ZabbixException($body['error']['data']);
        }

        return $body['result'];
    }

    public function updateItem($params)
    {
        $request = $this->curl->post('', [
            'json' => [
                'jsonrpc' => '2.0',
                'method' => 'item.update',
                'params' => $params,
                'auth' => $this->getLoginApi(),
                'id' => self::ZABBIX_ID,
            ],
        ]);

        $body = json_decode((string) $request->getBody(), true);

        if (! empty($body['error'])) {
            throw new ZabbixException($body['error']['data']);
        }

        return $body['result'];
    }

    public function updateTrigger(array $params = [])
    {
        $request = $this->curl->post('', [
            'json' => [
                'jsonrpc' => '2.0',
                'method' => 'trigger.update',
                'params' => $params,
                'auth' => $this->getLoginApi(),
                'id' => self::ZABBIX_ID,
            ],
        ]);

        $body = json_decode((string) $request->getBody(), true);

        if (! empty($body['error'])) {
            throw new ZabbixException($body['error']['data']);
        }

        return $body['result'];
    }

    /**
     * @return mixed
     *
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getAllItems()
    {
        $request = $this->curl->post('', [
            'json' => [
                'jsonrpc' => '2.0',
                'method' => 'item.get',
                'params' => [
                    'limit' => 50,
                    'sortfield' => 'itemid',
                    'sortorder' => 'DESC',
                ],

                'auth' => $this->getLoginApi(),
                'id' => self::ZABBIX_ID,
            ],
        ]);

        $body = json_decode((string) $request->getBody(), true);

        return $body['result'];
    }

    /**
     * @return mixed
     *
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getAllItemsByHostId(int $hostId)
    {
        $request = $this->curl->post('', [
            'json' => [
                'jsonrpc' => '2.0',
                'method' => 'item.get',
                'params' => [
                    'limit' => 1000,
                    'hostids' => $hostId,
                ],

                'auth' => $this->getLoginApi(),
                'id' => self::ZABBIX_ID,
            ],
        ]);

        $body = json_decode((string) $request->getBody(), true);

        return $body['result'];
    }

    public function getAllUserRoles()
    {
        $request = $this->curl->post('', [
            'json' => [
                'jsonrpc' => '2.0',
                'method' => 'role.get',
                'params' => [
                ],
                'auth' => $this->getLoginApi(),
                'id' => self::ZABBIX_ID,
            ],
        ]);

        $body = json_decode((string) $request->getBody(), true);

        return $body['result'];
    }

    public function getAllTriggers($page = 1)
    {
        $request = $this->curl->post('', [
            'json' => [
                'jsonrpc' => '2.0',
                'method' => 'trigger.get',
                'params' => [
                ],
                'auth' => $this->getLoginApi(),
                'id' => self::ZABBIX_ID,
            ],
        ]);

        $body = json_decode((string) $request->getBody(), true);

        return $body['result'];
    }

    public function getAllTemplates(): array
    {
        $request = $this->curl->post('', [
            'json' => [
                'jsonrpc' => '2.0',
                'method' => 'template.get',
                'params' => [
                    'limit' => 20000,
                ],
                'auth' => $this->getLoginApi(),
                'id' => self::ZABBIX_ID,
            ],
        ]);

        $body = json_decode((string) $request->getBody(), true);

        if (! empty($body['error'])) {
            throw new ZabbixException($body['error']['data']);
        }

        return $body['result'];
    }

    public function getTriggerById($id)
    {
        $request = $this->curl->post('', [
            'json' => [
                'jsonrpc' => '2.0',
                'method' => 'trigger.get',
                'params' => [
                    'triggerids' => $id,
                ],

                'auth' => $this->getLoginApi(),
                'id' => self::ZABBIX_ID,
            ],
        ]);

        $body = json_decode((string) $request->getBody(), true);

        if (! empty($body['error'])) {
            throw new ZabbixException($body['error']['data']);
        }

        return ! empty($body['result']) ? $body['result'][0] : null;
    }
}
