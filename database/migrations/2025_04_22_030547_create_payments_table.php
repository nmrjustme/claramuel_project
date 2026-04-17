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
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('facility_log_id')->nullable();
            $table->unsignedBigInteger('event_log_id')->nullable();
            $table->string('method');
            $table->string('status', 50);
            $table->string('reference_no')->nullable();
            $table->string('payer_email')->nullable();
            $table->string('GCash_number')->nullable();
            $table->decimal('amount', 7, 2);
            $table->timestamp('payment_date')->useCurrent();
            
            $table->foreign('facility_log_id')->references('id')->on('facility_booking_log')->onDelete('cascade');
            $table->foreign('event_log_id')->references('id')->on('event_booking_log')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
