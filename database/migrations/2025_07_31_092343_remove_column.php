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
        Schema::table('booking_guest_details', function (Blueprint $table) {
            $table->dropForeign(['facility_summary_id']);
            $table->dropColumn('facility_summary_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('booking_guest_details', function (Blueprint $table) {
            //
        });
    }
};
