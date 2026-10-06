<?php

namespace Tests\Unit;

use App\Models\Form;
use App\Models\Group;
use App\Models\Question;
use App\Services\QuestionTemplateSpreadsheet;
use Illuminate\Support\Collection;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class CustomerAssessmentReasonPromptTest extends TestCase
{
    #[Test]
    public function question_can_store_a_custom_reason_prompt(): void
    {
        $question = new Question([
            'questiontype_id' => 3,
            'reason_prompt' => 'Jelaskan alasan penilaian Kinerja yang diberikan.',
        ]);

        $this->assertSame(
            'Jelaskan alasan penilaian Kinerja yang diberikan.',
            $question->reason_prompt
        );
    }

    #[Test]
    public function excel_template_contains_reason_prompt_column(): void
    {
        $form = new Form([
            'id' => 99,
            'group_id' => 1,
            'name' => 'Penilaian Pelanggan',
            'formtype_id' => 17,
        ]);
        $form->setRelation('group', new Group(['name' => 'Contoh Group']));

        $types = new Collection([
            ['id' => 3, 'name' => 'Kepentingan & Kinerja dengan Alasan', 'description' => ''],
        ]);

        $spreadsheet = (new QuestionTemplateSpreadsheet)->create($form, $types);
        $sheet = $spreadsheet->getSheetByName('INPUT_PERTANYAAN');

        $this->assertSame('narasi_alasan', $sheet->getCell('I1')->getValue());

        $spreadsheet->disconnectWorksheets();
    }

    #[Test]
    public function user_customer_view_uses_custom_prompt_with_a_fallback(): void
    {
        $view = file_get_contents(
            resource_path('views/user/survey/forms/partials/customer-assessment.blade.php')
        );

        $this->assertStringContainsString('$question->reason_prompt', $view);
        $this->assertStringContainsString('Komen/saran untuk perbaikan', $view);
    }
}
