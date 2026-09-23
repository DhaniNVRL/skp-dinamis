<?php

namespace Tests\Feature;

use App\Http\Controllers\UnitFormVisibilityController;
use App\Models\Form;
use App\Services\SurveyBranchingService;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class UnitFormVisibilityTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Schema::create('groups', function (Blueprint $table): void {
            $table->id();
        });
        Schema::create('units', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('group_id');
        });
        Schema::create('forms', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('group_id');
            $table->unsignedInteger('no_urut')->default(1);
            $table->unsignedBigInteger('formtype_id')->default(1);
            $table->string('name');
        });
        Schema::create('user_profiles', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('unit_id')->nullable();
        });
        Schema::create('unit_form_visibilities', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('unit_id');
            $table->unsignedBigInteger('form_id');
            $table->boolean('is_visible')->default(true);
            $table->timestamps();
            $table->unique(['unit_id', 'form_id']);
        });

        DB::table('groups')->insert(['id' => 1]);
        DB::table('units')->insert(['id' => 10, 'group_id' => 1]);
        DB::table('forms')->insert([
            'id' => 20,
            'group_id' => 1,
            'no_urut' => 1,
            'formtype_id' => 1,
            'name' => 'Form Uji',
        ]);
        DB::table('user_profiles')->insert([
            'id' => 1,
            'user_id' => 30,
            'unit_id' => 10,
        ]);
    }

    protected function tearDown(): void
    {
        Schema::dropIfExists('unit_form_visibilities');
        Schema::dropIfExists('user_profiles');
        Schema::dropIfExists('forms');
        Schema::dropIfExists('units');
        Schema::dropIfExists('groups');

        parent::tearDown();
    }

    public function test_hidden_form_is_skipped_for_users_in_the_configured_unit(): void
    {
        $request = Request::create('/unit-form-visibility/toggle', 'POST', [
            'unit_id' => 10,
            'form_id' => 20,
            'is_visible' => false,
        ]);

        $response = app(UnitFormVisibilityController::class)->toggle($request);

        $this->assertFalse($response->getData(true)['is_visible']);
        $this->assertDatabaseHas('unit_form_visibilities', [
            'unit_id' => 10,
            'form_id' => 20,
            'is_visible' => 0,
        ]);

        $form = Form::query()->findOrFail(20);
        $this->assertTrue(app(SurveyBranchingService::class)->shouldSkipForm($form, 30));
    }

    public function test_form_is_visible_by_default_and_can_be_shown_again(): void
    {
        $form = Form::query()->findOrFail(20);
        $this->assertFalse(app(SurveyBranchingService::class)->shouldSkipForm($form, 30));

        DB::table('unit_form_visibilities')->insert([
            'unit_id' => 10,
            'form_id' => 20,
            'is_visible' => false,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $request = Request::create('/unit-form-visibility/toggle', 'POST', [
            'unit_id' => 10,
            'form_id' => 20,
            'is_visible' => true,
        ]);

        $response = app(UnitFormVisibilityController::class)->toggle($request);
        $this->assertTrue($response->getData(true)['is_visible']);
    }
}
