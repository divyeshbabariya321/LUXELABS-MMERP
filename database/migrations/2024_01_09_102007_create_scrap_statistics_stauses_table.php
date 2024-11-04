<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use App\Models\ScrapStatisticsStaus;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('scrap_statistics_stauses', function (Blueprint $table) {
            $table->id();
            $table->string('status')->nullable();
            $table->string('status_value')->nullable();
            $table->timestamps();
        });

        ScrapStatisticsStaus::insert([
            'status'       => 'N/A',
            'status_value' => '',
        ]);

        ScrapStatisticsStaus::insert([
            'status'       => 'Ok',
            'status_value' => 'Ok',
        ]);

        ScrapStatisticsStaus::insert([
            'status'       => 'Rework',
            'status_value' => 'Rework',
        ]);

        ScrapStatisticsStaus::insert([
            'status'       => 'In Process',
            'status_value' => 'In Process',
        ]);

        ScrapStatisticsStaus::insert([
            'status'       => 'Scrapper Fixed',
            'status_value' => 'Scrapper Fixed',
        ]);

        ScrapStatisticsStaus::insert([
            'status'       => 'Process Complete',
            'status_value' => 'Process Complete',
        ]);

        ScrapStatisticsStaus::insert([
            'status'       => 'Categories',
            'status_value' => 'Categories',
        ]);

        ScrapStatisticsStaus::insert([
            'status'       => 'Logs Checked',
            'status_value' => 'Logs Checked',
        ]);

        ScrapStatisticsStaus::insert([
            'status'       => 'Scrapper Checked',
            'status_value' => 'Scrapper Checked',
        ]);

        ScrapStatisticsStaus::insert([
            'status'       => 'All brands Scrapped',
            'status_value' => 'All brands Scrapped',
        ]);

        ScrapStatisticsStaus::insert([
            'status'       => 'All Categories Scrapped',
            'status_value' => 'All Categories Scrapped',
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('scrap_statistics_stauses');
    }
};
