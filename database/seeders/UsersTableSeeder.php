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

            $accounts = [
                ['username' => 'admin', 'role_id' => 1, 'fullname' => 'Administrator Uji Coba', 'email' => 'admin@uji-coba.local'],
                ['username' => 'surveyor', 'role_id' => 2, 'fullname' => 'Surveyor Uji Coba', 'email' => 'surveyor@uji-coba.local'],
                ['username' => 'monitoring', 'role_id' => 3, 'fullname' => 'Monitoring Uji Coba', 'email' => 'monitoring@uji-coba.local'],
                ['username' => 'user', 'role_id' => 4, 'fullname' => 'User Uji Coba', 'email' => 'user@uji-coba.local'],
                ['username' => 'dummy', 'role_id' => 5, 'fullname' => 'Dummy Uji Coba', 'email' => 'dummy@uji-coba.local'],
            ];

            foreach ($accounts as $account) {
                DB::table('users')->updateOrInsert(
                    ['username' => $account['username']],
                    [
                        'role_id' => $account['role_id'],
                        'password' => Hash::make('UjiCoba123!'),
                        'updated_at' => $now,
                        'created_at' => $now,
                    ]
                );

                $userId = DB::table('users')->where('username', $account['username'])->value('id');

                DB::table('user_profiles')->updateOrInsert(
                    ['user_id' => $userId],
                    [
                        'activity_id' => $account['role_id'] === 1 ? null : 1,
                        'group_id' => in_array($account['role_id'], [2, 4, 5], true) ? 1 : null,
                        'unit_id' => in_array($account['role_id'], [2, 4, 5], true) ? 1 : null,
                        'fullname' => $account['fullname'],
                        'email' => $account['email'],
                        'no_handphone' => '080000000000',
                        'updated_at' => $now,
                        'created_at' => $now,
                    ]
                );
            }
        });
    }
}
