<?php

use App\Models\QuestionType;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasColumn('question_types', 'is_title')) {
            Schema::table('question_types', function (Blueprint $table): void {
                $table->dropColumn('is_title');
            });
        }
    }

    public function down(): void
    {
        if (! Schema::hasColumn('question_types', 'is_title')) {
            Schema::table('question_types', function (Blueprint $table): void {
                $table->boolean('is_title')->default(false)->after('description');
            });
        }

        DB::table('question_types')
            ->where('id', QuestionType::TITLE_ONLY_ID)
            ->update(['is_title' => true]);
    }
};
