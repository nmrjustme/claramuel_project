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
            $table->string('confirmation_token', 60)->nullable()->after('user_id');
            $table->string('verified_status', 50)->nullable()->after('confirmation_token');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('facility_booking_log', function (Blueprint $table) {
        });
    }
};
