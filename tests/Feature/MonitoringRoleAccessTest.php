<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class MonitoringRoleAccessTest extends TestCase
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
        Schema::create('activities', function (Blueprint $table): void {
            $table->id();
            $table->string('name');
            $table->timestamps();
        });
        Schema::create('groups', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('activity_id');
            $table->string('name');
            $table->timestamps();
        });
        Schema::create('units', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('group_id');
            $table->string('name');
            $table->timestamps();
        });
        Schema::create('user_profiles', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('activity_id')->nullable();
            $table->unsignedBigInteger('group_id')->nullable();
            $table->unsignedBigInteger('unit_id')->nullable();
            $table->string('fullname')->nullable();
            $table->string('email')->nullable();
            $table->string('no_handphone')->nullable();
            $table->timestamps();
        });
        Schema::create('answers', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->timestamps();
        });
        Schema::create('survey_sessions', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('activity_id')->nullable();
            $table->unsignedBigInteger('group_id')->nullable();
            $table->unsignedBigInteger('unit_id')->nullable();
            $table->unsignedBigInteger('current_form_id')->nullable();
            $table->string('status')->nullable();
            $table->timestamp('started_at')->nullable();
            $table->timestamp('finished_at')->nullable();
            $table->timestamp('reopened_at')->nullable();
            $table->timestamps();
        });

        DB::table('roles')->insert([
            ['id' => 1, 'name' => 'Admin'],
            ['id' => 3, 'name' => 'Monitoring'],
            ['id' => 4, 'name' => 'User'],
        ]);
        DB::table('activities')->insert([
            ['id' => 10, 'name' => 'Activity Terkunci'],
            ['id' => 20, 'name' => 'Activity Lain'],
        ]);
        DB::table('groups')->insert([
            ['id' => 100, 'activity_id' => 10, 'name' => 'Group Terlihat'],
            ['id' => 200, 'activity_id' => 20, 'name' => 'Group Rahasia'],
        ]);
        DB::table('units')->insert([
            ['id' => 1000, 'group_id' => 100, 'name' => 'Unit Terlihat'],
            ['id' => 2000, 'group_id' => 200, 'name' => 'Unit Rahasia'],
        ]);
        DB::table('users')->insert([
            ['id' => 1, 'username' => 'client-monitoring', 'password' => 'secret', 'role_id' => 3],
            ['id' => 2, 'username' => 'responden-terlihat', 'password' => 'secret', 'role_id' => 4],
            ['id' => 3, 'username' => 'responden-rahasia', 'password' => 'secret', 'role_id' => 4],
            ['id' => 4, 'username' => 'responden-selesai', 'password' => 'secret', 'role_id' => 4],
        ]);
        DB::table('user_profiles')->insert([
            ['user_id' => 1, 'activity_id' => 10, 'group_id' => null, 'unit_id' => null, 'fullname' => 'Client'],
            ['user_id' => 2, 'activity_id' => 10, 'group_id' => 100, 'unit_id' => 1000, 'fullname' => 'Terlihat'],
            ['user_id' => 3, 'activity_id' => 20, 'group_id' => 200, 'unit_id' => 2000, 'fullname' => 'Rahasia'],
            ['user_id' => 4, 'activity_id' => 10, 'group_id' => 100, 'unit_id' => 1000, 'fullname' => 'Selesai'],
        ]);
        DB::table('survey_sessions')->insert([
            'user_id' => 4,
            'activity_id' => 10,
            'group_id' => 100,
            'unit_id' => 1000,
            'status' => 'completed',
            'started_at' => now()->subHour(),
            'finished_at' => now(),
        ]);
    }

    protected function tearDown(): void
    {
        foreach (['survey_sessions', 'answers', 'user_profiles', 'units', 'groups', 'activities', 'users', 'roles'] as $table) {
            Schema::dropIfExists($table);
        }

        parent::tearDown();
    }

    public function test_monitoring_only_sees_respondents_from_its_locked_activity(): void
    {
        $response = $this->actingAs(User::query()->findOrFail(1))->get(route('monitoring.dashboard'));

        $response->assertOk();
        $response->assertSee('responden-terlihat');
        $response->assertDontSee('responden-rahasia');
        $response->assertDontSee('Group Rahasia');
        $response->assertDontSee('Unit Rahasia');
        $response->assertDontSee('name="activity_id"', false);
        $response->assertDontSee('name="status"', false);
        $response->assertDontSee('Lihat Jawaban');
    }

    public function test_monitoring_cannot_open_respondent_answer_detail_or_admin_pages(): void
    {
        $monitoring = User::query()->findOrFail(1);

        $this->actingAs($monitoring)
            ->get(route('monitoring.respondent.detail', ['userId' => 2]))
            ->assertForbidden();

        $this->actingAs($monitoring)
            ->get(route('admin.datauser'))
            ->assertForbidden();
    }

    public function test_foreign_group_and_unit_filters_are_rejected(): void
    {
        $monitoring = User::query()->findOrFail(1);

        $this->actingAs($monitoring)
            ->from(route('monitoring.dashboard'))
            ->get(route('monitoring.dashboard', ['group_id' => 200, 'unit_id' => 2000]))
            ->assertRedirect(route('monitoring.dashboard'))
            ->assertSessionHasErrors(['group_id', 'unit_id']);
    }

    public function test_status_cards_filter_the_table_and_keep_monitoring_read_only(): void
    {
        $monitoring = User::query()->findOrFail(1);

        $all = $this->actingAs($monitoring)->get(route('monitoring.dashboard'));
        $all->assertSee('status=completed', false);
        $all->assertSee('status=in_progress', false);
        $all->assertSee('status=not_started', false);

        $completed = $this->actingAs($monitoring)->get(route('monitoring.dashboard', [
            'status' => 'completed',
            'username' => 'responden',
            'group_id' => 100,
        ]));

        $completed->assertOk();
        $completed->assertSee('responden-selesai');
        $completed->assertDontSee('responden-terlihat');
        $completed->assertSee('name="status" value="completed"', false);
        $completed->assertDontSee('Lihat Jawaban');

        $notStarted = $this->actingAs($monitoring)->get(route('monitoring.dashboard', [
            'status' => 'not_started',
        ]));

        $notStarted->assertOk();
        $notStarted->assertSee('responden-terlihat');
        $notStarted->assertDontSee('responden-selesai');
    }
}
