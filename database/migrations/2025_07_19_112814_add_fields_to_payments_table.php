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
        Schema::table('payments', function (Blueprint $table) {
            $table->foreignId('verified_by')->nullable()->constrained('users');
            $table->timestamp('verified_at')->nullable();
            $table->boolean('is_read')->default(false);
            $table->boolean('is_updated')->default(false);
            $table->string('rejection_reason')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            Schema::table('payments', function (Blueprint $table) {
                $table->dropColumn(['is_read', 'is_updated', 'rejection_reason', 'verified_by', 'verified_at']);
            });
        });
    }
};
