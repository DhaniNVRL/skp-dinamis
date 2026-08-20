<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private const TABLES = [
        'survey_branch_rules',
        'survey_branch_rule_questions',
        'survey_branch_rule_skipped_questions',
        'survey_branch_rule_skipped_forms',
    ];

    public function up(): void
    {
        if (! Schema::hasTable('survey_branch_rule_skipped_forms')) {
            Schema::create('survey_branch_rule_skipped_forms', function (Blueprint $table): void {
                $table->id();
                $table->unsignedBigInteger('survey_branch_rule_id');
                $table->unsignedBigInteger('form_id');
                $table->timestamps();
                $table->unique(['survey_branch_rule_id', 'form_id'], 'branch_rule_skipped_form_unique');
            });
        }

        foreach (self::TABLES as $table) {
            $this->repairId($table);
        }
    }

    public function down(): void
    {
        // AUTO_INCREMENT adalah perbaikan integritas dan tidak aman untuk dibalik.
    }

    private function repairId(string $table): void
    {
        if (! Schema::hasTable($table) || ! Schema::hasColumn($table, 'id')) {
            return;
        }

        $column = DB::selectOne(
            'SELECT COLUMN_TYPE, COLUMN_KEY, EXTRA FROM INFORMATION_SCHEMA.COLUMNS
             WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = ? AND COLUMN_NAME = ?',
            [$table, 'id']
        );

        if (! $column || strtolower((string) $column->COLUMN_TYPE) !== 'bigint unsigned') {
            throw new RuntimeException("Perbaikan {$table}.id dibatalkan: tipe kolom harus BIGINT UNSIGNED.");
        }

        $invalid = DB::table($table)->whereNull('id')->exists()
            || DB::table($table)->select('id')->groupBy('id')->havingRaw('COUNT(*) > 1')->exists();

        if ($invalid) {
            throw new RuntimeException("Perbaikan {$table}.id dibatalkan: ditemukan ID kosong atau duplikat.");
        }

        if ((string) $column->COLUMN_KEY !== 'PRI') {
            DB::statement("ALTER TABLE `{$table}` ADD PRIMARY KEY (`id`)");
        }

        if (! str_contains(strtolower((string) $column->EXTRA), 'auto_increment')) {
            DB::statement("ALTER TABLE `{$table}` MODIFY `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT");
        }
    }
};
