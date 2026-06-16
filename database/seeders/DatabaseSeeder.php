<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        Schema::disableForeignKeyConstraints();

        $this->call([
            RolesTableSeeder::class,
            ActivityTableSeeder::class,
            GroupsTableSeeder::class,
            UnitsTableSeeder::class,
            FormTypeSeeder::class,
            QuestionTypeSeeder::class,
        ]);

        Schema::enableForeignKeyConstraints();

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
            ],
            [
                'id' => 4,
                'username' => 'testuser',
                'password' => Hash::make('testpassword'),
                'id_roles' => 4,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}