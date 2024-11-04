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
        Schema::create('scrapper_monitorings', function (Blueprint $table) {
            $table->id();
            $table->integer('task_id')->nullable();
            $table->integer('user_id')->unsigned()->nullable();
            $table->string('scrapper_name')->nullable();
            $table->boolean('need_proxy')->default(1)->nullable();
            $table->boolean('move_to_aws')->default(1)->nullable();
            $table->text('remarks')->nullable();
            $table->timestamps();

            $table->foreign('task_id')->references('id')->on('developer_tasks');
            $table->foreign('user_id')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('scrapper_monitorings', function (Blueprint $table) {
            $table->dropForeign(['task_id']);
            $table->dropForeign(['user_id']);
        });

        Schema::dropIfExists('scrapper_monitorings');
    }
};
