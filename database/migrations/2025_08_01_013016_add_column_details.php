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
            $table->string('arriving_time', 50)->nullable()->after('facility_summary_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('facility_booking_details', function (Blueprint $table) {
            $table->dropColumn('arriving_time');
        });
    }
};
