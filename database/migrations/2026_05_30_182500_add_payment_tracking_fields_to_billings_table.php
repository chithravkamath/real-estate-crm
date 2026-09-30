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
        Schema::table('billings', function (Blueprint $table) {
            $table->decimal('advance_amount', 15, 2)->nullable();
            $table->timestamp('advance_paid_at')->nullable();
            $table->decimal('final_amount', 15, 2)->nullable();
            $table->timestamp('final_paid_at')->nullable();
            $table->decimal('due_amount', 15, 2)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('billings', function (Blueprint $table) {
            $table->dropColumn([
                'advance_amount',
                'advance_paid_at',
                'final_amount',
                'final_paid_at',
                'due_amount'
            ]);
        });
    }
};
