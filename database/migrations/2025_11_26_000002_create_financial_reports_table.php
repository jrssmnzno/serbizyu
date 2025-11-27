<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('financial_reports', function (Blueprint $table) {
            $table->id();
            $table->date('report_period_start');
            $table->date('report_period_end');
            $table->decimal('total_gmv', 14, 2)->default(0);
            $table->decimal('total_platform_fees', 14, 2)->default(0);
            $table->decimal('total_seller_earnings', 14, 2)->default(0);
            $table->decimal('total_refunds', 14, 2)->default(0);
            $table->decimal('net_platform_revenue', 14, 2)->default(0);
            $table->enum('status', ['pending', 'completed', 'archived'])->default('pending');
            $table->timestamps();
            
            $table->index('report_period_start');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('financial_reports');
    }
};
