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
            $table->integer('qty')->nullable();
        }); // <-- FIXED HERE
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('facility_summary', function (Blueprint $table) {
            $table->dropColumn('qty');
        }); // <-- FIXED HERE
    }
};
