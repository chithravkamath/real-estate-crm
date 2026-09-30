<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (!Schema::hasColumn('billings', 'client_id')) {
            Schema::table('billings', function (Blueprint $table) {
                $table->unsignedBigInteger('client_id')->nullable()->after('id');
                $table->foreign('client_id')->references('id')->on('clients')->onDelete('set null');
            });
        }

        // Backfill data using client_name
        $billings = DB::table('billings')->get();
        foreach ($billings as $billing) {
            $client = DB::table('clients')->where('name', $billing->client_name)->first();
            if ($client) {
                DB::table('billings')->where('id', $billing->id)->update(['client_id' => $client->id]);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('billings', 'client_id')) {
            Schema::table('billings', function (Blueprint $table) {
                $table->dropForeign(['client_id']);
                $table->dropColumn(['client_id']);
            });
        }
    }
};
