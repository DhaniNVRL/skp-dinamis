<?php

namespace Tests\Unit;

use App\Models\Form;
use App\Models\FormType;
use App\Models\Question;
use Tests\TestCase;

class CustomerAssessmentWithoutZeroAdminCrudTest extends TestCase
{
    public function test_create_and_edit_form_dropdowns_contain_both_without_zero_types(): void
    {
        $formTypes = collect([
            new FormType([
                'id' => 15,
                'name' => 'Form Penilaian Pelanggan',
                'description' => 'Skala 1-5 Tanpa Nilai 0',
            ]),
            new FormType([
                'id' => 16,
                'name' => 'Form Penilaian Pelanggan',
                'description' => 'Skala 1-7 Tanpa Nilai 0',
            ]),
        ]);

        foreach ($formTypes as $index => $formType) {
            $formType->id = 15 + $index;
        }

        $createHtml = view(
            'admin.units.question.templates.create-form-row',
            compact('formTypes')
        )->render();
        $editHtml = view(
            'admin.units.question.modals.edit-form',
            compact('formTypes')
        )->render();

        foreach ([15, 16] as $id) {
            $this->assertStringContainsString('value="'.$id.'"', $createHtml);
            $this->assertStringContainsString('value="'.$id.'"', $editHtml);
        }
        $this->assertStringContainsString('Skala 1-5 Tanpa Nilai 0', $createHtml);
        $this->assertStringContainsString('Skala 1-7 Tanpa Nilai 0', $editHtml);
    }

    public function test_admin_form_cards_render_add_and_edit_controls_for_types_15_and_16(): void
    {
        foreach ([15, 16] as $formTypeId) {
            $form = new Form([
                'group_id' => 1,
                'no_urut' => $formTypeId,
                'name' => 'Penilaian Tanpa Nol',
                'formtype_id' => $formTypeId,
            ]);
            $form->id = 100 + $formTypeId;
            $form->setRelation('questions', collect());
            $form->setRelation('description', null);
            $form->setRelation('formtype', new FormType([
                'name' => 'Form Penilaian Pelanggan',
                'description' => $formTypeId === 15
                    ? 'Skala 1-5 Tanpa Nilai 0'
                    : 'Skala 1-7 Tanpa Nilai 0',
            ]));

            $html = view('admin.units.question.partials.form-card', [
                'forms' => collect([$form]),
                'competitors' => collect(),
            ])->render();

            $this->assertStringContainsString('data-modal-open="editFormModal"', $html);
            $this->assertStringContainsString('data-modal-open="createQuestionModal"', $html);
            $this->assertStringNotContainsString('Form type tidak ditemukan.', $html);
        }
    }

    public function test_without_zero_question_templates_are_available_for_add_and_edit(): void
    {
        $html = view(
            'admin.units.question.templates.forms.customer-assessment-without-zero'
        )->render();

        $this->assertStringContainsString('customerAssessment15WithoutZeroTypeOptions', $html);
        $this->assertStringContainsString('customerAssessment17WithoutZeroTypeOptions', $html);
        $this->assertStringContainsString('tanpa nilai 0', $html);
    }

    public function test_admin_unit_preview_omits_zero_only_for_without_zero_form_types(): void
    {
        $question = new Question([
            'group_id' => 1,
            'form_id' => 1,
            'no_header' => 'A',
            'no' => '1',
            'name' => 'Kinerja HSE',
            'questiontype_id' => 2,
        ]);
        $question->id = 501;
        $question->setRelation('options', collect());

        foreach ([
            2 => true,
            3 => true,
            15 => false,
            16 => false,
        ] as $formTypeId => $expectsZero) {
            $form = new Form([
                'group_id' => 1,
                'name' => 'Penilaian Pelanggan',
                'formtype_id' => $formTypeId,
            ]);
            $form->id = 600 + $formTypeId;

            $html = view('admin.units.question.partials.forms.question-card', [
                'form' => $form,
                'question' => $question,
                'competitors' => collect(),
            ])->render();

            $this->assertStringContainsString('value="1"', $html);
            $this->assertSame(
                $expectsZero,
                str_contains($html, 'value="0"'),
                'Nilai 0 tidak sesuai untuk formtype_id '.$formTypeId
            );
        }
    }
}
