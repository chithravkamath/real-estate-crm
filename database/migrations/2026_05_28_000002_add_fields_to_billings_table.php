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
            if (! Schema::hasColumn('billings', 'property_name')) {
                $table->string('property_name')->nullable()->after('client_name');
            }
            if (! Schema::hasColumn('billings', 'agent_name')) {
                $table->string('agent_name')->nullable()->after('property_name');
            }
            if (! Schema::hasColumn('billings', 'commission')) {
                $table->string('commission')->default('0')->after('payment_amount');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('billings', function (Blueprint $table) {
            if (Schema::hasColumn('billings', 'commission')) {
                $table->dropColumn('commission');
            }
            if (Schema::hasColumn('billings', 'agent_name')) {
                $table->dropColumn('agent_name');
            }
            if (Schema::hasColumn('billings', 'property_name')) {
                $table->dropColumn('property_name');
            }
        });
    }
};
