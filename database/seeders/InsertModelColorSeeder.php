<?php

namespace Database\Seeders;

use Illuminate\Support\Facades\File;
use App\ModelColor;
use Illuminate\Database\Seeder;

class InsertModelColorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $filesInFolder = File::files('app');
        foreach ($filesInFolder as $filesArr) {
            $file = pathinfo($filesArr);
            ModelColor::create(['model_name' => $file['filename'], 'color_code' => '#ffffff']);
        }
    }
}
