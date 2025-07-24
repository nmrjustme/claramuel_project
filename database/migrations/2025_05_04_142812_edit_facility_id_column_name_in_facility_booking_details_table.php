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
        Schema::table('facility_booking_details', function (Blueprint $table) {
            $table->dropForeign(['facility_id']); 
        });
        Schema::table('facility_booking_details', function (Blueprint $table) {
            $table->renameColumn('facility_id', 'facility_summary_id');
        });
        Schema::table('facility_booking_details', function (Blueprint $table) {
            $table->foreign('facility_summary_id')->references('id')->on('facility_summary')->onDelete('cascade');
        });
            
            
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // First: drop new foreign key
        Schema::table('facility_booking_details', function (Blueprint $table) {
            $table->dropForeign(['facility_summary_id']);
        });

        // Second: rename column back
        Schema::table('facility_booking_details', function (Blueprint $table) {
            $table->renameColumn('facility_summary_id', 'facility_id');
        });

        // Third: restore old foreign key
        Schema::table('facility_booking_details', function (Blueprint $table) {
            $table->foreign('facility_id')->references('id')->on('facilities')->onDelete('cascade');
        });
    }
};
