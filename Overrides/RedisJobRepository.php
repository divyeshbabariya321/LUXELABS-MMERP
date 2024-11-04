<?php

namespace Overrides;

class RedisJobRepository extends \Laravel\Horizon\Repositories\RedisJobRepository
{
    /**
     * Delete pending and reserved jobs for a queue.
     *
     * @param string $queue
     *
     * @return int
     */
    public function purge($queue)
    {
        return $this->connection()->eval(
            LuaScripts::purge(),
            2,
            'recent_jobs',
            'pending_jobs',
            config('horizon.prefix'),
            $queue
        );
    }

    public function clear($queue)
    {
        return $this->connection()->eval(
            LuaScripts::clear(),
            4,
            $queue,
            $queue . ':delayed',
            $queue . ':reserved',
            $queue . ':notify'
        );
    }
}
