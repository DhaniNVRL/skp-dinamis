<?php

namespace Tests\Unit;

use App\Models\Form;
use App\Models\Option;
use App\Models\Question;
use App\Models\QuestionType;
use Illuminate\Support\ViewErrorBag;
use Tests\TestCase;

class GeneralQuestionnaireTitleTypeTest extends TestCase
{
    public function test_title_only_type_exists_and_is_recognized(): void
    {
        $type = new QuestionType([
            'name' => QuestionType::TITLE_ONLY_NAME,
            'description' => 'Judul saja',
        ]);
        $type->id = QuestionType::TITLE_ONLY_ID;

        $this->assertTrue($type->isTitleOnly());
        $this->assertFalse((new QuestionType(['name' => 'Jawaban singkat']))->isTitleOnly());
    }

    public function test_general_questionnaire_renders_title_without_answer_input(): void
    {
        $type = new QuestionType([
            'name' => QuestionType::TITLE_ONLY_NAME,
            'description' => 'Judul saja',
        ]);
        $type->id = QuestionType::TITLE_ONLY_ID;

        $question = new Question([
            'no_header' => null,
            'no' => 1,
            'name' => 'Bagian Identitas Responden',
            'questiontype_id' => $type->id,
        ]);
        $question->id = 999999;
        $question->setRelation('questiontype', $type);

        $html = view('user.survey.forms.general-questionnaire', [
            'questions' => collect([$question]),
            'answerMap' => [],
        ])->render();

        $this->assertStringContainsString('Bagian Identitas Responden', $html);
        $this->assertStringContainsString('data-question-title', $html);
        $this->assertStringNotContainsString('answers[999999]', $html);
        $this->assertStringNotContainsString('Pertanyaan ini wajib diisi.', $html);
    }

    public function test_general_questionnaire_admin_selector_contains_title_type(): void
    {
        $shortText = new QuestionType(['name' => 'Short Text', 'description' => 'Jawaban singkat']);
        $shortText->id = 1;
        $title = new QuestionType([
            'name' => QuestionType::TITLE_ONLY_NAME,
            'description' => 'Judul saja',
        ]);
        $title->id = 10;

        $html = view('admin.units.question.templates.forms.general-questionnaire', [
            'questionTypes' => collect([$shortText, $title]),
        ])->render();

        $this->assertStringContainsString('Judul (Tanpa Jawaban)', $html);
        $this->assertStringContainsString('data-title-only="1"', $html);
        $this->assertStringContainsString('value="10"', $html);
    }

    public function test_subunit_question_show_renders_title_without_unsupported_warning_or_answer(): void
    {
        $question = new Question([
            'no_header' => 'D',
            'no' => '1',
            'name' => 'Bagaimana penilaian terhadap proses bisnis?',
            'questiontype_id' => QuestionType::TITLE_ONLY_ID,
        ]);
        $question->id = 999998;
        $question->setRelation('options', collect());

        $html = view('admin.subunit.show-question.forms.general-questionnaire', [
            'questions' => collect([$question]),
        ])->render();

        $this->assertStringContainsString('data-question-title', $html);
        $this->assertStringContainsString('Bagaimana penilaian terhadap proses bisnis?', $html);
        $this->assertStringNotContainsString('Question Type 10 belum didukung', $html);
        $this->assertStringNotContainsString('general_answers[999998]', $html);
        $this->assertStringNotContainsString('D1', $html);
        $this->assertStringNotContainsString('>Judul<', $html);
    }

    public function test_special_form_show_title_contains_only_its_name(): void
    {
        $form = new Form(['formtype_id' => 10]);
        $form->id = 777;

        $question = new Question([
            'form_id' => $form->id,
            'no_header' => 'A',
            'no' => '1',
            'name' => 'Arahan untuk responden',
            'questiontype_id' => 1,
        ]);
        $question->id = 999997;

        $html = view('admin.subunit.show-question.forms.suggestions', [
            'form' => $form,
            'questions' => collect([$question]),
        ])->render();

        $this->assertStringContainsString('Arahan untuk responden', $html);
        $this->assertStringNotContainsString('A1', $html);
        $this->assertStringNotContainsString('Judul Pertanyaan', $html);
    }

    public function test_dropdown_type_is_rendered_for_admin_preview_and_user_survey(): void
    {
        $type = new QuestionType([
            'name' => 'Dropdown',
            'description' => 'Pilih dari daftar',
        ]);
        $type->id = 5;

        $question = new Question([
            'no_header' => 'A',
            'no' => '4',
            'name' => 'Location',
            'questiontype_id' => 5,
        ]);
        $question->id = 999996;
        $question->setRelation('questiontype', $type);

        $firstOption = new Option([
            'question_id' => $question->id,
            'no' => 1,
            'answer_text' => 'Jakarta',
            'answer_text2' => 'Tuliskan lokasi lainnya',
            'has_child' => 1,
        ]);
        $firstOption->id = 8101;

        $secondOption = new Option([
            'question_id' => $question->id,
            'no' => 2,
            'answer_text' => 'Surabaya',
            'has_child' => 0,
        ]);
        $secondOption->id = 8102;

        $question->setRelation('options', collect([$firstOption, $secondOption]));

        $adminHtml = view('admin.subunit.show-question.forms.general-questionnaire', [
            'questions' => collect([$question]),
        ])->render();

        $userHtml = view('user.survey.forms.general-questionnaire', [
            'questions' => collect([$question]),
            'answerMap' => [],
            'errors' => new ViewErrorBag(),
        ])->render();

        foreach ([$adminHtml, $userHtml] as $html) {
            $this->assertStringContainsString('<select', $html);
            $this->assertStringContainsString('Jakarta', $html);
            $this->assertStringContainsString('Surabaya', $html);
            $this->assertStringNotContainsString('Type 5 belum didukung', $html);
            $this->assertStringNotContainsString('Tipe pertanyaan 5 belum didukung', $html);
            $this->assertStringContainsString('data-has-child="1"', $html);
            $this->assertStringContainsString('Tuliskan lokasi lainnya', $html);
        }

        $this->assertStringContainsString('answers[999996][value]', $userHtml);
        $this->assertStringContainsString('answers[999996][child][8101]', $userHtml);
        $this->assertStringContainsString('data-child-input', $userHtml);
        $this->assertStringContainsString('general_child_answers[999996][global][8101]', $adminHtml);
    }
}
