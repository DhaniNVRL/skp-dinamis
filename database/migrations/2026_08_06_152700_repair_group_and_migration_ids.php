<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $this->restoreAutoIncrement('migrations', 'INT UNSIGNED');
        $this->restoreAutoIncrement('groups', 'BIGINT UNSIGNED');
    }

    public function down(): void
    {
        // Tidak dibalik: menghapus AUTO_INCREMENT membuat insert gagal kembali.
    }

    private function restoreAutoIncrement(string $table, string $expectedType): void
    {
        if (! Schema::hasTable($table) || ! Schema::hasColumn($table, 'id')) {
            return;
        }

        $column = DB::selectOne(
            <<<'SQL'
                SELECT COLUMN_TYPE, COLUMN_KEY, EXTRA
                FROM INFORMATION_SCHEMA.COLUMNS
                WHERE TABLE_SCHEMA = DATABASE()
                  AND TABLE_NAME = ?
                  AND COLUMN_NAME = 'id'
            SQL,
            [$table]
        );

        if (! $column) {
            return;
        }

        if (strtoupper((string) $column->COLUMN_TYPE) !== $expectedType
            || strtoupper((string) $column->COLUMN_KEY) !== 'PRI') {
            throw new \RuntimeException(
                "Perbaikan {$table}.id dibatalkan karena tipe atau primary key tidak sesuai."
            );
        }

        if (! str_contains(strtolower((string) $column->EXTRA), 'auto_increment')) {
            DB::statement(
                "ALTER TABLE `{$table}` MODIFY `id` {$expectedType} NOT NULL AUTO_INCREMENT"
            );
        }
    }
};
