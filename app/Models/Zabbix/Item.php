<?php

declare(strict_types=1);

namespace App\Models\Zabbix;

use JsonSerializable;
use App\Zabbix\Zabbix;

class Item implements JsonSerializable
{
    const TYPES = [
        0  => 'Zabbix agent',
        2  => 'Zabbix trapper',
        3  => 'Simple check',
        5  => 'Zabbix agent',
        7  => 'Zabbix internal',
        9  => 'Zabbix agent (active)',
        10 => 'Web item',
        11 => 'External check',
        12 => 'IPMI agent',
        13 => 'SSH agent',
        14 => 'TELNET',
        15 => 'Calculated',
        16 => 'JMX agent',
        17 => 'SNMP trap',
        18 => 'Dependent item',
        19 => 'HTTP agent',
        20 => 'SNMP agent',
        21 => 'Script',
    ];

    const VALUE_TYPES = [
        0 => 'numeric float',
        1 => 'character',
        2 => 'log',
        3 => 'numeric unsigned',
        4 => 'text',
    ];

    /**
     * @var Zabbix
     */
    private $zabbix;

    private $id;

    private $name;

    private $type;

    private $key;

    private $valueType;

    private $interfaceId;

    private $hostId;

    private $units;

    private $delay;

    public function __construct()
    {
        $this->zabbix = new Zabbix();
    }

    /**
     * @return Item[]
     */
    public function getAllItems(): array
    {
        return array_map(function ($item) {
            $model = new self();
            $model->setData($item);

            return $model;
        }, $this->zabbix->getAllItems());
    }

    /**
     * @return Item[]
     */
    public function getItemsByHostId(int $hostId): array
    {
        return array_map(function ($item) {
            $model = new self();
            $model->setData($item);

            return $model;
        }, $this->zabbix->getAllItemsByHostId($hostId));
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

    public function getKey(): ?string
    {
        return (string) $this->key;
    }

    public function setKey(string $key): self
    {
        $this->key = $key;

        return $this;
    }

    public function getDelay(): ?string
    {
        return (string) $this->delay;
    }

    public function setDelay(string $delay): self
    {
        $this->delay = $delay;

        return $this;
    }

    public function getValueType(): ?int
    {
        return (int) $this->valueType;
    }

    public function setValueType(?int $valueType): self
    {
        $this->valueType = $valueType;

        return $this;
    }

    public function getInterfaceid(): ?int
    {
        return (int) $this->interfaceId;
    }

    public function setInterfaceid(?int $interfaceId): self
    {
        $this->interfaceId = $interfaceId;

        return $this;
    }

    public function getHostId(): ?int
    {
        return (int) $this->hostId;
    }

    public function setHostId(?int $hostId): self
    {
        $this->hostId = $hostId;

        return $this;
    }

    public function getType(): ?string
    {
        return (string) $this->type;
    }

    public function setType(string $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function getUnits(): ?string
    {
        return (string) $this->units;
    }

    /**
     * @param $type
     * @param mixed $units
     */
    public function setUnits($units): self
    {
        $this->units = $units;

        return $this;
    }

    public function getTemplateId(): int
    {
        return $this->templateId ?? 0;
    }

    public function setTemplateId(int $templateId): self
    {
        $this->templateId = $templateId;

        return $this;
    }

    public function save()
    {
        if (! $this->getId()) {
            $this->zabbix->saveItem([
                'name'        => $this->getName(),
                'key_'        => $this->getKey(),
                'hostid'      => $this->getHostId(),
                'type'        => $this->getType(),
                'value_type'  => $this->getValueType(),
                'interfaceid' => $this->getInterfaceid(),
                'delay'       => $this->getDelay(),
                'units'       => $this->getUnits(),
                'templateids' => $this->getTemplateId(),
            ]);
        } else {
            $this->zabbix->updateItem([
                'name'        => $this->getName(),
                'key_'        => $this->getKey(),
                'hostid'      => $this->getHostId(),
                'type'        => $this->getType(),
                'value_type'  => $this->getValueType(),
                'interfaceid' => $this->getInterfaceid(),
                'delay'       => $this->getDelay(),
                'units'       => $this->getUnits(),
                'templateids' => $this->getTemplateId(),
            ]);
        }
    }

    public function getById(int $id): ?self
    {
        $item = $this->zabbix->getItemByIds($id);

        if (! $item) {
            return null;
        }

        return (new self())->setData($item);
    }

    public function setData(array $data = [])
    {
        $this->setName($data['name'] ?? '');
        $this->setType($data['type'] ?? '');
        $this->setId((int) $data['itemid'] ?? null);
        $this->setValueType((int) $data['value_type'] ?? 0);
        $this->setKey((string) $data['key_'] ?? '');
        $this->setDelay((string) $data['delay'] ?? '');
        $this->setInterfaceid((int) $data['interfaceid'] ?? 0);
        $this->setHostId((int) $data['hostid'] ?? 0);
        $this->setUnits($data['units'] ?? 0);
        $this->setTemplateId((int) $data['templateid'] ?? 0);

        return $this;
    }

    public function delete(): ?int
    {
        $this->zabbix->deleteItem($this->getId());

        return $this->getId();
    }

    /**
     * {@inheritDoc}
     */
    public function jsonSerialize()
    {
        return [
            'id'          => $this->getId(),
            'name'        => $this->getName(),
            'key'         => $this->getKey(),
            'type'        => $this->getType(),
            'value_type'  => $this->getValueType(),
            'intarfaceid' => $this->getInterfaceid(),
            'delay'       => $this->getDelay(),
            'host_id'     => $this->getHostId(),
            'units'       => $this->getUnits(),
            'templateid'  => $this->getTemplateId(),
        ];
    }
}
