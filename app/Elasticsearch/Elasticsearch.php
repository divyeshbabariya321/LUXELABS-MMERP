<?php

declare(strict_types=1);

namespace App\Elasticsearch;

use Elastic\Elasticsearch\Client;
use Elastic\Elasticsearch\ClientBuilder;
use Exception;
use GuzzleHttp\RequestOptions;
use Throwable;

class Elasticsearch
{
    const INDEXES = [
        'messages',
    ];

    protected Client $connection;

    /**
     * @throws \Elastic\Elasticsearch\Exception\AuthenticationException
     */
    public function __construct()
    {
        $this->connection = $this->connect();
    }

    public function getConn(): Client
    {
        $this->createNonExistsIndexes();

        return $this->connection;
    }

    public static function __callStatic(string $name, array $arguments)
    {
        try {
            return (new self)->getConn()->$name(...$arguments);
        } catch (Throwable $e) {
            return response()->json(['status' => 400, 'message' => $e->getMessage()]);
        }
    }

    public function indexExist(int|string $index): bool
    {
        try {
            return $this->connection->indices()->exists(['index' => $index])->asBool();
        } catch (Exception $e) {
            return false;
        }

        return false;
    }

    /**
     * @return \Elastic\Elasticsearch\Response\Elasticsearch|false|\Http\Promise\Promise
     */
    public function createIndex(int|string $index)
    {
        try {
            return $this->connection->index([
                'index' => $index,
            ]);
        } catch (Exception $e) {
            return false;
        }
    }

    public function createNonExistsIndexes(array $indexes = []): array
    {
        $result = [];
        foreach (! $indexes ? self::INDEXES : $indexes as $index) {
            if (! $this->indexExist($index)) {
                $this->createIndex($index);
                $result[] = $indexes;
            }
        }

        return $result;
    }

    public function count(int|string $index): int
    {
        try {
            $count = $this->connection->count(['index' => $index]);
            if (! $count) {
                return 0;
            }

            return (int) $count['count'];
        } catch (Throwable $e) {
            return 0;
        }
    }

    public function connect()
    {
        $hosts = explode(',', config('settings.elasticsearch_host'));

        return ClientBuilder::create()
            ->setHttpClientOptions([
                RequestOptions::CONNECT_TIMEOUT => 5,
            ])
            ->setHosts(
                $hosts ?? [$_ENV['ELASTICSEARCH_HOST'] ?? 'elasticsearch:9200']
            )
            ->build();
    }
}
