<?php

namespace Tests\Feature;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class QuestionAnswerConsistencyTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Schema::create('groups', fn (Blueprint $table) => $table->id());
        Schema::create('question_types', fn (Blueprint $table) => $table->id());
        Schema::create('forms', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('group_id');
            $table->unsignedBigInteger('formtype_id');
        });
        Schema::create('questions', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('group_id');
            $table->unsignedBigInteger('form_id');
            $table->string('no_header')->nullable();
            $table->string('no');
            $table->text('name');
            $table->unsignedBigInteger('questiontype_id');
            $table->timestamps();
        });
        Schema::create('answers', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('question_id');
        });
        Schema::create('options', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('question_id');
        });
        Schema::create('subunit_questions', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('question_id');
        });

        DB::table('groups')->insert(['id' => 10]);
        DB::table('question_types')->insert(['id' => 2]);
        DB::table('forms')->insert([
            'id' => 20,
            'group_id' => 10,
            'formtype_id' => 2,
        ]);
    }

    protected function tearDown(): void
    {
        Schema::dropIfExists('subunit_questions');
        Schema::dropIfExists('options');
        Schema::dropIfExists('answers');
        Schema::dropIfExists('questions');
        Schema::dropIfExists('forms');
        Schema::dropIfExists('question_types');
        Schema::dropIfExists('groups');

        parent::tearDown();
    }

    public function test_edit_question_deletes_only_answers_for_that_question(): void
    {
        $this->insertQuestion(30, 'Pertanyaan lama');
        $this->insertQuestion(31, 'Pertanyaan lain');
        DB::table('answers')->insert([
            ['id' => 1, 'question_id' => 30],
            ['id' => 2, 'question_id' => 31],
        ]);

        $response = $this->withoutMiddleware()->put(
            route('question.update', 30),
            [
                'group_id' => 10,
                'form_id' => 20,
                'no_header' => 'A',
                'no' => '1',
                'name' => 'Pertanyaan baru',
                'questiontype_id' => 2,
            ]
        );

        $response->assertRedirect();
        $this->assertDatabaseHas('questions', ['id' => 30, 'name' => 'Pertanyaan baru']);
        $this->assertDatabaseMissing('answers', ['question_id' => 30]);
        $this->assertDatabaseHas('answers', ['question_id' => 31]);
    }

    public function test_create_question_accepts_alphanumeric_number(): void
    {
        $response = $this->withoutMiddleware()->post(route('question.store'), [
            'group_id' => 10,
            'form_id' => 20,
            'questions' => [[
                'no_header' => 'L',
                'no' => 'L1A',
                'name' => 'Pertanyaan alfanumerik',
                'questiontype_id' => 2,
            ]],
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('questions', [
            'no_header' => 'L',
            'no' => 'L1A',
            'name' => 'Pertanyaan alfanumerik',
        ]);
    }

    public function test_delete_question_also_deletes_its_answers_options_and_visibility(): void
    {
        $this->insertQuestion(30, 'Pertanyaan dihapus');
        DB::table('answers')->insert(['id' => 1, 'question_id' => 30]);
        DB::table('options')->insert(['id' => 1, 'question_id' => 30]);
        DB::table('subunit_questions')->insert(['id' => 1, 'question_id' => 30]);

        $response = $this
            ->withoutMiddleware()
            ->delete(route('question.destroy', 30));

        $response->assertRedirect();
        $this->assertDatabaseMissing('questions', ['id' => 30]);
        $this->assertDatabaseMissing('answers', ['question_id' => 30]);
        $this->assertDatabaseMissing('options', ['question_id' => 30]);
        $this->assertDatabaseMissing('subunit_questions', ['question_id' => 30]);
    }

    private function insertQuestion(int $id, string $name): void
    {
        DB::table('questions')->insert([
            'id' => $id,
            'group_id' => 10,
            'form_id' => 20,
            'no_header' => 'A',
            'no' => (string) ($id - 29),
            'name' => $name,
            'questiontype_id' => 2,
        ]);
    }
}
