<?php

declare(strict_types=1);

namespace App\Models\Zabbix;

use JsonSerializable;
use App\Zabbix\Zabbix;

class User implements JsonSerializable
{
    /**
     * @var Zabbix
     */
    private $zabbix;

    private $id;

    private $name;

    private $surname;

    private $roleId;

    private $url;

    private $timezone;

    private $autologin;

    private $username;

    private $password;

    public function __construct()
    {
        $this->zabbix = new Zabbix();
    }

    /**
     * @return User[]
     */
    public function getAllUsers(): array
    {
        return array_map(function ($item) {
            $model = new self();
            $model->setName($item['name'] ?? '');
            $model->setUsername($item['username'] ?? '');
            $model->setId((int) $item['userid'] ?? null);
            $model->setSurname($item['surname'] ?? '');
            $model->setRoleId((int) $item['roleid'] ?? null);
            $model->setUrl((string) $item['url'] ?? '');
            $model->setTimezone((string) $item['timezone'] ?? '');
            $model->setAutologin((int) $item['autologin'] ?? 0);

            return $model;
        }, $this->zabbix->getAllUsers());
    }

    public function getId(): ?int
    {
        return (int) $this->id;
    }

    public function setId(?int $id): self
    {
        $this->id = $id;

        return $this;
    }

    public function getName(): ?string
    {
        return (string) $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getUsername(): ?string
    {
        return (string) $this->username;
    }

    public function setUsername(string $username): self
    {
        $this->username = $username;

        return $this;
    }

    public function getSurname(): ?string
    {
        return (string) $this->surname;
    }

    public function setSurname(string $surname): self
    {
        $this->surname = $surname;

        return $this;
    }

    public function getRoleId(): ?int
    {
        return (int) $this->roleId;
    }

    public function setRoleId(?int $roleId): self
    {
        $this->roleId = $roleId;

        return $this;
    }

    public function getUrl(): ?string
    {
        return (string) $this->url;
    }

    public function setUrl(string $url): self
    {
        $this->url = $url;

        return $this;
    }

    public function getTimezone(): ?string
    {
        return (string) $this->timezone;
    }

    public function setTimezone(string $timezone): self
    {
        $this->timezone = $timezone;

        return $this;
    }

    public function getPassword(): ?string
    {
        return (string) $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    public function getAutologin(): ?int
    {
        return (int) $this->autologin;
    }

    public function setAutologin(?int $autologin): self
    {
        $this->autologin = $autologin;

        return $this;
    }

    public function save()
    {
        if (! $this->getId()) {
            $this->zabbix->saveUser([
                'username' => $this->getUsername(),
                'name'     => $this->getName(),
                'surname'  => $this->getSurname(),
                'roleid'   => $this->getRoleId(),
                'passwd'   => $this->getPassword(),
                'usrgrps'  => [
                    [
                        'usrgrpid' => 7,
                    ],
                ],
            ]);

            return $this->getById(1);
        } else {
            $this->zabbix->updateUser([
                'name'    => $this->getName(),
                'surname' => $this->getSurname(),
                'roleid'  => $this->getRoleId(),
                'userid'  => $this->getId(),
            ]);

            return $this->getById($this->getId());
        }
    }

    public function getById(int $id): ?self
    {
        $user = $this->zabbix->getUserByIds($id);

        if (! $user) {
            return null;
        }

        return (new self())->setData($user);
    }

    public function getRoleById($id): array
    {
        return $this->zabbix->getRoleByIds($id);
    }

    public function getAllRoles()
    {
        return $this->zabbix->getAllUserRoles();
    }

    public function setData(array $data = [])
    {
        $this->setName($data['name'] ?? '');
        $this->setUsername($data['username'] ?? '');
        $this->setId((int) $data['userid'] ?? null);
        $this->setSurname($data['surname'] ?? '');
        $this->setRoleId((int) $data['roleid'] ?? null);
        $this->setUrl((string) $data['url'] ?? '');
        $this->setTimezone((string) $data['timezone'] ?? '');
        $this->setAutologin((int) $data['autologin'] ?? 0);

        return $this;
    }

    public function delete(): ?int
    {
        $this->zabbix->deleteUser($this->getId());

        return $this->getId();
    }

    public function saveRole(array $params = [])
    {
        if (! empty($params['roleid'])) {
            $this->zabbix->saveRole($params, 'update');
        } else {
            $this->zabbix->saveRole($params);
        }
    }

    /**
     * {@inheritDoc}
     */
    public function jsonSerialize()
    {
        return [
            'id'        => $this->getId(),
            'name'      => $this->getName(),
            'surname'   => $this->getSurname(),
            'username'  => $this->getUsername(),
            'url'       => $this->getUrl(),
            'role_id'   => $this->getRoleId(),
            'autologin' => $this->getAutologin(),
            'timezone'  => $this->getTimezone(),
        ];
    }
}
