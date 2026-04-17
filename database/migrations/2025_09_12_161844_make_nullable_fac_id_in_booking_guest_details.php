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
            $table->dropForeign(['facility_id']);

            // Then, make it nullable
            $table->unsignedBigInteger('facility_id')->nullable()->after('id')->change();

            // Re-add the foreign key, but allow NULL
            $table->foreign('facility_id')
                ->references('id')
                ->on('facilities')
                ->onDelete('cascade');
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
