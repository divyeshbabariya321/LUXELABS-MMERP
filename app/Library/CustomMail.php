<?php

namespace App\Library;
use App\Library;
use App\EmailAddress;

use IWasHereFirst2\LaravelMultiMail\MailSettings;

class CustomMail implements MailSettings
{
    public $config;

    public $setting = [];

    public $provider = [];

    public function initialize($key)
    {
        // $this->config = EmailAddress::where('from_address', $key)->where('from_address', 'not like', '%theluxuryunlimited.com%')->first();
        $this->config = EmailAddress::where('from_address', $key)->first();
    
        if ($this->config) {
            $this->provider = [
                'host'       => $this->config->host,
                'port'       => $this->config->port,
                'encryption' => $this->config->encryption,
            ];

            $this->setting = [
                'pass'          => $this->config->password,
                'username'      => $this->config->username,
                'from_name'     => $this->config->from_name,
                'from'          => $this->config->from_address,
                'reply_to_mail' => $this->config->from_address,
            ];

            if (! empty($this->config->send_grid_token)) {
                $this->provider['host']       = config('env.MAIL_HOST');
                $this->provider['port']       = config('env.MAIL_PORT');
                $this->provider['encryption'] = config('env.MAIL_ENCRYPTION');
                $this->setting['pass']        = $this->config->send_grid_token;
                $this->setting['username']    = 'apikey';
            }
        } else {
            $this->provider = [
                'host'       => config('env.MAIL_HOST'),
                'port'       => config('env.MAIL_PORT'),
                'encryption' => config('env.MAIL_ENCRYPTION'),
            ];

            $this->setting = [
                'pass'          => config('env.MAIL_PASSWORD'),
                'username'      => config('env.MAIL_USERNAME'),
                'from_name'     => config('env.MAIL_FROM_NAME'),
                'from'          => config('env.MAIL_FROM_ADDRESS'),
                'reply_to_mail' => config('env.MAIL_FROM_ADDRESS'),
            ];
        }

        if (empty($this->name)) {
            $this->name = $this->settings['from_name'] ?? null;
        }

        return $this;
    }

    /**
     * Check if log driver is currently used.
     */
    public function isLogDriver(): bool
    {
        return false;
    }

    /**
     * Get provider.
     */
    public function getProvider(): array
    {
        return $this->provider;
    }

    /**
     * Get setting.
     */
    public function getSetting(): array
    {
        return $this->setting;
    }

    /**
     * Return email of sender.
     */
    public function getFromEmail(): string
    {
        return ($this->setting && $this->setting['from']) ? $this->setting['from'] : null;
    }

    /**
     * Return name of sender.
     */
    public function getFromName(): string
    {
        return ($this->setting && $this->setting['from_name']) ? $this->setting['from_name'] : null;
    }

    /**
     * Return email of sender.
     */
    public function getReplyEmail(): string
    {
        return ($this->setting && $this->setting['reply_to_mail']) ? $this->setting['reply_to_mail'] : null;
    }

    /**
     * Return name of sender.
     */
    public function getReplyName(): string
    {
        return ($this->setting && $this->setting['from_name']) ? $this->setting['from_name'] : null;
    }

    /**
     * Return email
     */
    public function getEmail(): string
    {
        return ($this->setting && $this->setting['from']) ? $this->setting['from'] : null;
    }
}
