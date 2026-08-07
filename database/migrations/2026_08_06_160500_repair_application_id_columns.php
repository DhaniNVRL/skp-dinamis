<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private const TABLES = [
        'answers',
        'competitors',
        'complete_profiles',
        'descriptions',
        'failed_jobs',
        'form_types',
        'forms',
        'jobs',
        'options',
        'question_types',
        'questions',
        'roles',
        'subunit_questions',
        'subunits',
        'survey_sessions',
        'user_profiles',
        'users',
    ];

    public function up(): void
    {
        $originalSqlMode = (string) (DB::selectOne('SELECT @@SESSION.sql_mode AS value')->value ?? '');
        $temporarySqlMode = collect(explode(',', $originalSqlMode))
            ->reject(fn (string $mode) => in_array($mode, [
                'STRICT_TRANS_TABLES',
                'STRICT_ALL_TABLES',
                'NO_ZERO_DATE',
                'NO_ZERO_IN_DATE',
            ], true))
            ->implode(',');

        DB::statement('SET SESSION sql_mode = ?', [$temporarySqlMode]);

        try {
            foreach (self::TABLES as $table) {
                $this->repairIdColumn($table);
            }
        } finally {
            DB::statement('SET SESSION sql_mode = ?', [$originalSqlMode]);
        }
    }

    public function down(): void
    {
        // Tidak dibalik: primary key dan AUTO_INCREMENT wajib untuk integritas data.
    }

    private function repairIdColumn(string $table): void
    {
        if (! Schema::hasTable($table) || ! Schema::hasColumn($table, 'id')) {
            return;
        }

        $column = DB::selectOne(
            <<<'SQL'
                SELECT COLUMN_TYPE, EXTRA
                FROM INFORMATION_SCHEMA.COLUMNS
                WHERE TABLE_SCHEMA = DATABASE()
                  AND TABLE_NAME = ?
                  AND COLUMN_NAME = 'id'
            SQL,
            [$table]
        );

        if (! $column || strtolower((string) $column->COLUMN_TYPE) !== 'bigint unsigned') {
            throw new \RuntimeException("Perbaikan {$table}.id dibatalkan: tipe kolom tidak sesuai.");
        }

        $hasInvalidIds = DB::table($table)->whereNull('id')->exists()
            || DB::table($table)
                ->select('id')
                ->groupBy('id')
                ->havingRaw('COUNT(*) > 1')
                ->exists();

        if ($hasInvalidIds) {
            throw new \RuntimeException(
                "Perbaikan {$table}.id dibatalkan: ditemukan ID kosong atau duplikat."
            );
        }

        $primaryColumns = collect(DB::select(
            <<<'SQL'
                SELECT COLUMN_NAME
                FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE
                WHERE TABLE_SCHEMA = DATABASE()
                  AND TABLE_NAME = ?
                  AND CONSTRAINT_NAME = 'PRIMARY'
                ORDER BY ORDINAL_POSITION
            SQL,
            [$table]
        ))->pluck('COLUMN_NAME')->all();

        if ($primaryColumns === []) {
            DB::statement("ALTER TABLE `{$table}` ADD PRIMARY KEY (`id`)");
        } elseif ($primaryColumns !== ['id']) {
            throw new \RuntimeException(
                "Perbaikan {$table}.id dibatalkan: primary key tidak sesuai."
            );
        }

        if (! str_contains(strtolower((string) $column->EXTRA), 'auto_increment')) {
            DB::statement(
                "ALTER TABLE `{$table}` MODIFY `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT"
            );
        }
    }
};
