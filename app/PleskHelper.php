<?php

namespace App;

use App\plesk\PleskClient;
use Exception;
use PleskX\Api\Client;

/**
 * @SWG\Definition(type="object", @SWG\Xml(name="User"))
 */
class PleskHelper
{
    private $_options = null;

    public function __construct()
    {
        $this->_options = [
            'username' => getenv('PLESK_USERNAME'),
            'password' => getenv('PLESK_PASSWORD'),
            'ip' => getenv('PLESK_IP'),
        ];
    }

    public function getDomains()
    {
        $client = new PleskClient($this->_options['ip']);
        $client->setCredentials($this->_options['username'], $this->_options['password']);

        $field = null;
        $value = null;
        $dns = $client->dns()->getAll($field, $value);
        $domains = [];
        if (count($dns) > 0) {
            for ($i = 0; $i < count($dns); $i++) {
                try {
                    $str = substr($dns[$i]->host, 0, -1);
                    $d = $client->server()->getDomain($str);
                    $temp = [];
                    $temp['id'] = $d['id'];
                    $temp['name'] = $d['filter-id'];
                    if (! in_array($temp, $domains)) {
                        $domains[] = $temp;
                    }
                } catch (Exception $e) {
                    return response()->json(['status' => 400, 'message' => 'Opps! Something went wrong, Please try again.']);

                }
            }
        }

        return $domains;
    }

    public function createMail($name, $id, $mailbox, $pass)
    {
        $client = new Client($this->_options['ip']);
        $client->setCredentials($this->_options['username'], $this->_options['password']);

        $response = $client->mail()->create($name, $id, $mailbox, $pass);

        return $response;
    }

    public function getMailAccounts($id)
    {
        $client = new PleskClient($this->_options['ip']);
        $client->setCredentials($this->_options['username'], $this->_options['password']);
        $response = $client->mail()->get($id);
        $accounts = [];
        for ($i = 0; $i < count($response); $i++) {
            $temp['id'] = $response[$i]->id;
            $temp['name'] = $response[$i]->name;
            $accounts[] = $temp;
        }

        return $accounts;
    }

    public function viewDomain($domain_id)
    {
        $client = new PleskClient($this->_options['ip']);
        $client->setCredentials($this->_options['username'], $this->_options['password']);

        $field = null;
        $value = null;
        $d = $client->server()->getDomainById($domain_id);
        dd($d);
        $temp = [];
        $temp['id'] = $d['id'];
        $temp['name'] = $d['filter-id'];

        return $temp;
    }

    public function deleteMailAccount($site_id, $name)
    {
        $client = new Client($this->_options['ip']);
        $client->setCredentials($this->_options['username'], $this->_options['password']);
        $response = $client->mail()->delete('name', $name, $site_id);

        return $response;
    }

    public function changePassword($site_id, $name, $password)
    {
        $client = new PleskClient($this->_options['ip']);
        $client->setCredentials($this->_options['username'], $this->_options['password']);

        $response = $client->mail()->changePassword($site_id, $name, $password);

        return $response;
    }
}
