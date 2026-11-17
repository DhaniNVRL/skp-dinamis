<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        if (DB::table('roles')->whereRaw('LOWER(name) = ?', ['dummy'])->exists()) {
            return;
        }

        DB::table('roles')->insert([
            'name' => 'Dummy',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    public function down(): void
    {
        $role = DB::table('roles')
            ->whereRaw('LOWER(name) = ?', ['dummy'])
            ->first();

        if ($role && ! DB::table('users')->where('role_id', $role->id)->exists()) {
            DB::table('roles')->where('id', $role->id)->delete();
        }
    }
};
