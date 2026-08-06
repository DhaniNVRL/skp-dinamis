<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RolesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
   public function run(): void
    {
        foreach (['Admin', 'PM', 'Surveyor', 'Monitoring', 'User'] as $name) {
            DB::table('roles')->insertOrIgnore([
                [
                    'name' => $name,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
            ]);
        }
    }

}
