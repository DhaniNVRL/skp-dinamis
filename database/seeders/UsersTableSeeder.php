<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UsersTableSeeder extends Seeder
{
    public function run(): void
    {
        DB::transaction(function (): void {
            $now = now();

            DB::table('users')->updateOrInsert(
                ['username' => 'admin'],
                [
                    'role_id' => 1,
                    'password' => Hash::make('admin123'),
                    'updated_at' => $now,
                    'created_at' => $now,
                ]
            );

            DB::table('users')->updateOrInsert(
                ['username' => 'surveyor'],
                [
                    'role_id' => 2,
                    'password' => Hash::make('surveyor123'),
                    'updated_at' => $now,
                    'created_at' => $now,
                ]
            );

            $surveyorId = DB::table('users')
                ->where('username', 'surveyor')
                ->value('id');

            DB::table('user_profiles')->updateOrInsert(
                ['user_id' => $surveyorId],
                [
                    'activity_id' => 1,
                    'group_id' => 1,
                    'unit_id' => 1,
                    'fullname' => 'Surveyor Test',
                    'email' => 'surveyor@test.local',
                    'no_handphone' => '080000000000',
                    'updated_at' => $now,
                    'created_at' => $now,
                ]
            );
        });
    }
}