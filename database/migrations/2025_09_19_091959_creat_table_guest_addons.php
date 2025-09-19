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
        Schema::create('guest_addons', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('facility_booking_log_id');
            $table->string('type', 50);
            $table->decimal('cost', 10, 2);
            $table->integer('quantity');
            $table->decimal('total_cost', 10, 2);
            $table->timestamps();
            $table->foreign('facility_booking_log_id')->references('id')->on('facility_booking_log')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('guest_addons');
    }
};
