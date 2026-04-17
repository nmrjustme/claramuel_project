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
            $table->decimal('refund_amount', 10, 2)->nullable()->after('payment_date');
            $table->string('refund_reason')->nullable()->after('refund_amount'); 
            $table->string('refund_type')->nullable()->after('refund_date')->after('refund_reason');
            $table->string('refund_date')->nullable()->after('refund_reason');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->dropColumn('refund_amount');
            $table->dropColumn('refund_reason');
            $table->dropColumn('refund_type');
            $table->dropColumn('refund_date');
        });
    }
};
