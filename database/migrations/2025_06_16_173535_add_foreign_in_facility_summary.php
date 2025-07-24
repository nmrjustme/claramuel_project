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
        Schema::table('facility_summary', function (Blueprint $table) {
            // First define the column with nullable and after
            $table->unsignedBigInteger('facility_booking_log_id')->nullable()->after('breakfast_id');

            // Then add the foreign key constraint
            $table->foreign('facility_booking_log_id')
                  ->references('id')
                  ->on('facility_booking_log')
                  ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('facility_summary', function (Blueprint $table) {
            $table->dropForeign(['facility_booking_log_id']);
            $table->dropColumn('facility_booking_log_id');
        });
    }
};
