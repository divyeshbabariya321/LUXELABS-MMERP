<?php

use App\Models\ProjectServerenv;
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
        Schema::create('project_serverenvs', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->string('name');
        });

        $currentTime = date('Y-m-d H:i:s');
        ProjectServerenv::insert([
            ['name' => 'brands-prod', 'created_at' => $currentTime, 'updated_at' => $currentTime],
            ['name' => 'brands-stage', 'created_at' => $currentTime, 'updated_at' => $currentTime],
            ['name' => 'qa', 'created_at' => $currentTime, 'updated_at' => $currentTime],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('project_serverenvs');
    }
};
