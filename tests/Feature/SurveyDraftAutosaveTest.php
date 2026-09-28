<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class SurveyDraftAutosaveTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Schema::create('roles', function (Blueprint $table): void {
            $table->id();
            $table->string('name');
            $table->timestamps();
        });
        Schema::create('users', function (Blueprint $table): void {
            $table->id();
            $table->string('username');
            $table->string('password');
            $table->unsignedBigInteger('role_id');
            $table->rememberToken();
            $table->timestamps();
        });
        Schema::create('user_profiles', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('activity_id')->nullable();
            $table->unsignedBigInteger('group_id')->nullable();
            $table->unsignedBigInteger('unit_id')->nullable();
            $table->timestamps();
        });
        Schema::create('forms', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('group_id');
            $table->string('name')->nullable();
            $table->timestamps();
        });
        Schema::create('survey_sessions', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('activity_id')->nullable();
            $table->unsignedBigInteger('group_id')->nullable();
            $table->unsignedBigInteger('unit_id')->nullable();
            $table->unsignedBigInteger('current_form_id')->nullable();
            $table->string('status');
            $table->timestamp('started_at')->nullable();
            $table->timestamp('finished_at')->nullable();
            $table->timestamp('reopened_at')->nullable();
            $table->timestamps();
        });
        Schema::create('survey_drafts', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('form_id');
            $table->text('payload');
            $table->timestamps();
            $table->unique(['user_id', 'form_id']);
        });

        DB::table('roles')->insert([
            ['id' => 2, 'name' => 'Surveyor'],
            ['id' => 4, 'name' => 'User'],
        ]);
        DB::table('forms')->insert([
            'id' => 50,
            'group_id' => 10,
            'name' => 'Form Uji',
        ]);

        foreach ([[20, 2, 'surveyor-uji'], [40, 4, 'user-uji']] as [$id, $roleId, $username]) {
            DB::table('users')->insert([
                'id' => $id,
                'username' => $username,
                'password' => bcrypt('Password!123'),
                'role_id' => $roleId,
            ]);
            DB::table('user_profiles')->insert([
                'user_id' => $id,
                'activity_id' => 1,
                'group_id' => 10,
                'unit_id' => 100,
            ]);
            DB::table('survey_sessions')->insert([
                'user_id' => $id,
                'activity_id' => 1,
                'group_id' => 10,
                'unit_id' => 100,
                'current_form_id' => 50,
                'status' => 'in_progress',
            ]);
        }
    }

    protected function tearDown(): void
    {
        foreach (['survey_drafts', 'survey_sessions', 'forms', 'user_profiles', 'users', 'roles'] as $table) {
            Schema::dropIfExists($table);
        }

        parent::tearDown();
    }

    public function test_user_and_surveyor_drafts_are_saved_per_account(): void
    {
        $payload = [
            'fields' => [
                ['name' => 'answers[91][100][importance]', 'value' => '7'],
                ['name' => 'answers[91][100][performance]', 'value' => '6'],
            ],
        ];

        foreach ([20, 40] as $userId) {
            $this->actingAs(User::query()->findOrFail($userId))
                ->postJson(route('survey.draft.store', 50), $payload)
                ->assertOk()
                ->assertJson(['saved' => true]);
        }

        $this->assertDatabaseCount('survey_drafts', 2);
        $this->assertDatabaseHas('survey_drafts', ['user_id' => 20, 'form_id' => 50]);
        $this->assertDatabaseHas('survey_drafts', ['user_id' => 40, 'form_id' => 50]);
    }

    public function test_completed_account_cannot_change_a_draft(): void
    {
        DB::table('survey_sessions')->where('user_id', 40)->update(['status' => 'completed']);

        $this->actingAs(User::query()->findOrFail(40))
            ->postJson(route('survey.draft.store', 50), [
                'fields' => [['name' => 'answers[91][value]', 'value' => '5']],
            ])
            ->assertForbidden();

        $this->assertDatabaseMissing('survey_drafts', ['user_id' => 40]);
    }
}
