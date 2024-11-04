<?php

namespace Database\Seeders;

use App\User;
use App\TodoList;
use Carbon\Carbon;
use App\TodoCategory;
use Illuminate\Database\Seeder;

class TodoListTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Load Faker
        $faker = \Faker\Factory::create();

        $userIds = User::get()->pluck('id')->toArray();

        $todoCategoryIds = TodoCategory::get()->pluck('id')->toArray();

        if (! empty($userIds)) {
            // Create 1000 contacts
            for ($i = 0; $i < 5000; $i++) {
                $todoList                   = new TodoList();
                $todoList->user_id          = $userIds[array_rand($userIds, 1)];
                $todoList->title            = $faker->jobTitle();
                $todoList->subject          = $faker->name();
                $todoList->status           = 'Active';
                $todoList->todo_date        = Carbon::parse()->format('Y-m-d');
                $todoList->remark           = '-';
                $todoList->todo_category_id = $todoCategoryIds[array_rand($todoCategoryIds, 1)];
                $todoList->save();
            }
        }
    }
}
