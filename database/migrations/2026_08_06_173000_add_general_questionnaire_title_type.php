<?php

use App\Models\QuestionType;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        QuestionType::query()->firstOrCreate(
            ['name' => QuestionType::TITLE_ONLY_NAME],
            ['description' => 'Menampilkan judul atau pemisah bagian tanpa kolom jawaban.']
        );
    }

    public function down(): void
    {
        $type = QuestionType::query()
            ->where('name', QuestionType::TITLE_ONLY_NAME)
            ->first();

        if ($type && ! $type->questions()->exists()) {
            $type->delete();
        }
    }
};
