<?php

namespace Database\Seeders;

use App\TodoCategory;
use Illuminate\Database\Seeder;

class TodoCategoryTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Load Faker
        $faker = \Faker\Factory::create();

        // Create 1000 customers
        for ($i = 0; $i < 100; $i++) {
            $todoCategory         = new TodoCategory();
            $todoCategory->name   = $faker->name();
            $todoCategory->status = 1;
            $todoCategory->save();
        }
    }
}
