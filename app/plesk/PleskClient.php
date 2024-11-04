<?php

// Copyright 1999-2019. Plesk International GmbH.

namespace App\plesk;
use App\plesk\PleskServer;
use App\plesk\PleskMail;

class PleskClient extends \PleskX\Api\Client
{
    /**
     * @param mixed $host
     * @param mixed $port
     * @param mixed $protocol
     *
     * @return array
     */
    public function __construct($host, $port = 8443, $protocol = 'https')
    {
        $this->_host     = $host;
        $this->_port     = $port;
        $this->_protocol = $protocol;
    }

    public function server()
    {
        $name = 'Server';
        if (! isset($this->_operatorsCache[$name])) {
            $className                    = PleskServer::class;
            $this->_operatorsCache[$name] = new $className($this);
        }

        return $this->_operatorsCache[$name];
    }

    public function mail()
    {
        $name = 'Mail';
        if (! isset($this->_operatorsCache[$name])) {
            $className                    = PleskMail::class;
            $this->_operatorsCache[$name] = new $className($this);
        }

        return $this->_operatorsCache[$name];
    }
}
