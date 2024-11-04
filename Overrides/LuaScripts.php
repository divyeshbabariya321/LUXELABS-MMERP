<?php

namespace Overrides;

class LuaScripts extends \Laravel\Horizon\LuaScripts
{
    /**
     * Get the Lua script for purging recent and pending jobs off of the queue.
     *
     * KEYS[1] - The name of the recent jobs sorted set
     * KEYS[2] - The name of the pending jobs sorted set
     * ARGV[1] - The prefix of the Horizon keys
     * ARGV[2] - The name of the queue to purge
     *
     * @return string
     */
    public static function purge()
    {
        return <<<'LUA'
            
            local count = 0
            local cursor = 0
            
            repeat
                -- Iterate over the recent jobs sorted set
                local scanner = redis.call('zscan', KEYS[1], cursor)
                cursor = scanner[1]
                for i = 1, #scanner[2], 2 do
                    local jobid = scanner[2][i]
                    local hashkey = ARGV[1] .. jobid
                    local job = redis.call('hmget', hashkey, 'status', 'queue')
                    -- Delete the pending/reserved jobs, that match the queue
                    -- name, from the sorted sets as well as the job hash
                    if((job[1] == 'reserved' or job[1] == 'pending') and job[2] == ARGV[2]) then
                        redis.call('zrem', KEYS[1], jobid)
                        redis.call('zrem', KEYS[2], jobid)
                        redis.call('del', hashkey)
                        count = count + 1
                    end           
                end
            until cursor == '0'
            return count
LUA;
    }

    /**
     * Get the Lua script for removing all jobs from the queue.
     *
     * KEYS[1] - The name of the primary queue
     * KEYS[2] - The name of the "delayed" queue
     * KEYS[3] - The name of the "reserved" queue
     * KEYS[4] - The name of the "notify" queue
     *
     * @return string
     */
    public static function clear()
    {
        return <<<'LUA'
    local size = redis.call('llen', KEYS[1]) + redis.call('zcard', KEYS[2]) + redis.call('zcard', KEYS[3])
        redis.call('del', KEYS[1], KEYS[2], KEYS[3], KEYS[4])
    return size
LUA;
    }
}
