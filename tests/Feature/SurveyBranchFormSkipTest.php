<?php

namespace Tests\Feature;

use App\Models\Form;
use App\Services\SurveyBranchingService;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class SurveyBranchFormSkipTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Schema::create('forms', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('group_id');
            $table->unsignedBigInteger('formtype_id')->default(1);
            $table->unsignedInteger('no_urut')->default(1);
        });
        Schema::create('questions', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('group_id');
            $table->unsignedBigInteger('form_id');
            $table->string('no_header')->nullable();
            $table->string('no');
            $table->text('name');
            $table->unsignedBigInteger('questiontype_id')->default(3);
        });
        Schema::create('options', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('question_id');
            $table->string('answer_text');
        });
        Schema::create('survey_branch_rules', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('group_id');
            $table->unsignedBigInteger('parent_question_id');
            $table->unsignedBigInteger('affirmative_option_id');
            $table->unsignedBigInteger('skip_form_id')->nullable();
        });
        Schema::create('survey_branch_rule_skipped_forms', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('survey_branch_rule_id');
            $table->unsignedBigInteger('form_id');
        });
        Schema::create('answers', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('form_id');
            $table->unsignedBigInteger('question_id');
            $table->longText('answer')->nullable();
            $table->timestamps();
        });

        DB::table('forms')->insert([
            ['id' => 116, 'group_id' => 21, 'formtype_id' => 1, 'no_urut' => 6],
            ['id' => 117, 'group_id' => 21, 'formtype_id' => 11, 'no_urut' => 7],
        ]);
        DB::table('questions')->insert([
            'id' => 1359, 'group_id' => 21, 'form_id' => 116,
            'no_header' => 'L', 'no' => '1', 'name' => 'Pertanyaan pemicu',
        ]);
        DB::table('options')->insert([
            ['id' => 791, 'question_id' => 1359, 'answer_text' => 'Ya'],
            ['id' => 792, 'question_id' => 1359, 'answer_text' => 'Tidak'],
        ]);
        DB::table('survey_branch_rules')->insert([
            'id' => 1, 'group_id' => 21, 'parent_question_id' => 1359,
            'affirmative_option_id' => 792, 'skip_form_id' => 117,
        ]);
        DB::table('survey_branch_rule_skipped_forms')->insert([
            'id' => 1, 'survey_branch_rule_id' => 1, 'form_id' => 117,
        ]);
    }

    protected function tearDown(): void
    {
        foreach ([
            'answers', 'survey_branch_rule_skipped_forms', 'survey_branch_rules',
            'options', 'questions', 'forms',
        ] as $table) {
            Schema::dropIfExists($table);
        }

        parent::tearDown();
    }

    public function test_configured_form_is_skipped_when_trigger_option_is_selected(): void
    {
        DB::table('answers')->insert([
            'user_id' => 418, 'form_id' => 116, 'question_id' => 1359,
            'answer' => '792', 'created_at' => now(), 'updated_at' => now(),
        ]);

        $form = Form::query()->findOrFail(117);

        $this->assertTrue(app(SurveyBranchingService::class)->shouldSkipForm($form, 418));
    }

    public function test_configured_form_is_visible_when_another_option_is_selected(): void
    {
        DB::table('answers')->insert([
            'user_id' => 418, 'form_id' => 116, 'question_id' => 1359,
            'answer' => '791', 'created_at' => now(), 'updated_at' => now(),
        ]);

        $form = Form::query()->findOrFail(117);

        $this->assertFalse(app(SurveyBranchingService::class)->shouldSkipForm($form, 418));
    }
}
