<?php

use App\MagentoSettingStatus;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('magento_setting_statuses', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->string('name');
            $table->string('color')->nullable();
        });

        // Table magento_settings having only 2 status values, So created those 2 statuses statically
        MagentoSettingStatus::firstOrCreate(['name' => 'Success']);
        MagentoSettingStatus::firstOrCreate(['name' => 'Error']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('magento_setting_statuses');
    }
};
