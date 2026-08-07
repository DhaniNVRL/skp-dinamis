<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $this->assertExpectedAnswerSchema();
        $this->assertNoDuplicates('users', ['username']);
        $this->assertNoDuplicates('user_profiles', ['user_id']);
        $this->assertNoDuplicates('survey_sessions', ['user_id']);
        $this->assertNoDuplicates('descriptions', ['form_id']);
        $this->assertNoDuplicates(
            'subunit_questions',
            ['subunit_id', 'question_id', 'form_id']
        );
        $this->assertSubunitQuestionReferencesExist();

        $this->normalizeLegacyColumnNames();

        if (Schema::hasTable('questions') && Schema::hasColumn('questions', 'no_header')) {
            Schema::table('questions', function (Blueprint $table): void {
                $table->string('no_header')->nullable()->change();
            });
        } elseif (Schema::hasTable('questions')) {
            Schema::table('questions', function (Blueprint $table): void {
                $table->string('no_header')->nullable()->after('form_id');
            });
        }

        if (Schema::hasTable('answers') && Schema::hasColumn('answers', 'competitor_id')) {
            Schema::table('answers', function (Blueprint $table): void {
                $table->foreignId('competitor_id')->nullable()->change();
            });
        }

        $this->addUniqueIfMissing('users', ['username'], 'users_username_unique');
        $this->addUniqueIfMissing('user_profiles', ['user_id'], 'user_profiles_user_id_unique');
        $this->addUniqueIfMissing('survey_sessions', ['user_id'], 'survey_sessions_user_id_unique');
        $this->addUniqueIfMissing('descriptions', ['form_id'], 'descriptions_form_id_unique');
        $this->addUniqueIfMissing(
            'subunit_questions',
            ['subunit_id', 'question_id', 'form_id'],
            'subunit_questions_context_unique'
        );

        $this->addSubunitQuestionForeignKeys();
    }

    public function down(): void
    {
        if (Schema::hasTable('subunit_questions')) {
            Schema::table('subunit_questions', function (Blueprint $table): void {
                foreach ([
                    'subunit_questions_subunit_fk',
                    'subunit_questions_question_fk',
                    'subunit_questions_form_fk',
                ] as $constraint) {
                    if ($this->foreignKeyExists('subunit_questions', $constraint)) {
                        $table->dropForeign($constraint);
                    }
                }

                if ($this->indexExists('subunit_questions', 'subunit_questions_context_unique')) {
                    $table->dropUnique('subunit_questions_context_unique');
                }
            });
        }

        $this->dropUniqueIfPresent('descriptions', 'descriptions_form_id_unique');
        $this->dropUniqueIfPresent('survey_sessions', 'survey_sessions_user_id_unique');
        $this->dropUniqueIfPresent('user_profiles', 'user_profiles_user_id_unique');
        $this->dropUniqueIfPresent('users', 'users_username_unique');
    }

    private function normalizeLegacyColumnNames(): void
    {
        $this->renameIfNeeded('users', 'id_roles', 'role_id');
        $this->renameIfNeeded('questions', 'id_groups', 'group_id');
        $this->renameIfNeeded('questions', 'id_questiontypes', 'questiontype_id');
        $this->renameIfNeeded('questions', 'questiontypes_id', 'questiontype_id');
    }

    private function renameIfNeeded(string $tableName, string $from, string $to): void
    {
        if (! Schema::hasTable($tableName)
            || ! Schema::hasColumn($tableName, $from)
            || Schema::hasColumn($tableName, $to)) {
            return;
        }

        Schema::table($tableName, function (Blueprint $table) use ($from, $to): void {
            $table->renameColumn($from, $to);
        });
    }

    private function assertExpectedAnswerSchema(): void
    {
        if (! Schema::hasTable('answers')) {
            return;
        }

        foreach (['user_id', 'form_id', 'question_id', 'answer'] as $column) {
            if (! Schema::hasColumn('answers', $column)) {
                throw new \RuntimeException(
                    "Schema tabel answers masih legacy: kolom {$column} tidak ditemukan. "
                    .'Lakukan migrasi data jawaban secara khusus sebelum hardening.'
                );
            }
        }
    }

    private function assertNoDuplicates(string $table, array $columns): void
    {
        if (! Schema::hasTable($table)) {
            return;
        }

        foreach ($columns as $column) {
            if (! Schema::hasColumn($table, $column)) {
                return;
            }
        }

        $duplicate = DB::table($table)
            ->select($columns)
            ->groupBy($columns)
            ->havingRaw('COUNT(*) > 1')
            ->first();

        if ($duplicate) {
            throw new \RuntimeException(
                'Migration dibatalkan: ditemukan data duplikat pada '
                .$table.' untuk kombinasi '.implode(', ', $columns).'.'
            );
        }
    }

    private function assertSubunitQuestionReferencesExist(): void
    {
        if (! Schema::hasTable('subunit_questions')) {
            return;
        }

        foreach ([
            'subunit_id' => ['subunits', 'id'],
            'question_id' => ['questions', 'id'],
            'form_id' => ['forms', 'id'],
        ] as $column => [$parentTable, $parentKey]) {
            if (! Schema::hasColumn('subunit_questions', $column)
                || ! Schema::hasTable($parentTable)) {
                continue;
            }

            $orphanExists = DB::table('subunit_questions as pivot')
                ->leftJoin(
                    $parentTable.' as parent',
                    'parent.'.$parentKey,
                    '=',
                    'pivot.'.$column
                )
                ->whereNull('parent.'.$parentKey)
                ->exists();

            if ($orphanExists) {
                throw new \RuntimeException(
                    "Migration dibatalkan: subunit_questions memiliki {$column} orphan."
                );
            }
        }
    }

    private function addUniqueIfMissing(string $tableName, array $columns, string $name): void
    {
        if (! Schema::hasTable($tableName) || $this->indexExists($tableName, $name)) {
            return;
        }

        foreach ($columns as $column) {
            if (! Schema::hasColumn($tableName, $column)) {
                return;
            }
        }

        Schema::table($tableName, function (Blueprint $table) use ($columns, $name): void {
            $table->unique($columns, $name);
        });
    }

    private function addSubunitQuestionForeignKeys(): void
    {
        if (! Schema::hasTable('subunit_questions')) {
            return;
        }

        Schema::table('subunit_questions', function (Blueprint $table): void {
            if (! $this->foreignKeyExists('subunit_questions', 'subunit_questions_subunit_fk')) {
                $table->foreign('subunit_id', 'subunit_questions_subunit_fk')
                    ->references('id')->on('subunits')->cascadeOnDelete();
            }

            if (! $this->foreignKeyExists('subunit_questions', 'subunit_questions_question_fk')) {
                $table->foreign('question_id', 'subunit_questions_question_fk')
                    ->references('id')->on('questions')->cascadeOnDelete();
            }

            if (! $this->foreignKeyExists('subunit_questions', 'subunit_questions_form_fk')) {
                $table->foreign('form_id', 'subunit_questions_form_fk')
                    ->references('id')->on('forms')->cascadeOnDelete();
            }
        });
    }

    private function dropUniqueIfPresent(string $tableName, string $name): void
    {
        if (! Schema::hasTable($tableName) || ! $this->indexExists($tableName, $name)) {
            return;
        }

        Schema::table($tableName, fn (Blueprint $table) => $table->dropUnique($name));
    }

    private function indexExists(string $tableName, string $name): bool
    {
        return collect(Schema::getIndexes($tableName))
            ->contains(fn (array $index) => ($index['name'] ?? null) === $name);
    }

    private function foreignKeyExists(string $tableName, string $name): bool
    {
        return collect(Schema::getForeignKeys($tableName))
            ->contains(fn (array $key) => ($key['name'] ?? null) === $name);
    }
};
