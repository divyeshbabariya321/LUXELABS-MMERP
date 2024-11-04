<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use App\Models\SeoCompanyStatus;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('seo_company_statuses', function (Blueprint $table) {
            $table->id();
            $table->text('status_name')->nullable();
            $table->text('status_color')->nullable();
            $table->timestamps();
        });

        SeoCompanyStatus::insert([
            'status_name'  => 'pending',
            'status_color' => '',
        ]);

        SeoCompanyStatus::insert([
            'status_name'  => 'approved',
            'status_color' => '',
        ]);

        SeoCompanyStatus::insert([
            'status_name'  => 'rejected',
            'status_color' => '',
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('seo_company_statuses');
    }
};
