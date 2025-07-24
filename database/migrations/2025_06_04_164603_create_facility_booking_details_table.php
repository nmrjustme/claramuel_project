
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
        Schema::create('facility_booking_details', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('facility_booking_log_id');
            $table->unsignedBigInteger('facility_id');
            $table->unsignedBigInteger('facility_summary_id')->nullable();
            $table->integer('facility_qty');
            $table->string('checkin_date');
            $table->string('checkout_date');
            $table->string('reservation_type');
            $table->decimal('total_price', 7, 2);
        
      
            $table->foreign('facility_summary_id')->references('id')->on('facility_summary')->onDelete('cascade');
            $table->foreign('facility_booking_log_id')->references('id')->on('facility_booking_log')->onDelete('cascade');
            $table->foreign('facility_id')->references('id')->on('facilities')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('facility_booking_details');
    }
};
