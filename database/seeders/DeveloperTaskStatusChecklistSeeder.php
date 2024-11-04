<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\DeveloperTaskStatusChecklist;

class DeveloperTaskStatusChecklistSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $checklist = [
            [
                'task_status' => 'User Complete',
                'name'        => 'Validate the migrations',
            ],
            [
                'task_status' => 'User Complete',
                'name'        => 'Code optimized',
            ],
            [
                'task_status' => 'User Complete',
                'name'        => 'Added menu item',
            ],
            [
                'task_status' => 'User Complete',
                'name'        => 'Recorded the working video',
            ],
            [
                'task_status' => 'User Complete',
                'name'        => 'Added a user guide',
            ],
            [
                'task_status' => 'User Complete',
                'name'        => 'Added a code guide',
            ],
            [
                'task_status' => 'User Complete',
                'name'        => 'Check the coding standards',
            ],
            [
                'task_status' => 'User Complete',
                'name'        => 'PR Link',
            ],
        ];
        foreach ($checklist as $list) {
            DeveloperTaskStatusChecklist::create($list);
        }
    }
}
