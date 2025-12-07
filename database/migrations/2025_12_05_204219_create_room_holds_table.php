<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('room_holds', function (Blueprint $table) {
            $table->id();
            $table->foreignId('facility_id')->constrained()->onDelete('cascade');
            $table->date('date_from');
            $table->date('date_to');
            $table->string('session_id');
            $table->timestamp('expires_at');
            $table->enum('status', ['pending', 'confirmed', 'expired'])->default('pending');
            $table->timestamps();
            
            $table->index(['facility_id', 'date_from', 'date_to']);
            $table->index('session_id');
            $table->index('expires_at');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('room_holds');
    }
};