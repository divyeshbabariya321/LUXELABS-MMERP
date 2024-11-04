<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Route;

class CheckDuplicateRoutes extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'route:check-duplicates';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check for duplicate routes and show route details with file and line number';

    /**
     * Create a new command instance.
     *
     * @return void
     */

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $routes = Route::getRoutes()->getRoutes();

        $routeDetails = [];

        foreach ($routes as $route) {
            $name = $route->getName();
            $uri = $route->uri(); // Get the route URI
            $methods = implode(',', $route->methods()); // Get the route methods (GET, POST, etc.)
            $action = $route->getActionName(); // Get the controller/action

            $file = 'N/A';
            $line = 'N/A';

            // Check if the route has a controller action
            if (isset($route->getAction()['controller'])) {
                $controllerAction = $route->getAction()['controller'];

                // Split controller and method (e.g., App\Http\Controllers\MessageController@index)
                if (strpos($controllerAction, '@') !== false) {
                    [$controller, $method] = explode('@', $controllerAction);

                    try {
                        if (method_exists($controller, $method)) {
                            // Use Reflection to find file and line of the controller method
                            $reflector = new \ReflectionMethod($controller, $method);
                            $file = $reflector->getFileName();
                            $line = $reflector->getStartLine();
                        } else {
                            $file = "Method {$controller}::{$method} does not exist.";
                            $line = '';
                        }
                    } catch (\ReflectionException $e) {
                        // Handle reflection error if the controller/method doesn't exist
                        $file = 'Reflection error: '.$e->getMessage();
                        $line = '';
                    }
                } else {
                    $file = "Invalid controller-action format: {$controllerAction}";
                    $line = '';
                }
            }

            // If the route name exists, append the details
            if ($name) {
                if (! isset($routeDetails[$name])) {
                    $routeDetails[$name] = [
                        'uri' => $uri,
                        'methods' => $methods,
                        'action' => $action,
                        'files' => [],
                    ];
                }
                // Append file and line information
                $routeDetails[$name]['files'][] = [
                    'file' => $file,
                    'line' => $line,
                ];
            }
        }

        // Prepare to check for duplicates
        $duplicateRoutes = array_filter($routeDetails, function ($details) {
            return count($details['files']) > 1; // Check if there are multiple entries
        });

        if (empty($duplicateRoutes)) {
            $this->info('No duplicate routes found.');

            return 0; // Return 0 to indicate success
        } else {
            $this->error('Duplicate routes found:');
            $i = 1;
            foreach ($duplicateRoutes as $name => $route) {
                $this->line("--------- ({$i}) Route: {$name} ---------");
                $this->line("URI: {$route['uri']}");
                $this->line("Methods: {$route['methods']}");
                $this->line("Action: {$route['action']}");
                foreach ($route['files'] as $fileInfo) {
                    $this->line("File: {$fileInfo['file']}");
                    $this->line("Line: {$fileInfo['line']}");
                }
                $i++;
            }

            return 1; // Return 1 to indicate an error (duplicates found)
        }
    }
}
