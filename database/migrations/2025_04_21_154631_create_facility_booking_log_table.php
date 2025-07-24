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
        Schema::create('facility_booking_log', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->string('status')->nullable();
            $table->string('approved_by')->nullable();
            $table->timestamp('created_at')->useCurrent(); // default CURRENT_TIMESTAMP
            $table->string('reported_by')->nullable();
            $table->dateTime('reported_at')->nullable();
            $table->text('completion_not')->nullable();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('facility_booking_log');
    }
};

