<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class FindTableQueries extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'find:table-queries';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Find all database queries using DB::table in the Laravel project';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $queries = [];

        // Directory to search
        $directory = app_path(); // You can change this to your desired directory

        $this->traverseDirectory($directory, $queries);

        // Export queries to a file
        File::put(storage_path('table_queries1.txt'), implode("\n\n", $queries));

        $this->info('Queries using DB::table exported to table_queries.txt');
    }

    /**
     * Traverse the directory and search for queries using DB::table.
     */
    private function traverseDirectory(string $directory, array &$queries): void
    {
        $files = File::allFiles($directory);

        foreach ($files as $file) {
            $contents = File::get($file->getPathname());

            // Search for queries using DB::table
            if (strpos($contents, 'DB::select') !== false) {
                // Log query
                $queries[] = "File: {$file->getPathname()} \nContent: {$contents}";
            }
        }
    }
}
