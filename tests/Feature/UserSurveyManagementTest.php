<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class UserSurveyManagementTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Schema::create('users', function (Blueprint $table): void {
            $table->id();
            $table->string('username');
            $table->string('password');
            $table->unsignedBigInteger('role_id')->nullable();
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

        Schema::create('answers', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->text('answer')->nullable();
            $table->timestamps();
        });

        Schema::create('survey_sessions', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('activity_id')->nullable();
            $table->unsignedBigInteger('group_id')->nullable();
            $table->unsignedBigInteger('unit_id')->nullable();
            $table->unsignedBigInteger('current_form_id')->nullable();
            $table->string('status')->default('not_started');
            $table->timestamp('started_at')->nullable();
            $table->timestamp('finished_at')->nullable();
            $table->timestamp('reopened_at')->nullable();
            $table->timestamps();
        });

        Schema::create('forms', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('group_id');
            $table->integer('no_urut')->nullable();
            $table->timestamps();
        });
    }

    protected function tearDown(): void
    {
        Schema::dropIfExists('forms');
        Schema::dropIfExists('survey_sessions');
        Schema::dropIfExists('answers');
        Schema::dropIfExists('user_profiles');
        Schema::dropIfExists('users');

        parent::tearDown();
    }

    public function test_deleting_answers_keeps_account_profile_and_survey_status(): void
    {
        $admin = $this->createUser(1, 'admin');
        $this->createRespondentWithCompletedSurvey();
        DB::table('answers')->insert(['user_id' => 2, 'answer' => 'jawaban']);

        $response = $this
            ->withoutMiddleware()
            ->actingAs($admin)
            ->from('/datauser')
            ->delete(route('admin.datauser.resetjawaban', 2));

        $response->assertRedirect('/datauser');
        $this->assertDatabaseMissing('answers', ['user_id' => 2]);
        $this->assertDatabaseHas('users', ['id' => 2]);
        $this->assertDatabaseHas('user_profiles', ['user_id' => 2]);
        $this->assertDatabaseHas('survey_sessions', [
            'user_id' => 2,
            'status' => 'completed',
        ]);
    }

    public function test_completed_survey_can_be_reopened_without_deleting_answers(): void
    {
        $admin = $this->createUser(1, 'admin');
        $this->createRespondentWithCompletedSurvey();
        DB::table('forms')->insert(['id' => 100, 'group_id' => 20, 'no_urut' => 1]);
        DB::table('answers')->insert(['user_id' => 2, 'answer' => 'jawaban lama']);

        $response = $this
            ->withoutMiddleware()
            ->actingAs($admin)
            ->from('/datauser')
            ->post(route('admin.datauser.reopen-survey', 2));

        $response->assertRedirect('/datauser');
        $this->assertDatabaseHas('answers', ['user_id' => 2, 'answer' => 'jawaban lama']);
        $this->assertDatabaseHas('survey_sessions', [
            'user_id' => 2,
            'status' => 'in_progress',
            'current_form_id' => 100,
            'finished_at' => null,
        ]);
        $this->assertNotNull(
            DB::table('survey_sessions')->where('user_id', 2)->value('reopened_at')
        );
    }

    private function createRespondentWithCompletedSurvey(): void
    {
        $this->createUser(2, 'respondent');
        DB::table('user_profiles')->insert([
            'user_id' => 2,
            'activity_id' => 10,
            'group_id' => 20,
            'unit_id' => 30,
        ]);
        DB::table('survey_sessions')->insert([
            'user_id' => 2,
            'activity_id' => 10,
            'group_id' => 20,
            'unit_id' => 30,
            'status' => 'completed',
            'started_at' => now()->subHour(),
            'finished_at' => now(),
        ]);
    }

    private function createUser(int $id, string $username): User
    {
        DB::table('users')->insert([
            'id' => $id,
            'username' => $username,
            'password' => 'secret',
        ]);

        return User::query()->findOrFail($id);
    }
}
