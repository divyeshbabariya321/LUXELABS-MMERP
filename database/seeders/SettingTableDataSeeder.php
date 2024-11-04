<?php

namespace Database\Seeders;

use App\Setting;
use Illuminate\Database\Seeder;

class SettingTableDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        if (! Setting::get('posts_per_day')) {
            Setting::insert([
                'name' => 'posts_per_day',
                'val'  => 1,
            ]);
        }
        if (! Setting::get('send_requests_per_day')) {
            Setting::insert([
                'name' => 'send_requests_per_day',
                'val'  => 1,
            ]);
        }
        if (! Setting::get('likes_per_day')) {
            Setting::insert([
                'name' => 'likes_per_day',
                'val'  => 1,
            ]);
        }
        if (! Setting::get('accept_requests_per_day')) {
            Setting::insert([
                'name' => 'accept_requests_per_day',
                'val'  => 1,
            ]);
        }
        if (! Setting::get('image_per_post')) {
            Setting::insert([
                'name' => 'image_per_post',
                'val'  => 1,
            ]);
        }
    }
}
