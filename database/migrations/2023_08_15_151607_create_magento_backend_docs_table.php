<?php

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
        Schema::create('magento_backend_docs', function (Blueprint $table) {
            $table->id();
            $table->integer('site_development_category_id');
            $table->integer('post_man_api_id');
            $table->integer('mageneto_module_id');
            $table->text('features');
            $table->text('bug');
            $table->text('bug_details');
            $table->text('bug_resolution');
            $table->text('api_remark')->nullable();
            $table->text('description')->nullable();
            $table->text('google_drive_file_id')->nullable();
            $table->text('description_file_name')->nullable();
            $table->text('description_extension')->nullable();
            $table->text('admin_configuration')->nullable();
            $table->text('admin_configuration_file_name')->nullable();
            $table->text('admin_configuration_extension')->nullable();
            $table->integer('updated_by');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('magento_backend_docs');
    }
};
