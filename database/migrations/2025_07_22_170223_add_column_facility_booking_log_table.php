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
            $table->string('checked_in_at')->nullable()->after('approved_by');
            $table->string('checked_in_by')->nullable()->after('checked_in_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('facility_booking_log', function (Blueprint $table) {
            $table->dropColumn('checked_in_at');
            $table->dropColumn('checked_in_by');
        });
    }
};
