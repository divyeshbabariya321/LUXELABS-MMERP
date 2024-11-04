<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Seo\SeoProcessStatus;

class SeoProcessStatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $dataArr = [
            // Seo Status
            [
                'type'  => 'seo_approval',
                'label' => 'Check Keywords',
            ],
            [
                'type'  => 'seo_approval',
                'label' => 'Keywords Density',
            ],
            [
                'type'  => 'seo_approval',
                'label' => 'Readability',
            ],
            [
                'type'  => 'seo_approval',
                'label' => 'Add internal and external linking',
            ],
            [
                'type'  => 'seo_approval',
                'label' => 'Check plagiarism',
            ],
            [
                'type'  => 'seo_approval',
                'label' => 'Send revision request to content team if require',
            ],
            [
                'type'  => 'seo_approval',
                'label' => 'Sent to development team after verification',
            ],

            // Publish status
            [
                'type'  => 'publish',
                'label' => 'Upload in content',
            ],
            [
                'type'  => 'publish',
                'label' => 'Check links',
            ],
            [
                'type'  => 'publish',
                'label' => 'Check mobile responsiveness',
            ],
            [
                'type'  => 'publish',
                'label' => 'Analytics tracking',
            ],
            [
                'type'  => 'publish',
                'label' => 'Test page in various browsers',
            ],
        ];

        SeoProcessStatus::insert($dataArr);
    }
}
