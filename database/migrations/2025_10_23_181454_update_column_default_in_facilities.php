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
          DB::table('facilities')
            ->whereNull('type')
            ->update(['type' => 'room']);

        // Now safely modify column
        Schema::table('facilities', function (Blueprint $table) {
            $table->string('type')->default('room')->nullable(false)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('facilities', function (Blueprint $table) {
        
        });
    }
};
