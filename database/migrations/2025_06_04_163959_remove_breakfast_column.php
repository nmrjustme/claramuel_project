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
            $table->dropColumn(['breakfast_price', 'breakfast_included']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('facility_booking_details', function (Blueprint $table) {
            $table->boolean('breakfast_included')->default(false);
            $table->decimal('breakfast_price', 7, 2)->default(0);
        });
    }
};
