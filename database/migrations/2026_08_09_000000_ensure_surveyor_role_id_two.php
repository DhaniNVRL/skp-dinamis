<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $roleAtIdTwo = DB::table('roles')->where('id', 2)->first();

        if ($roleAtIdTwo) {
            if (strtolower(trim((string) $roleAtIdTwo->name)) !== 'surveyor') {
                throw new RuntimeException(
                    'Role ID 2 sudah digunakan oleh role lain. Migrasi Surveyor dibatalkan.'
                );
            }

            return;
        }

        $existingSurveyor = DB::table('roles')
            ->whereRaw('LOWER(name) = ?', ['surveyor'])
            ->first();

        if ($existingSurveyor) {
            throw new RuntimeException(
                'Role Surveyor sudah menggunakan ID '.$existingSurveyor->id.'. Migrasi tidak mengubah relasi secara otomatis.'
            );
        }

        DB::table('roles')->insert([
            'id' => 2,
            'name' => 'Surveyor',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    public function down(): void
    {
        if (! DB::table('users')->where('role_id', 2)->exists()) {
            DB::table('roles')
                ->where('id', 2)
                ->whereRaw('LOWER(name) = ?', ['surveyor'])
                ->delete();
        }
    }
};
