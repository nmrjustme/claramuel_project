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
        Schema::create('event_booking_details', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('event_booking_log_id');
            $table->unsignedBigInteger('package_id');
            $table->string('event_date');
            $table->string('status');
            $table->string('guest_count');
            $table->string('reservation_type');
            $table->decimal('total_cost', 7, 2);
            
            $table->foreign('event_booking_log_id')->references('id')->on('event_booking_log')->onDelete('cascade');
            $table->foreign('package_id')->references('id')->on('event_packages')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('event_booking_details');
    }
};
