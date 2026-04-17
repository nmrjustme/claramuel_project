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
        Schema::create('day_tour_log_details', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('facility_summary_id');
            $table->unsignedBigInteger('booking_guest_details_id');
            $table->string('date_tour', 50);
            $table->unsignedBigInteger('approved_by');
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('facility_summary_id')->references('id')->on('facility_summary')->onDelete('cascade');
            $table->foreign('booking_guest_details_id')->references('id')->on('booking_guest_details')->onDelete('cascade');
            
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('day_tour_log_details');
    }
};
