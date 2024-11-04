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
        Schema::table('store_websites', function (Blueprint $table) {
            $table->string('aws_region')->nullable();
            $table->string('aws_cluster')->nullable();
            $table->string('aws_ecs_service_id')->nullable();
            $table->string('aws_api_key')->nullable();
            $table->string('aws_api_secret', 3000)->nullable();
            $table->text('aws_token')->nullable();
            $table->string('aws_document_name')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('store_websites', function (Blueprint $table) {
            $table->dropColumn('aws_region');
            $table->dropColumn('aws_cluster');
            $table->dropColumn('aws_ecs_service_id');
            $table->dropColumn('aws_api_key');
            $table->dropColumn('aws_api_secret', 3000);
            $table->dropColumn('aws_token');
            $table->dropColumn('aws_document_name');
        });
    }
};
