<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use App\Models\CouponCodeRulesStatus;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('coupon_code_rules_statuses', function (Blueprint $table) {
            $table->id();
            $table->text('status_name')->nullable();
            $table->text('status_alias')->nullable();
            $table->text('status_color')->nullable();
            $table->timestamps();
        });

        CouponCodeRulesStatus::insert([
            'status_name'  => 'Active',
            'status_alias' => 1,
        ]);

        CouponCodeRulesStatus::insert([
            'status_name'  => 'InActive',
            'status_alias' => 0,
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('coupon_code_rules_statuses');
    }
};
