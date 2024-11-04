<?php

namespace App\Console\Commands;

use Illuminate\Support\Arr;
use Illuminate\Console\Command;
use Overrides\RedisJobRepository;
use Illuminate\Queue\QueueManager;
use Illuminate\Console\ConfirmableTrait;

class HorizonClear extends Command
{
    use ConfirmableTrait;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ehorizon:clear
                            {--queue= : The name of the queue to clear}
                            {--force : Force the operation to run when in production}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Delete all of the jobs from the specified queue';

    /**
     * Execute the console command.
     */
    public function handle(RedisJobRepository $jobRepository, QueueManager $manager): int
    {
        if (! $this->confirmToProceed()) {
            return 1;
        }

        $connection = Arr::first($this->laravel['config']->get('horizon.defaults'))['connection'] ?? 'redis';

        $jobRepository->purge($queue = $this->getQueue($connection));

        // @todo this clear command is not clearing queues from redis cli.
        $count = $jobRepository->clear($queue);

        $this->line('<info>Cleared ' . $count . ' jobs from the [' . $queue . '] queue</info> ');

        return 0;
    }

    /**
     * Get the queue name to clear.
     */
    protected function getQueue(string $connection): string
    {
        return $this->option('queue') ?: $this->laravel['config']->get(
            "queue.connections.{$connection}.queue",
            'default'
        );
    }
}
