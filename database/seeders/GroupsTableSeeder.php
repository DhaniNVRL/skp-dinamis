<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class GroupsTableSeeder extends Seeder
{
    public function run(): void
    {
        // MATIKAN foreign key check sementara
        Schema::disableForeignKeyConstraints();

        DB::table('groups')->truncate();

        Schema::enableForeignKeyConstraints();

        DB::table('groups')->insert([
            [
                'id' => 1,
                'activity_id' => 1,
                'name' => 'Energi Primer',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 2,
                'activity_id' => 1,
                'name' => 'Keuangan, Komunikasi dan Umum',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 3,
                'activity_id' => 1,
                'name' => 'Perencanaan & Engineering',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 4,
                'activity_id' => 1,
                'name' => 'Produksi (Operasi & Settlement, Pemeliharaan & Logistik)',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 5,
                'activity_id' => 1,
                'name' => 'K3L',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 6,
                'activity_id' => 1,
                'name' => 'General Manager',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 7,
                'activity_id' => 1,
                'name' => 'Pengadaan',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 8,
                'activity_id' => 2,
                'name' => 'Jamali',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}