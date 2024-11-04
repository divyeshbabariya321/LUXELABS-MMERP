<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class ScanFolderNew extends Command
{
    protected $signature = 'scan:folderNew {viewPath?}';

    protected $description = 'Command description';

    public function handle()
    {
        $relativeViewPath = $this->argument('viewPath') ?? 'resources/views';
        $viewPath = base_path($relativeViewPath);
        $cssPaths = [];
        $cssFileCount = 0;
        $jsPaths = [];
        $jsFileCount = 0;
        $newCssMixLines = [];
        $newJsMixLines = [];

        // Check if the provided path is a directory
        if (! is_dir($viewPath)) {
            $this->error("The provided view path is not a directory: $viewPath");

            return;
        }

        // Define the search pattern to find all occurrences
        $pattern = '/<link\s+(?:rel="stylesheet"\s+)?href="((?:(?!https?:\/\/)[^"]+))"\s*(?:rel="stylesheet"\s*)?\s*>/';
        $jsSearchPattern = '/<script\s+(?:type="text\/javascript"\s+)?src="((?:(?!https?:\/\/)[^"]+))"\s*(?:type="text\/javascript"\s*)?\s*><\/script>/';
        $cssSearchPattern = $pattern;

        // Get all files in the view directory
        $viewFiles = File::allFiles($viewPath);

        foreach ($viewFiles as $file) {
            $content = File::get($file->getPathname());
            $fileUpdated = false;

            // Process CSS paths
            if (preg_match_all($cssSearchPattern, $content, $matches)) {
                foreach ($matches[0] as $key => $match) {
                    $matches[1][$key] = str_replace(' ', '', $matches[1][$key]);
                    $matches[1][$key] = str_replace("{{env('APP_URL')}}", '', $matches[1][$key]);
                    $matches[1][$key] = str_replace("?v={{config('pilot.version')", '', $matches[1][$key]);

                    if (strpos($matches[1][$key], '{{URL::asset') !== false) {
                        $matches[1][$key] = str_replace(['URL::'], '', $matches[1][$key]);
                    }

                    if (strpos($matches[1][$key], '{{asset') !== false) {
                        $matches[1][$key] = str_replace(["{{asset('", "')}}"], '', $matches[1][$key]);
                    }

                    if (strpos($matches[1][$key], '{{url') !== false) {
                        $matches[1][$key] = str_replace(["{{url('", "')}}"], '', $matches[1][$key]);
                    }

                    if (substr($matches[1][$key], 0, 1) === '/') {
                        $matches[1][$key] = substr($matches[1][$key], 1);
                    }

                    $replacement = '<link rel="stylesheet" href="{{ mix(\'webpack-dist/'.$matches[1][$key].'\') }} ">';

                    $content = str_replace($match, $replacement, $content);
                    $fileUpdated = true;
                    $cssPaths[] = $matches[1][$key];
                }
                $cssFileCount++;
            }

            // Process JS paths
            if (preg_match_all($jsSearchPattern, $content, $matches)) {
                foreach ($matches[0] as $key => $match) {
                    $matches[1][$key] = str_replace(' ', '', $matches[1][$key]);
                    $matches[1][$key] = str_replace("{{env('APP_URL')}}", '', $matches[1][$key]);
                    $matches[1][$key] = str_replace("?v={{config('pilot.version')", '', $matches[1][$key]);

                    if (strpos($matches[1][$key], '{{URL::asset') !== false) {
                        $matches[1][$key] = str_replace(['URL::'], '', $matches[1][$key]);
                    }

                    if (strpos($matches[1][$key], '{{asset') !== false) {
                        $matches[1][$key] = str_replace(["{{asset('", "')}}"], '', $matches[1][$key]);
                    }

                    if (strpos($matches[1][$key], '{{url') !== false) {
                        $matches[1][$key] = str_replace(["{{url('", "')}}"], '', $matches[1][$key]);
                    }

                    if (substr($matches[1][$key], 0, 1) === '/') {
                        $matches[1][$key] = substr($matches[1][$key], 1);
                    }
                    $replacement = '<script type="text/javascript" src="{{ mix(\'webpack-dist/'.$matches[1][$key].'\') }} "></script>';

                    $content = str_replace($match, $replacement, $content);
                    $fileUpdated = true;
                    $jsPaths[] = $matches[1][$key];
                }
                $jsFileCount++;
            }

            // Update the file if changes were made
            if ($fileUpdated) {
                File::put($file->getPathname(), $content);
                $this->info("Updated file: {$file->getRelativePathname()}");
            } else {
                $this->info("No matches found in file: {$file->getRelativePathname()}");
            }
        }

        $paths = [
            'css' => array_values(array_unique($cssPaths)),
            'js' => array_values(array_unique($jsPaths)),
        ];

        file_put_contents('paths.json', json_encode($paths));

        $this->info("css file count: {$cssFileCount}");
        $this->info("js file count: {$jsFileCount}");

        $paths = json_decode(file_get_contents('paths.json'), true);

        $webpackFilePath = 'path/webpack.mix.js';
        $webpackFileContent = file_get_contents($webpackFilePath);
        $webpackFileContent .= "const mix = require('laravel-mix');
                                mix.webpackConfig({
                                    module: {
                                        rules: [
                                            {
                                                test: /\.(png|jpe?g|gif|svg)$/i,
                                                use: [
                                                    {
                                                        loader: 'file-loader',
                                                        options: {
                                                            name: '[path][name].[ext]',
                                                            context: 'public/images',
                                                            outputPath: 'public/webpack-dist/images',
                                                        },
                                                    }
                                                ],
                                            },
                                        ],
                                    },
                                });\n";

        foreach ($paths['css'] as $path) {
            $lineToAdd = "mix.css('public/$path', 'webpack-dist/$path');\n";
            if (strpos($webpackFileContent, $lineToAdd) === false) {
                $newCssMixLines[] = $lineToAdd;
            }
        }

        foreach ($paths['js'] as $path) {
            $lineToAdd = "mix.js('public/$path', 'webpack-dist/$path');\n";
            if (strpos($webpackFileContent, $lineToAdd) === false) {
                $newJsMixLines[] = $lineToAdd;
            }
        }

        if (! empty($newCssMixLines) || ! empty($newJsMixLines)) {
            $webpackFileContent .= "\n".implode('', $newCssMixLines).implode('', $newJsMixLines);
            file_put_contents($webpackFilePath, $webpackFileContent);
            echo "webpack.mix.js has been updated with the new paths.\n";
        } else {
            echo "No new paths to add.\n";
        }

    }
}
