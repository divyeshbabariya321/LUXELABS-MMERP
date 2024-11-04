<?php

declare(strict_types=1);

namespace App\Models;
use App\Models;

use Carbon\Carbon;
use App\Elasticsearch\Reindex\Messages;
use Illuminate\Database\Eloquent\Model;
use App\Elasticsearch\Reindex\Interfaces\Reindex;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class IndexerState extends Model
{
    use HasFactory;

    const ID = 'id';

    const INDEX = 'index';

    const STATUS = 'status';

    const SETTINGS = 'settings';

    const UPDATED_AT = 'updated_at';

    const CREATED_AT = 'created_at';

    const LOGS = 'logs';

    const IDS = 'ids';

    const INDEXER_MAPPING = [
        Messages::INDEX_NAME => Messages::class,
    ];

    protected $table = 'indexer_state';

    public function getId(): ?int
    {
        return (int) $this->getAttribute('id');
    }

    public function getIndex(): ?string
    {
        return (string) $this->getAttribute(self::INDEX);
    }

    public function getStatus(): ?string
    {
        return (string) $this->getAttribute(self::STATUS);
    }

    public function getSettings(): ?array
    {
        return json_decode($this->getAttribute(self::SETTINGS), true);
    }

    public function getLogs(): mixed
    {
        return json_decode($this->getAttribute(self::LOGS), true);
    }

    public function getUpdatedAt(): ?Carbon
    {
        return $this->getAttribute(self::UPDATED_AT);
    }

    public function getCreatedAt(): ?Carbon
    {
        return $this->getAttribute(self::CREATED_AT);
    }

    public function setIndex(string $index): self
    {
        $this->setAttribute(self::INDEX, $index);

        return $this;
    }

    public function setStatus(string $status): self
    {
        $this->setAttribute(self::STATUS, $status);

        return $this;
    }

    public function setSettings(array $settings = []): self
    {
        $this->setAttribute(self::SETTINGS, json_encode($settings));

        return $this;
    }

    public function setLogs(array $logs = []): self
    {
        $this->setAttribute(self::LOGS, json_encode($logs));

        return $this;
    }

    public function addLog(string $log): self
    {
        $logs   = $this->getLogs();
        $logs[] = $log . ' ' . date('Y-m-d H:i:s');

        $this->setLogs($logs);
        parent::save();

        return $this;
    }

    public function setProcessId(?int $pId)
    {
        $settings              = $this->getSettings() ?? [];
        $settings['processId'] = $pId;
        $this->setSettings($settings);
        parent::save();
    }

    public function getProcessId(): ?int
    {
        $settings = $this->getSettings() ?? [];
        $pId      = $settings['processId'] ?? null;

        return $pId ?: (int) $pId;
    }

    public function getIds(): ?array
    {
        $ids = $this->getAttribute(self::IDS);

        if (! $ids) {
            return [];
        }

        $ids = json_decode($ids, true) ?? [];

        return $ids ?: [];
    }

    public function setIds(array $ids): self
    {
        $this->setAttribute(self::IDS, json_encode($ids));

        return $this;
    }

    public function addId($id): self
    {
        $ids   = $this->getIds();
        $ids[] = $id;

        $this->setIds($ids);
        parent::save();

        return $this;
    }

    /**
     * skip if running
     */
    public function isSkip(): bool
    {
        if ($this->getStatus() === Reindex::RUNNING) {
            return true;
        }

        return false;
    }

    public function getClassName(): ?string
    {
        return self::INDEXER_MAPPING[$this->getIndex()];
    }
}
