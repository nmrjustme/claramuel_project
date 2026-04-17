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
            $table->unsignedBigInteger('facility_booking_log_id')->nullable()->change();
            $table->unsignedBigInteger('day_tour_log_details_id')->nullable()->after('facility_booking_log_id');

            $table->foreign('day_tour_log_details_id')->references('id')->on('day_tour_log_details')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('facility_summary', function (Blueprint $table) {
            //
        });
    }
};
