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
        Schema::table('facility_summary', function (Blueprint $table) {
            $table->decimal('breakfast_price', 10, 2)->nullable()->after('breakfast_id');
            $table->decimal('facility_price', 10, 2)->nullable()->after('facility_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('facility_summary', function (Blueprint $table) {
            //
        });
    }
};
