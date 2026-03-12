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
        $this->call([
            ActivityTableSeeder::class,
        ]);
        $this->call([
            GroupsTableSeeder::class,
        ]);
        $this->call([
            UnitsTableSeeder::class,
        ]);
        $this->call([
            FormTypeSeeder::class,
        ]);
        $this->call([
            QuestionTypeSeeder::class,
        ]);

        // Buat user yang merujuk ke role id 1
        User::insert([
            [
                'id' => 1,
                'username' => 'testadmin',
                'password' => Hash::make('testpassword'),
                'id_roles' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 2,
                'username' => 'testpm',
                'password' => Hash::make('testpassword'),
                'id_roles' => 2,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 3,
                'username' => 'testsurveyor',
                'password' => Hash::make('testpassword'),
                'id_roles' => 3,
                'created_at' => now(),
                'updated_at' => now(),
            ],[
                'id' => 4,
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
