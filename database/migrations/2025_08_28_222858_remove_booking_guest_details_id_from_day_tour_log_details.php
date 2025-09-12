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
            $table->dropForeign(['booking_guest_details_id']);
            $table->dropColumn('booking_guest_details_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('day_tour_log_details', function (Blueprint $table) {
            $table->unsignedBigInteger('booking_guest_details_id')->nullable();
            $table->foreign('booking_guest_details_id')->references('id')->on('booking_guest_details');
        });
    }
};
