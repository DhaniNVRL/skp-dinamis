<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class SurveyorAccountCreationTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Schema::create('roles', function (Blueprint $table): void {
            $table->id();
            $table->string('name');
        });
        Schema::create('activities', function (Blueprint $table): void {
            $table->id();
            $table->string('name');
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
            $table->string('fullname')->nullable();
            $table->string('email')->nullable();
            $table->string('no_handphone')->nullable();
            $table->timestamps();
        });

        DB::table('roles')->insert([
            ['id' => 1, 'name' => 'Admin'],
            ['id' => 2, 'name' => 'Surveyor'],
            ['id' => 4, 'name' => 'User'],
        ]);
        DB::table('activities')->insert(['id' => 10, 'name' => 'Activity A']);
        DB::table('users')->insert([
            'id' => 1,
            'username' => 'admin',
            'password' => 'secret',
            'role_id' => 1,
        ]);
    }

    protected function tearDown(): void
    {
        foreach (['user_profiles', 'users', 'activities', 'roles'] as $table) {
            Schema::dropIfExists($table);
        }

        parent::tearDown();
    }

    public function test_admin_can_create_surveyor_without_activity(): void
    {
        $response = $this
            ->withoutMiddleware()
            ->actingAs(User::query()->findOrFail(1))
            ->post(route('admin.datauser.store'), [
                'username' => ['contoh-surveyor'],
                'password' => ['Surveyor!123'],
                'role_id' => [2],
                'activity_id' => [''],
            ]);

        $response->assertSessionHasNoErrors();
        $response->assertSessionHas('success');

        $surveyorId = DB::table('users')->where('username', 'contoh-surveyor')->value('id');
        $this->assertNotNull($surveyorId);
        $this->assertDatabaseHas('users', ['id' => $surveyorId, 'role_id' => 2]);
        $this->assertDatabaseHas('user_profiles', [
            'user_id' => $surveyorId,
            'activity_id' => null,
            'group_id' => null,
            'unit_id' => null,
        ]);
    }

    public function test_normal_user_still_requires_activity(): void
    {
        $response = $this
            ->withoutMiddleware()
            ->actingAs(User::query()->findOrFail(1))
            ->post(route('admin.datauser.store'), [
                'username' => ['respondent'],
                'password' => ['Respondent!123'],
                'role_id' => [4],
                'activity_id' => [''],
            ]);

        $response->assertSessionHasErrors('activity_id.0');
        $this->assertDatabaseMissing('users', ['username' => 'respondent']);
    }
}
