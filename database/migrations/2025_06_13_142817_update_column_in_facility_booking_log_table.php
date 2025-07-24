<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // First update all existing NULL values to 'pending_confirmation'
        DB::table('facility_booking_log')
            ->whereNull('status')
            ->update(['status' => 'pending_confirmation']);

        // Then modify the column
        Schema::table('facility_booking_log', function (Blueprint $table) {
            $table->string('status')
                ->default('pending_confirmation')
                ->nullable(false)
                ->change();
        });
    }

    public function down(): void
    {
        Schema::table('facility_booking_log', function (Blueprint $table) {
            // First make the column nullable
            $table->string('status')
                ->nullable()
                ->default(null)
                ->change();
            
            // Then you could optionally set existing 'pending_confirmation' values back to NULL
            // if that's what you want in your rollback
        });
    }
};