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
            $table->unsignedBigInteger('breakfast_id')->after('id')->nullable();
            $table->foreign('breakfast_id')->references('id')->on('breakfast')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('facility_booking_details', function (Blueprint $table) {
            $table->dropForeign('breakfast_id'); // Drop the foreign key constraint
            $table->dropColumn('breakfast_id'); 
        });
    }
};
