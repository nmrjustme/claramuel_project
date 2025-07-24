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
        Schema::table('users', function (Blueprint $table) {
            $table->string('firstname', 50)->after('id');
            $table->string('lastname', 50)->after('firstname');
            $table->string('phone', 13)->after('lastname');
            $table->enum('role', ['Customer', 'Admin', 'Owner', 'Staff'])->default('Customer')->after('password');
            $table->string('profile_img')->default('default.jpg')->after('role');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['firstname', 'lastname', 'phone', 'role', 'profile_img']);
        });
    }
};
