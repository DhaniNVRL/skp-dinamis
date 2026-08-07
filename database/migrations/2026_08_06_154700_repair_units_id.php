<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('units') || ! Schema::hasColumn('units', 'id')) {
            return;
        }

        $column = DB::selectOne(
            <<<'SQL'
                SELECT COLUMN_TYPE, EXTRA
                FROM INFORMATION_SCHEMA.COLUMNS
                WHERE TABLE_SCHEMA = DATABASE()
                  AND TABLE_NAME = 'units'
                  AND COLUMN_NAME = 'id'
            SQL
        );

        if (! $column || strtolower((string) $column->COLUMN_TYPE) !== 'bigint unsigned') {
            throw new \RuntimeException('Perbaikan units.id dibatalkan karena tipe kolom tidak sesuai.');
        }

        if (DB::table('units')->whereNull('id')->exists()
            || DB::table('units')->select('id')->groupBy('id')->havingRaw('COUNT(*) > 1')->exists()) {
            throw new \RuntimeException('Perbaikan units.id dibatalkan karena terdapat ID kosong atau duplikat.');
        }

        $primaryColumns = collect(DB::select(
            <<<'SQL'
                SELECT COLUMN_NAME
                FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE
                WHERE TABLE_SCHEMA = DATABASE()
                  AND TABLE_NAME = 'units'
                  AND CONSTRAINT_NAME = 'PRIMARY'
                ORDER BY ORDINAL_POSITION
            SQL
        ))->pluck('COLUMN_NAME')->all();

        if ($primaryColumns === []) {
            DB::statement('ALTER TABLE `units` ADD PRIMARY KEY (`id`)');
        } elseif ($primaryColumns !== ['id']) {
            throw new \RuntimeException('Perbaikan units.id dibatalkan karena primary key tidak sesuai.');
        }

        if (! str_contains(strtolower((string) $column->EXTRA), 'auto_increment')) {
            DB::statement(
                'ALTER TABLE `units` MODIFY `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT'
            );
        }
    }

    public function down(): void
    {
        // Tidak dibalik karena primary key dan AUTO_INCREMENT wajib untuk integritas data.
    }
};
