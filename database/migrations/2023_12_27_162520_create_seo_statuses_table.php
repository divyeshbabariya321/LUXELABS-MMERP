<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use App\Models\SeoStatus;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('seo_statuses', function (Blueprint $table) {
            $table->id();
            $table->text('status_name')->nullable();
            $table->text('status_alias')->nullable();
            $table->text('status_color')->nullable();
            $table->timestamps();
        });

        SeoStatus::insert([
            'status_name'  => 'Planned',
            'status_alias' => 'planned',
        ]);

        SeoStatus::insert([
            'status_name'  => 'Admin Approved',
            'status_alias' => 'admin_approve',
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('seo_statuses');
    }
};
