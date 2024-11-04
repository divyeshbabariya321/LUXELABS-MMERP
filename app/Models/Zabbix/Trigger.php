<?php

declare(strict_types=1);

namespace App\Models\Zabbix;

use App\Zabbix\Zabbix;
use JsonSerializable;

class Trigger implements JsonSerializable
{
    /**
     * @var Zabbix
     */
    private $zabbix;

    private $id;

    private $name;

    private $event_name;

    private $severity;

    private $expression;

    private $templateId;

    private $is_active;

    public function __construct()
    {
        $this->zabbix = new Zabbix;
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

    public function getEventName(): ?string
    {
        return (string) $this->event_name;
    }

    public function setEventName(string $eventName): self
    {
        $this->event_name = $eventName;

        return $this;
    }

    public function getDelay(): ?string
    {
        return (string) $this->delay;
    }

    public function setSeverity(string $severity): self
    {
        $this->severity = $severity;

        return $this;
    }

    public function getSeverity(): ?string
    {
        return (string) $this->severity;
    }

    public function setExpression(?string $expression): self
    {
        $this->expression = $expression;

        return $this;
    }

    public function getExpression(): ?string
    {
        return (string) $this->expression;
    }

    public function isActive(): bool
    {
        return (bool) $this->is_active;
    }

    public function setIsActive(bool $active): self
    {
        $this->is_active = ! $active;

        return $this;
    }

    public function getTemplateId(): ?int
    {
        return (int) $this->templateId;
    }

    public function setTemplateId(int $templateId): self
    {
        $this->templateId = $templateId;

        return $this;
    }

    public function getTemplateName()
    {
        return $this->templateName ?? '';
    }

    public function setTemplateName($tempateName): self
    {
        $this->templateName = $tempateName;

        return $this;
    }

    public function getAll($page = 1)
    {
        return array_map(fn ($item) => (new self)->setData($item), $this->zabbix->getAllTriggers($page));
    }

    public function getAllTemplates(): array
    {
        return $this->zabbix->getAllTemplates();
    }

    public function getById(int $id)
    {
        return (new self)->setData($this->zabbix->getTriggerById($id));
    }

    public function changeStatus($isActive = true)
    {
        $this->zabbix->updateTrigger([
            'triggerid' => $this->getId(),
            'status' => (int) ! $isActive,
        ]);
    }

    public function save(): void
    {
        if (! $this->getId()) {
            $this->zabbix->saveTrigger([
                'description' => $this->getName(),
                'expression' => $this->getExpression(),
                'event_name' => $this->getEventName(),
                'priority' => $this->getSeverity(),
            ]);
        } else {
            $this->zabbix->updateTrigger([
                'triggerid' => $this->getId(),
                'description' => $this->getName(),
                'expression' => $this->getExpression(),
                'event_name' => $this->getEventName(),
                'priority' => $this->getSeverity(),
            ]);
        }
    }

    public function setData(array $data = [])
    {
        $this->setId((int) $data['triggerid'] ?? 0);
        $this->setExpression($data['expression'] ?? '');
        $this->setEventName($data['event_name'] ?? '');
        $this->setName($data['description'] ?? '');
        $this->setTemplateId((int) $data['templateid'] ?? 0);
        $this->setIsActive((bool) $data['status'] ?? true);
        $this->setSeverity($data['priority'] ?? 0);

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function jsonSerialize()
    {
        return [
            'id' => $this->getId(),
            'name' => $this->getName(),
            'expression' => $this->getExpression(),
            'event_name' => $this->getEventName(),
            'template_id' => $this->getTemplateId(),
            'is_active' => $this->isActive(),
            'priority' => $this->getSeverity(),
            'template_name' => $this->getTemplateName(),
        ];
    }
}
