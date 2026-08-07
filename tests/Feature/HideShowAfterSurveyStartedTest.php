<?php

namespace Tests\Feature;

use App\Http\Controllers\SubUnitQuestionController;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class HideShowAfterSurveyStartedTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Schema::create('forms', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('group_id');
            $table->timestamps();
        });

        Schema::create('questions', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('form_id');
            $table->timestamps();
        });

        Schema::create('units', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('group_id');
            $table->timestamps();
        });

        Schema::create('subunits', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('unit_id');
            $table->timestamps();
        });

        Schema::create('subunit_questions', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('form_id');
            $table->unsignedBigInteger('question_id');
            $table->unsignedBigInteger('subunit_id');
            $table->timestamps();
        });

        Schema::create('survey_sessions', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('group_id');
            $table->timestamps();
        });
    }

    protected function tearDown(): void
    {
        Schema::dropIfExists('survey_sessions');
        Schema::dropIfExists('subunit_questions');
        Schema::dropIfExists('subunits');
        Schema::dropIfExists('units');
        Schema::dropIfExists('questions');
        Schema::dropIfExists('forms');

        parent::tearDown();
    }

    public function test_question_can_be_shown_after_group_survey_has_started(): void
    {
        DB::table('forms')->insert(['id' => 10, 'group_id' => 20]);
        DB::table('questions')->insert(['id' => 30, 'form_id' => 10]);
        DB::table('units')->insert(['id' => 40, 'group_id' => 20]);
        DB::table('subunits')->insert(['id' => 50, 'unit_id' => 40]);
        DB::table('survey_sessions')->insert(['id' => 60, 'group_id' => 20]);

        $request = Request::create('/subunit-questions/toggle', 'POST', [
            'form_id' => 10,
            'question_id' => 30,
            'subunit_ids' => [50],
            'is_active' => true,
        ]);

        $response = app(SubUnitQuestionController::class)->toggle($request);

        $this->assertSame(200, $response->status());
        $this->assertTrue($response->getData(true)['is_active']);
        $this->assertDatabaseHas('subunit_questions', [
            'form_id' => 10,
            'question_id' => 30,
            'subunit_id' => 50,
        ]);
    }
}
