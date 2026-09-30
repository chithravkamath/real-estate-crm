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
        Schema::table('deals', function (Blueprint $table) {
            $table->string('booking_status')->default('pending')->nullable();
            $table->timestamp('booking_confirmed_at')->nullable();
            $table->string('sale_status')->default('pending')->nullable();
            $table->timestamp('sale_confirmed_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('deals', function (Blueprint $table) {
            $table->dropColumn([
                'booking_status',
                'booking_confirmed_at',
                'sale_status',
                'sale_confirmed_at'
            ]);
        });
    }
};
