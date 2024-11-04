<?php

namespace Database\Seeders;

use Illuminate\Support\Facades\Log;
use App\StoreWebsite;
use Illuminate\Database\Seeder;
use App\Models\WebsiteStoreProject;

class StoreWebsiteTableProjectUpdateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $websiteProjects = WebsiteStoreProject::all();

        foreach ($websiteProjects as $project) {
            $projectName   = $project->name;
            $projectPrefix = substr($projectName, 0, 3);

            // Find the corresponding websites by searching for the project name in store_websites
            $websites = StoreWebsite::where('title', 'LIKE', $projectPrefix . '%')->get();
            Log::info('projectName ' . $projectName);
            Log::info('projectPrefix ' . $projectPrefix);
            Log::info('websites ' . print_r($websites, true));
            foreach ($websites as $website) {
                // Update the store_websites table with the project id
                $website->website_store_project_id = $project->id;
                $website->save();
            }
        }
    }
}
