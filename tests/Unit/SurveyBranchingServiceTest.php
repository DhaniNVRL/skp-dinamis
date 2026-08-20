<?php

namespace Tests\Unit;

use App\Models\Form;
use App\Models\Option;
use App\Models\Question;
use App\Services\SurveyBranchingService;
use Tests\TestCase;

class SurveyBranchingServiceTest extends TestCase
{
    public function test_yes_shows_numbered_children_and_no_hides_them(): void
    {
        $yes = new Option(['answer_text' => 'Ya']);
        $yes->id = 501;
        $no = new Option(['answer_text' => 'Tidak']);
        $no->id = 502;

        $parent = $this->question(101, '1');
        $parent->setRelation('options', collect([$yes, $no]));
        $childA = $this->question(102, '1a');
        $childB = $this->question(103, '1b');
        $next = $this->question(104, '3');

        foreach ([$childA, $childB, $next] as $question) {
            $question->setRelation('options', collect());
        }

        $form = new Form(['formtype_id' => 1]);
        $form->id = 10;
        $form->setRelation('questions', collect([$parent, $childA, $childB, $next]));
        $service = new SurveyBranchingService();

        $definition = $service->definitions($form)->first();
        $this->assertSame([102, 103], $definition['dependent_question_ids']);
        $this->assertSame([], $service->hiddenQuestionIds($form, [101 => ['value' => 501]])->all());
        $this->assertSame([102, 103], $service->hiddenQuestionIds($form, [101 => ['value' => 502]])->all());
    }

    public function test_multiple_rules_can_show_and_skip_questions_for_different_options(): void
    {
        $definitions = collect([
            [
                'parent_id' => 101,
                'affirmative_option_ids' => [501],
                'dependent_question_ids' => [102, 103],
                'skipped_question_ids' => [104],
            ],
            [
                'parent_id' => 101,
                'affirmative_option_ids' => [502],
                'dependent_question_ids' => [104],
                'skipped_question_ids' => [103],
            ],
        ]);
        $service = new class($definitions) extends SurveyBranchingService {
            public function __construct(private readonly \Illuminate\Support\Collection $rules) {}
            public function definitions(Form $form): \Illuminate\Support\Collection
            {
                return $this->rules;
            }
        };
        $form = new Form(['formtype_id' => 1]);

        $this->assertSame(
            [104],
            $service->hiddenQuestionIds($form, [101 => ['value' => 501]])->all()
        );
        $this->assertSame(
            [102, 103],
            $service->hiddenQuestionIds($form, [101 => ['value' => 502]])->all()
        );
    }
    private function question(int $id, string $number): Question
    {
        $question = new Question([
            'no_header' => '',
            'no' => $number,
            'name' => 'Pertanyaan '.$number,
            'questiontype_id' => 1,
        ]);
        $question->id = $id;

        return $question;
    }
}
