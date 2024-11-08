<?php

namespace App\Library\Watson;

use App\Library\Watson\Interfaces\ServiceInterface;

class Service implements ServiceInterface
{
    /**
     * Base url for the service
     *
     * @var string
     */
    protected $url;

    /**
     * API version
     *
     * @var string
     */
    protected $version;

    /**
     * Api service username
     *
     * @var string
     */
    protected $username;

    /**
     * Api service password
     *
     * @var string
     */
    protected $password;

    /**
     * Sdk client
     *
     * @var Client
     */
    protected $client;

    /**
     * The base service constructor
     *
     * @param $username string The service api username
     * @param $password string The service api password
     */
    public function __construct($username = null, $password = null)
    {
        $this->setUsername($username);
        $this->setPassword($password);

        $this->client = new Client($this->getUsername(), $this->getPassword());
    }

    /**
     * {@inheritdoc}
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * {@inheritdoc}
     */
    public function getMountedUrl()
    {
        $url     = $this->normalizeUrlEndBar($this->getUrl());
        $version = $this->normalizeUrlEndBar($this->getVersion());

        return $url . $version;
    }

    /**
     * {@inheritdoc}
     */
    public function setUrl($url)
    {
        $this->url = $url;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getVersion()
    {
        return $this->version;
    }

    /**
     * {@inheritdoc}
     */
    public function setVersion($version)
    {
        $this->version = $version;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getClient()
    {
        return $this->client;
    }

    /**
     * Normalize an url string
     *
     * If not end with '/', add then
     *
     * @param mixed $string
     */
    protected function normalizeUrlEndBar($string): string
    {
        return (substr($string, -1) != '/') ? $string . '/' : $string;
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password)
    {
        $this->password = $password;
    }

    public function getUsername(): string
    {
        return $this->username;
    }

    public function setUsername(string $username)
    {
        $this->username = $username;
    }
}
