<?php

use Illuminate\Contracts\Console\Kernel;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use App\Http\Controllers\FormController;
use App\Http\Controllers\QuestionController;
use App\Http\Controllers\FormTypeController;
use App\Http\Controllers\QuestionTypeController;
use App\Models\FormType;
use App\Models\QuestionType;

require __DIR__.'/../vendor/autoload.php';

$app = require __DIR__.'/../bootstrap/app.php';
$app->make(Kernel::class)->bootstrap();

$tables = [
    'answers', 'competitors', 'complete_profiles', 'descriptions',
    'failed_jobs', 'form_types', 'forms', 'jobs', 'options',
    'question_types', 'questions', 'roles', 'subunit_questions',
    'subunits', 'survey_sessions', 'user_profiles', 'users',
];

if (! QuestionType::query()->whereKey(QuestionType::TITLE_ONLY_ID)->exists()) {
    throw new RuntimeException('Tipe Judul (Tanpa Jawaban) belum tersedia di database.');
}

$invalid = [];
foreach ($tables as $table) {
    $column = DB::selectOne(
        <<<'SQL'
            SELECT COLUMN_KEY, EXTRA
            FROM INFORMATION_SCHEMA.COLUMNS
            WHERE TABLE_SCHEMA = DATABASE()
              AND TABLE_NAME = ?
              AND COLUMN_NAME = 'id'
        SQL,
        [$table]
    );

    if (! $column || $column->COLUMN_KEY !== 'PRI' || ! str_contains(strtolower($column->EXTRA), 'auto_increment')) {
        $invalid[] = $table;
    }
}

if ($invalid !== []) {
    throw new RuntimeException('ID belum valid: '.implode(', ', $invalid));
}

$groupId = DB::table('groups')
    ->whereNotExists(fn ($query) => $query
        ->selectRaw('1')
        ->from('survey_sessions')
        ->whereColumn('survey_sessions.group_id', 'groups.id'))
    ->value('id');
$formTypeId = DB::table('form_types')->where('id', 1)->value('id')
    ?? DB::table('form_types')->value('id');
$questionTypeId = DB::table('question_types')->value('id');

if (! $groupId || ! $formTypeId || ! $questionTypeId) {
    throw new RuntimeException('Master group/form type/question type belum tersedia untuk smoke test.');
}

DB::beginTransaction();
$temporaryFiles = [];

try {
    $now = now();
    $suffix = 'SMOKE-'.now()->format('YmdHisv');

    $formId = DB::table('forms')->insertGetId([
        'group_id' => $groupId,
        'no_urut' => 999999,
        'name' => "{$suffix}-FORM",
        'formtype_id' => $formTypeId,
        'created_at' => $now,
        'updated_at' => $now,
    ]);

    $questionId = DB::table('questions')->insertGetId([
        'group_id' => $groupId,
        'form_id' => $formId,
        'no_header' => 'T',
        'no' => 999999,
        'name' => "{$suffix}-QUESTION",
        'questiontype_id' => $questionTypeId,
        'created_at' => $now,
        'updated_at' => $now,
    ]);

    $optionId = DB::table('options')->insertGetId([
        'question_id' => $questionId,
        'no' => 999999,
        'answer_text' => "{$suffix}-OPTION",
        'answer_text2' => null,
        'has_child' => 0,
        'created_at' => $now,
        'updated_at' => $now,
    ]);

    DB::table('forms')->where('id', $formId)->update(['name' => "{$suffix}-FORM-EDIT"]);
    DB::table('questions')->where('id', $questionId)->update(['name' => "{$suffix}-QUESTION-EDIT"]);
    DB::table('options')->where('id', $optionId)->update(['answer_text' => "{$suffix}-OPTION-EDIT"]);

    $valid = DB::table('forms')->where('id', $formId)->where('name', "{$suffix}-FORM-EDIT")->exists()
        && DB::table('questions')->where('id', $questionId)->where('name', "{$suffix}-QUESTION-EDIT")->exists()
        && DB::table('options')->where('id', $optionId)->where('answer_text', "{$suffix}-OPTION-EDIT")->exists();

    if (! $valid) {
        throw new RuntimeException('Verifikasi update Form/Pertanyaan/Option gagal.');
    }

    $testingDirectory = storage_path('framework/testing');
    if (! is_dir($testingDirectory)) {
        mkdir($testingDirectory, 0775, true);
    }

    $formImportName = "{$suffix}-FORM-IMPORT";
    $formSpreadsheet = new Spreadsheet();
    $formSpreadsheet->getActiveSheet()->fromArray([
        ['group_id', 'no_urut', 'name', 'formtype_id'],
        [$groupId, 999998, $formImportName, $formTypeId],
    ]);
    $formImportPath = tempnam($testingDirectory, 'form-import-');
    $temporaryFiles[] = $formImportPath;
    (new Xlsx($formSpreadsheet))->save($formImportPath);
    $formSpreadsheet->disconnectWorksheets();

    $formRequest = Request::create('/forms/import', 'POST', [], [], [
        'file' => new UploadedFile(
            $formImportPath,
            'form-import.xlsx',
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            null,
            true
        ),
    ]);
    (new FormController())->import($formRequest);

    if (! DB::table('forms')->where('name', $formImportName)->exists()) {
        throw new RuntimeException('Import Excel Form tidak menghasilkan data.');
    }

    $questionImportName = "{$suffix}-QUESTION-IMPORT";
    $optionImportName = "{$suffix}-OPTION-IMPORT";
    $questionSpreadsheet = new Spreadsheet();
    $questionSheet = $questionSpreadsheet->getActiveSheet();
    $questionSheet->setTitle('INPUT_PERTANYAAN');
    $questionSheet->fromArray([
        ['kode_pertanyaan', 'form', 'no_header', 'no', 'nama_pertanyaan', 'tipe_pertanyaan'],
        ['Q-SMOKE', $formId, '', 999998, $questionImportName, $questionTypeId.' - Test'],
    ]);
    $optionSheet = $questionSpreadsheet->createSheet();
    $optionSheet->setTitle('INPUT_OPTIONS');
    $optionSheet->fromArray([
        ['kode_pertanyaan', 'urutan', 'nama_option', 'has_child', 'answer_text2'],
        ['Q-SMOKE', 1, $optionImportName, '0 - Tidak', ''],
    ]);
    $masterSheet = $questionSpreadsheet->createSheet();
    $masterSheet->setTitle('MASTER_FORM');
    $masterSheet->setCellValue('A2', $formId);
    $questionImportPath = tempnam($testingDirectory, 'question-import-');
    $temporaryFiles[] = $questionImportPath;
    (new Xlsx($questionSpreadsheet))->save($questionImportPath);
    $questionSpreadsheet->disconnectWorksheets();

    $questionRequest = Request::create('/questions/import', 'POST', [
        'group_id' => $groupId,
        'form_id' => $formId,
    ], [], [
        'file' => new UploadedFile(
            $questionImportPath,
            'question-import.xlsx',
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            null,
            true
        ),
    ]);
    (new QuestionController())->import($questionRequest, $formId);

    $importedQuestionId = DB::table('questions')->where('name', $questionImportName)->value('id');
    if (! $importedQuestionId || ! DB::table('options')
        ->where('question_id', $importedQuestionId)
        ->where('answer_text', $optionImportName)
        ->exists()) {
        throw new RuntimeException('Import Excel Pertanyaan/Option tidak menghasilkan data lengkap.');
    }

    $bulkDeleteRequest = Request::create('/questions/bulk-delete', 'DELETE', [
        'form_id' => $formId,
        'ids' => [$importedQuestionId],
    ]);
    (new QuestionController())->bulkDelete($bulkDeleteRequest);

    if (
        DB::table('questions')->where('id', $importedQuestionId)->exists()
        || DB::table('options')->where('question_id', $importedQuestionId)->exists()
    ) {
        throw new RuntimeException('Bulk delete Pertanyaan/Option gagal membersihkan data terpilih.');
    }

    $titleType = QuestionType::query()->findOrFail(QuestionType::TITLE_ONLY_ID);
    $questionTypeRequest = Request::create('/questtype/'.$titleType->id, 'PUT', [
        'name' => "{$suffix}-TITLE-EDIT",
        'description' => 'Judul hasil smoke test',
    ]);
    (new QuestionTypeController())->update(
        $questionTypeRequest,
        new QuestionType(),
        $titleType->id
    );
    $titleType->refresh();

    if ($titleType->name !== "{$suffix}-TITLE-EDIT" || ! $titleType->isTitleOnly()) {
        throw new RuntimeException('Edit Question Type gagal atau atribut judul hilang.');
    }

    $formTypeRequest = Request::create('/formtype/'.$formTypeId, 'PUT', [
        'name' => "{$suffix}-FORM-TYPE-EDIT",
        'description' => 'Form type hasil smoke test',
    ]);
    (new FormTypeController())->update($formTypeRequest, $formTypeId);
    $editedFormType = FormType::query()->findOrFail($formTypeId);

    if ($editedFormType->name !== "{$suffix}-FORM-TYPE-EDIT") {
        throw new RuntimeException('Edit Form Type gagal.');
    }

    echo "OK: ID valid; CRUD/import, bulk delete, serta edit Form Type dan Question Type berhasil.\n";
} finally {
    DB::rollBack();

    foreach ($temporaryFiles as $temporaryFile) {
        if (is_string($temporaryFile) && str_starts_with($temporaryFile, storage_path('framework/testing')) && is_file($temporaryFile)) {
            unlink($temporaryFile);
        }
    }
}
