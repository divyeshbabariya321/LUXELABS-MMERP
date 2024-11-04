<?php

declare(strict_types=1);

namespace App\Elasticsearch\Reindex\Interfaces;

use App\Models\IndexerState;

interface Reindex
{
    const RUNNING = 'running';

    const INVALIDATE = 'invalidate';

    const VALID = 'valid';

    const PARTIAL_INVALID = 'partial_invalid';

    public function execute(array $params = []): void;

    public function configure(): array;

    public function setIndexerState(IndexerState $indexerState): self;

    public function getIndexerState(): IndexerState;
}
