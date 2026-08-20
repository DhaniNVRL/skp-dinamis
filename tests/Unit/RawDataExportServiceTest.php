<?php

namespace Tests\Unit;

use App\Models\Form;
use App\Models\Option;
use App\Models\Question;
use App\Services\RawDataExportService;
use ReflectionMethod;
use Tests\TestCase;

class RawDataExportServiceTest extends TestCase
{
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
}
