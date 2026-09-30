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
        Schema::table('leads', function (Blueprint $table) {
            if (!Schema::hasColumn('leads', 'assigned_agent_id')) {
                $table->unsignedBigInteger('assigned_agent_id')->nullable()->after('agent');
                $table->foreign('assigned_agent_id')->references('id')->on('users')->onDelete('set null');
            }
        });

        Schema::table('deals', function (Blueprint $table) {
            if (!Schema::hasColumn('deals', 'agent_id')) {
                $table->unsignedBigInteger('agent_id')->nullable()->after('agent_name');
                $table->foreign('agent_id')->references('id')->on('users')->onDelete('set null');
            }
        });

        Schema::table('site_visits', function (Blueprint $table) {
            if (!Schema::hasColumn('site_visits', 'agent_id')) {
                $table->unsignedBigInteger('agent_id')->nullable()->after('agent_name');
                $table->foreign('agent_id')->references('id')->on('users')->onDelete('set null');
            }
        });

        // Backfill existing data matching agent name to user accounts
        $users = \DB::table('users')->where('role', 'agent')->get();
        foreach ($users as $user) {
            \DB::table('leads')->where('agent', $user->name)->update(['assigned_agent_id' => $user->id]);
            \DB::table('deals')->where('agent_name', $user->name)->update(['agent_id' => $user->id]);
            \DB::table('site_visits')->where('agent_name', $user->name)->update(['agent_id' => $user->id]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('leads', function (Blueprint $table) {
            if (Schema::hasColumn('leads', 'assigned_agent_id')) {
                $table->dropForeign(['assigned_agent_id']);
                $table->dropColumn('assigned_agent_id');
            }
        });

        Schema::table('deals', function (Blueprint $table) {
            if (Schema::hasColumn('deals', 'agent_id')) {
                $table->dropForeign(['agent_id']);
                $table->dropColumn('agent_id');
            }
        });

        Schema::table('site_visits', function (Blueprint $table) {
            if (Schema::hasColumn('site_visits', 'agent_id')) {
                $table->dropForeign(['agent_id']);
                $table->dropColumn('agent_id');
            }
        });
    }
};
