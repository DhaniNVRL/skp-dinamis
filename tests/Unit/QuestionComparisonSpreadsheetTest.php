<?php

namespace Tests\Unit;

use App\Http\Controllers\QuestionController;
use App\Models\Form;
use App\Models\Group;
use App\Services\QuestionTemplateSpreadsheet;
use Illuminate\Validation\ValidationException;
use ReflectionMethod;
use Tests\TestCase;

class QuestionComparisonSpreadsheetTest extends TestCase
{
    public function test_question_template_contains_comparison_columns_and_dropdown(): void
    {
        $form = new Form([
            'group_id' => 1,
            'formtype_id' => 2,
            'name' => 'Penilaian Pelanggan',
        ]);
        $form->id = 10;
        $group = new Group(['name' => 'Group Uji']);
        $group->id = 1;
        $form->setRelation('group', $group);

        $spreadsheet = (new QuestionTemplateSpreadsheet())->create($form, collect([
            ['id' => 2, 'name' => 'Kepentingan dan Kinerja', 'description' => 'Penilaian'],
        ]));
        $sheet = $spreadsheet->getSheetByName('INPUT_PERTANYAAN');

        $this->assertSame('pembanding_aktif', $sheet->getCell('G1')->getValue());
        $this->assertSame('pertanyaan_pembanding', $sheet->getCell('H1')->getValue());
        $this->assertSame('"0 - Tidak,1 - Iya"', $sheet->getCell('G2')->getDataValidation()->getFormula1());
        $comparisonSheet = $spreadsheet->getSheetByName('INPUT_PEMBANDING');
        $this->assertNotNull($comparisonSheet);
        $this->assertSame('kode_pertanyaan', $comparisonSheet->getCell('A1')->getValue());
        $this->assertSame('urutan', $comparisonSheet->getCell('B1')->getValue());
        $this->assertSame('pilihan_pembanding', $comparisonSheet->getCell('C1')->getValue());

        $instructions = $spreadsheet->getSheetByName('PETUNJUK')->toArray();
        $this->assertStringContainsString('minimal dua pilihan', json_encode($instructions));
        $spreadsheet->disconnectWorksheets();
    }

    public function test_header_validation_accepts_new_and_legacy_templates_but_rejects_invalid_extension(): void
    {
        $method = new ReflectionMethod(new QuestionController(), 'validateQuestionHeaders');
        $base = ['kode_pertanyaan', 'form', 'no_header', 'no', 'nama_pertanyaan', 'tipe_pertanyaan'];

        $method->invoke(new QuestionController(), [$base]);
        $method->invoke(new QuestionController(), [[...$base, 'pembanding_aktif', 'pertanyaan_pembanding']]);
        $method->invoke(new QuestionController(), [[...$base, 'pembanding_aktif', 'pertanyaan_pembanding', 'pilihan_pembanding']]);

        $this->expectException(ValidationException::class);
        $method->invoke(new QuestionController(), [[...$base, 'kolom_salah']]);
    }
}
