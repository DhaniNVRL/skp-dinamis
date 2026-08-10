<?php

namespace Tests\Unit;

use App\Models\Form;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class QuestionDisplayOrderTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Schema::dropIfExists('questions');
        Schema::dropIfExists('forms');

        Schema::create('forms', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('formtype_id');
        });

        Schema::create('questions', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('form_id');
            $table->string('no_header')->nullable();
            $table->string('no');
            $table->string('name');
            $table->unsignedBigInteger('questiontype_id');
        });
    }

    protected function tearDown(): void
    {
        Schema::dropIfExists('questions');
        Schema::dropIfExists('forms');

        parent::tearDown();
    }

    public function test_general_questionnaire_uses_varchar_order_and_places_title_first_on_equal_number(): void
    {
        DB::table('forms')->insert(['id' => 1, 'formtype_id' => 1]);
        DB::table('questions')->insert([
            ['id' => 1, 'form_id' => 1, 'no_header' => 'D', 'no' => '1', 'name' => 'Jawaban D1', 'questiontype_id' => 1],
            ['id' => 2, 'form_id' => 1, 'no_header' => 'D', 'no' => '1', 'name' => 'Judul D1', 'questiontype_id' => 10],
            ['id' => 3, 'form_id' => 1, 'no_header' => 'D', 'no' => '2', 'name' => 'Jawaban D2', 'questiontype_id' => 1],
            ['id' => 4, 'form_id' => 1, 'no_header' => 'D', 'no' => '10', 'name' => 'Jawaban D10', 'questiontype_id' => 1],
        ]);

        $this->assertSame(
            ['Judul D1', 'Jawaban D1', 'Jawaban D2', 'Jawaban D10'],
            Form::query()->findOrFail(1)->questions->pluck('name')->all()
        );
    }

    public function test_prefixed_varchar_numbers_use_natural_order(): void
    {
        DB::table('forms')->insert(['id' => 3, 'formtype_id' => 1]);
        DB::table('questions')->insert([
            ['id' => 7, 'form_id' => 3, 'no_header' => 'C', 'no' => '10', 'name' => 'Pertanyaan C10', 'questiontype_id' => 1],
            ['id' => 8, 'form_id' => 3, 'no_header' => 'C', 'no' => '2', 'name' => 'Pertanyaan C2', 'questiontype_id' => 1],
            ['id' => 9, 'form_id' => 3, 'no_header' => 'C', 'no' => '1', 'name' => 'Pertanyaan C1', 'questiontype_id' => 1],
        ]);

        $this->assertSame(
            ['Pertanyaan C1', 'Pertanyaan C2', 'Pertanyaan C10'],
            Form::query()->findOrFail(3)->questions->pluck('name')->all()
        );
    }

    public function test_special_form_places_question_type_one_title_before_same_number(): void
    {
        DB::table('forms')->insert(['id' => 2, 'formtype_id' => 2]);
        DB::table('questions')->insert([
            ['id' => 5, 'form_id' => 2, 'no_header' => 'A', 'no' => '1', 'name' => 'Pertanyaan A1', 'questiontype_id' => 2],
            ['id' => 6, 'form_id' => 2, 'no_header' => 'A', 'no' => '1', 'name' => 'Judul A1', 'questiontype_id' => 1],
        ]);

        $this->assertSame(
            ['Judul A1', 'Pertanyaan A1'],
            Form::query()->findOrFail(2)->questions->pluck('name')->all()
        );
    }
}
