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
        Schema::table('facility_booking_log', function (Blueprint $table) {
            $table->string('qr_code_path')->nullable()->after('checked_in_by');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('facility_booking_log', function (Blueprint $table) {
            $table->dropColumn('qr_code_path');
        });
    }
};
