<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('google_billing_projects', function (Blueprint $table) {
            $table->id();
            $table->integer('google_billing_master_id');
            $table->string('references');
            $table->string('project_id');
            $table->string('service_type');
            $table->string('dataset_id');
            $table->string('table_id');
            $table->text('description');
            $table->timestamps();
        });
    }
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('google_billing_projects');
    }
};
