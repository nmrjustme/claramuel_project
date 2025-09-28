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
        Schema::table('day_tour_log_details', function (Blueprint $table) {
            $table->string('reservation_status')->nullable()->after('status');
            $table->string('checked_in_at')->nullable()->after('reservation_status');
            $table->string('checked_out_at')->nullable()->after('checked_in_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('day_tour_log_details', function (Blueprint $table) {
            $table->dropColumn('reservation_status');
            $table->dropColumn('checked_in_at');
            $table->dropColumn('checked_out_at');
        });
    }
};
