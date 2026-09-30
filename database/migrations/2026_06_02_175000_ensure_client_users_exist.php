<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $clients = DB::table('clients')->get();
        foreach ($clients as $client) {
            // Find or create User for this client
            $user = DB::table('users')->where('email', $client->email)->first();
            if (!$user) {
                $userId = DB::table('users')->insertGetId([
                    'name' => $client->name,
                    'email' => $client->email,
                    'password' => Hash::make('password'),
                    'role' => 'client',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            } else {
                $userId = $user->id;
                // Ensure role is client if it's already there
                DB::table('users')->where('id', $userId)->update(['role' => 'client']);
            }

            // Link client to user
            DB::table('clients')->where('id', $client->id)->update(['user_id' => $userId]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No need to delete users on down to prevent data loss
    }
};
