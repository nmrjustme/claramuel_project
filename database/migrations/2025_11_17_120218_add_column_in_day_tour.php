<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('day_tour_log_details', function (Blueprint $table) {
            $table->decimal('manual_discount_amount', 10, 2)->default(0)->after('total_price');
            $table->string('manual_discount_type', 20)->nullable()->after('manual_discount_amount');
            $table->decimal('manual_discount_value', 10, 2)->default(0)->after('manual_discount_type');
            $table->string('manual_discount_reason', 255)->nullable()->after('manual_discount_value');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('day_tour_log_details', function (Blueprint $table) {
            //
        });
    }
};
