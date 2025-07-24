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
        Schema::create('booking_guest_details', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('guest_type_id');
            $table->unsignedBigInteger('facility_booking_log_id');
            $table->integer('quantity');
            
            $table->foreign('guest_type_id')->references('id')->on('guest_type')->onDelete('cascade');
            $table->foreign('facility_booking_log_id')->references('id')->on('facility_booking_log')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('booking_guest_details');
    }
};
