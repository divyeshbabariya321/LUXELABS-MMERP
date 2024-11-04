<?php

declare(strict_types=1);

namespace App\Elasticsearch\Reindex;

use Illuminate\Support\Facades\Log;
use Throwable;
use App\Models\IndexerState;
use App\Elasticsearch\Elasticsearch;
use App\Elasticsearch\Reindex\Interfaces\Reindex as ReindexInterface;
use Exception;

class Reindex
{
    public function execute(): void
    {
        $indexerState = IndexerState::all();

        /** @var IndexerState $indexer */
        foreach ($indexerState as $indexer) {
            try {
                // skip running indexes
                if ($indexer->isSkip()) {
                    continue;
                }

                $indexer->setLogs();
                $indexer->addLog('Reindex started.');

                try {
                    $pId                   = getmypid();
                    $settings              = $indexer->getSettings() ?? [];
                    $settings['processId'] = $pId;
                    $indexer->setSettings($settings);
                } catch (Exception $e) {
                    Log::error('Reindex pId error: ' . $e->getMessage());
                }

                $indexer->setStatus(ReindexInterface::RUNNING);
                $indexer->save();

                $this->createIndexIfNotExist($indexer->getIndex());
                $this->removeAll($indexer->getIndex());
                $indexer->addLog(sprintf('Removed all records in index: %s.', $indexer->getIndex()));

                $className = $indexer->getClassName();

                /** @var ReindexInterface $class */
                $class = new $className();
                $class->setIndexerState($indexer);
                $class->execute();

                $indexer->setStatus(ReindexInterface::VALID);
                $indexer->setProcessId(null);
                $indexer->save();

                $indexer->addLog('Reindex finished.');
            } catch (Throwable $throwable) {
                $indexer->setStatus(ReindexInterface::INVALIDATE);
                $indexer->save();

                $indexer->addLog('Reindex error: ' . $throwable->getMessage());

                Log::error('Reindex error: ' . $throwable->getMessage() . ' trace: ' . json_encode($throwable->getTrace()));
            }
        }
    }

    private function removeAll(string $index): void
    {
        Elasticsearch::deleteByQuery([
            'index' => $index,
            'body'  => [
                'query' => [
                    'match_all' => (object) [],
                ],
            ]]);
    }

    public function createIndexIfNotExist(string $index): void
    {
        $indexParams = [
            'index' => $index,
        ];
        if (! Elasticsearch::indices()->exists($indexParams)) {
            Elasticsearch::indices()->create($indexParams);
        }
    }
}
