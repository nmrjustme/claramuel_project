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
        Schema::create('facilities', function (Blueprint $table) {
            $table->id();
            $table->string('name', 50);
            $table->string('category');
            $table->text('description')->nullable();
            $table->string('status')->nullable();
            $table->integer('pax')->nullable();
            $table->string('rate_type')->nullable();
            $table->decimal('price', 7, 2)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('facilities', function (Blueprint $table) {
            Schema::dropIfExists('facilities');
        });
    }
};
