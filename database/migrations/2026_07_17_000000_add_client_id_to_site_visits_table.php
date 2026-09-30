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
        if (!Schema::hasColumn('site_visits', 'client_id')) {
            Schema::table('site_visits', function (Blueprint $table) {
                $table->unsignedBigInteger('client_id')->nullable()->after('id');
                $table->foreign('client_id')->references('id')->on('clients')->onDelete('set null');
            });
        }

        // Backfill existing site visits using matching client_name
        $visits = DB::table('site_visits')->get();
        foreach ($visits as $visit) {
            $client = DB::table('clients')->where('name', $visit->client_name)->first();
            if ($client) {
                DB::table('site_visits')->where('id', $visit->id)->update(['client_id' => $client->id]);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('site_visits', function (Blueprint $table) {
            $table->dropForeign(['client_id']);
            $table->dropColumn(['client_id']);
        });
    }
};
