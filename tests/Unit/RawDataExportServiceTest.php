<?php

namespace Tests\Unit;

use App\Models\Form;
use App\Models\Competitor;
use App\Models\Option;
use App\Models\Question;
use App\Services\RawDataExportService;
use App\Models\Activity;
use App\Models\Group;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PHPUnit\Framework\Attributes\Test;
use ReflectionMethod;
use Tests\TestCase;

class RawDataExportServiceTest extends TestCase
{
    #[Test]
    public function streaming_package_produces_a_valid_xlsx_workbook(): void
    {
        $service = new RawDataExportService();
        $sheetPath = tempnam(sys_get_temp_dir(), 'raw_sheet_test_');
        $xlsxPath = tempnam(sys_get_temp_dir(), 'raw_xlsx_test_');
        file_put_contents(
            $sheetPath,
            '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            .'<worksheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main">'
            .'<sheetData><row r="1"><c r="A1" t="inlineStr"><is><t>id_user</t></is></c></row>'
            .'<row r="2"><c r="A2"><v>149</v></c></row></sheetData></worksheet>'
        );

        $method = new ReflectionMethod(RawDataExportService::class, 'packageXlsx');
        $method->invoke(
            $service,
            $xlsxPath,
            $sheetPath,
            new Activity(['name' => 'Aktivitas']),
            new Group(['name' => 'Group'])
        );

        $spreadsheet = IOFactory::load($xlsxPath);
        $this->assertSame(
            'id_user',
            $spreadsheet->getActiveSheet()->getCell('A1')->getValue()->getPlainText()
        );
        $this->assertSame(149, $spreadsheet->getActiveSheet()->getCell('A2')->getValue());

        $spreadsheet->disconnectWorksheets();
        @unlink($sheetPath);
        @unlink($xlsxPath);
    }

    public function test_question_codes_follow_reference_raw_data_format(): void
    {
        $method = new ReflectionMethod(RawDataExportService::class, 'questionCode');
        $service = new RawDataExportService();

        $assessment = new Form(['formtype_id' => 3]);
        $assessment->id = 20;
        $assessmentQuestion = new Question(['no_header' => 'A', 'no' => '1', 'name' => 'Atribut']);

        $general = new Form(['formtype_id' => 1]);
        $general->id = 30;
        $generalQuestion = new Question(['no_header' => 'E', 'no' => '3', 'name' => '10. Pertanyaan tambahan']);

        $this->assertSame('BA1', $method->invoke($service, $assessment, $assessmentQuestion, null));
        $this->assertSame('E3.10', $method->invoke($service, $general, $generalQuestion, 10));
    }

    public function test_two_level_headers_group_columns_by_form_name(): void
    {
        $method = new ReflectionMethod(RawDataExportService::class, 'headerLayout');
        $service = new RawDataExportService();
        $descriptors = [
            ['header' => 'id_user', 'section_key' => 'respondent', 'form_name' => 'DATA RESPONDEN'],
            ['header' => 'No', 'section_key' => 'respondent', 'form_name' => 'DATA RESPONDEN'],
            ['header' => 'E1_IPP', 'section_key' => 'form:10', 'form_name' => 'PERTANYAAN LAIN-LAIN 1'],
            ['header' => 'E2_IPP', 'section_key' => 'form:10', 'form_name' => 'PERTANYAAN LAIN-LAIN 1'],
            ['header' => 'A1', 'section_key' => 'form:11', 'form_name' => 'FORM KUESIONER UMUM'],
        ];

        [$formHeaders, $merges] = $method->invoke($service, $descriptors);

        $this->assertSame(
            ['DATA RESPONDEN', '', 'PERTANYAAN LAIN-LAIN 1', '', 'FORM KUESIONER UMUM'],
            $formHeaders
        );
        $this->assertSame(['A1:B1', 'C1:D1'], $merges);
    }

    public function test_comparison_form_exports_competitors_referenced_by_existing_answers(): void
    {
        $method = new ReflectionMethod(RawDataExportService::class, 'descriptors');
        $service = new RawDataExportService();

        $form = new Form(['name' => 'PERTANYAAN LAIN-LAIN 2', 'formtype_id' => 13]);
        $form->id = 90;
        $question = new Question([
            'no_header' => 'E',
            'no' => '1',
            'name' => 'Telephone',
            'questiontype_id' => 2,
        ]);
        $question->id = 1213;
        $question->setRelation('options', collect());
        $form->setRelation('questions', collect([$question]));
        $form->setRelation('competitors', collect());

        $competitor = new Competitor(['name' => 'PLN IP']);
        $competitor->id = 15;

        $descriptors = $method->invoke(
            $service,
            collect([$form]),
            0,
            collect([90 => collect([$competitor])])
        );

        $comparisonDescriptor = collect($descriptors)->firstWhere('header', 'E.1_PLN IP');

        $this->assertNotNull($comparisonDescriptor);
        $this->assertSame('PERTANYAAN LAIN-LAIN 2', $comparisonDescriptor['form_name']);
    }

    public function test_option_ids_are_exported_as_readable_answer_text(): void
    {
        $method = new ReflectionMethod(RawDataExportService::class, 'readableValue');
        $service = new RawDataExportService();
        $question = new Question();
        $option = new Option(['answer_text' => 'Sangat Baik']);
        $option->id = 231;
        $question->setRelation('options', collect([$option]));

        $this->assertSame('Sangat Baik', $method->invoke($service, 231, $question));
    }

    public function test_legacy_indonesian_assessment_payload_is_exported(): void
    {
        $method = new ReflectionMethod(RawDataExportService::class, 'answerField');
        $service = new RawDataExportService();
        $question = new Question();
        $question->setRelation('options', collect());
        $payload = [
            'kepentingan' => '5',
            'kinerja' => '2',
            'alasan' => 'Informasi perlu lebih transparan',
        ];

        $this->assertSame(5, $method->invoke($service, $payload, 'importance', $question));
        $this->assertSame(2, $method->invoke($service, $payload, 'performance', $question));
        $this->assertSame(
            'Informasi perlu lebih transparan',
            $method->invoke($service, $payload, 'reason', $question)
        );
    }

    public function test_current_ranking_payload_exports_option_text_and_child_reason(): void
    {
        $method = new ReflectionMethod(RawDataExportService::class, 'answerField');
        $service = new RawDataExportService();
        $question = new Question();
        $option = new Option([
            'answer_text' => 'Ketersediaan dan kelengkapan teknologi',
            'has_child' => 1,
        ]);
        $option->id = 231;
        $question->setRelation('options', collect([$option]));

        $payload = [
            'value' => [
                1 => [
                    'option_id' => '231',
                    'child' => 'Alasan ranking pertama',
                ],
            ],
        ];

        $this->assertSame(
            'Ketersediaan dan kelengkapan teknologi',
            $method->invoke($service, $payload, 'ranking_value', $question, 1)
        );
        $this->assertSame(
            'Alasan ranking pertama',
            $method->invoke($service, $payload, 'ranking_child', $question, 1)
        );
    }

    public function test_legacy_ranking_value_payload_remains_supported(): void
    {
        $method = new ReflectionMethod(RawDataExportService::class, 'answerField');
        $service = new RawDataExportService();
        $question = new Question();
        $option = new Option(['answer_text' => 'Kemudahan informasi']);
        $option->id = 225;
        $question->setRelation('options', collect([$option]));

        $payload = [1 => ['value' => 225]];

        $this->assertSame(
            'Kemudahan informasi',
            $method->invoke($service, $payload, 'ranking_value', $question, 1)
        );
    }

    public function test_double_encoded_legacy_ranking_payload_remains_supported(): void
    {
        $method = new ReflectionMethod(RawDataExportService::class, 'answerField');
        $service = new RawDataExportService();
        $question = new Question();
        $option = new Option(['answer_text' => 'Ketersediaan teknologi']);
        $option->id = 231;
        $question->setRelation('options', collect([$option]));

        $payload = json_encode(json_encode([
            1 => ['value' => '231', 'child' => null],
        ]));

        $this->assertSame(
            'Ketersediaan teknologi',
            $method->invoke($service, $payload, 'ranking_value', $question, 1)
        );
    }
}
