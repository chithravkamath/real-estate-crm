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
        // 1. Add user_id to clients
        if (!Schema::hasColumn('clients', 'user_id')) {
            Schema::table('clients', function (Blueprint $table) {
                $table->unsignedBigInteger('user_id')->nullable()->after('id');
                $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');
            });
        }

        // 2. Add client_id and property_id to deals
        Schema::table('deals', function (Blueprint $table) {
            if (!Schema::hasColumn('deals', 'client_id')) {
                $table->unsignedBigInteger('client_id')->nullable()->after('id');
                $table->foreign('client_id')->references('id')->on('clients')->onDelete('set null');
            }
            if (!Schema::hasColumn('deals', 'property_id')) {
                $table->unsignedBigInteger('property_id')->nullable()->after('client_id');
                $table->foreign('property_id')->references('id')->on('properties')->onDelete('set null');
            }
        });

        // 3. Backfill data
        // Backfill clients.user_id using matching email
        $clients = DB::table('clients')->get();
        foreach ($clients as $client) {
            $user = DB::table('users')->where('email', $client->email)->first();
            if ($user) {
                DB::table('clients')->where('id', $client->id)->update(['user_id' => $user->id]);
            }
        }

        // Backfill deals.client_id using matching client_name
        // Also backfill deals.property_id using matching property_name
        $deals = DB::table('deals')->get();
        foreach ($deals as $deal) {
            $client = DB::table('clients')->where('name', $deal->client_name)->first();
            $property = DB::table('properties')->where('property_name', $deal->property_name)->first();

            $update = [];
            if ($client) {
                $update['client_id'] = $client->id;
            }
            if ($property) {
                $update['property_id'] = $property->id;
            }

            if (!empty($update)) {
                DB::table('deals')->where('id', $deal->id)->update($update);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('deals', function (Blueprint $table) {
            $table->dropForeign(['client_id']);
            $table->dropForeign(['property_id']);
            $table->dropColumn(['client_id', 'property_id']);
        });

        Schema::table('clients', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropColumn(['user_id']);
        });
    }
};
