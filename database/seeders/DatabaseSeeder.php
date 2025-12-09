<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Jalankan seeder untuk roles terlebih dahulu
        $this->call([
            RolesTableSeeder::class,
        ]);

        // Buat user yang merujuk ke role id 1
        User::insert([
            [
                'id' => 1,
                'name' => 'Test Admin',
                'username' => 'testadmin',
                'password' => Hash::make('testpassword'),
                'id_roles' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 2,
                'name' => 'Test PM',
                'username' => 'testpm',
                'password' => Hash::make('testpassword'),
                'id_roles' => 2,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 3,
                'name' => 'Test Surveyor',
                'username' => 'testsurveyor',
                'password' => Hash::make('testpassword'),
                'id_roles' => 3,
                'created_at' => now(),
                'updated_at' => now(),
            ],[
                'id' => 4,
                'name' => 'Test User',
                'username' => 'testuser',
                'password' => Hash::make('testpassword'),
                'id_roles' => 4,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            // dan seterusnya...
        ]);

    }
}
