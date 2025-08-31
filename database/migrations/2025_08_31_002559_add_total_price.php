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
        Schema::table('day_tour_log_details', function (Blueprint $table) {
            $table->decimal('total_price', 10, 2)->nullable()->after('approved_by');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('day_tour_log_details', function (Blueprint $table) {
            $table->dropColumn('total_price');
        });
    }
};
