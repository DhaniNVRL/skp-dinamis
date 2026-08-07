<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class DataUserAccountDeletionTest extends TestCase
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
            $table->timestamps();
        });

        Schema::create('sessions', function (Blueprint $table): void {
            $table->string('id')->primary();
            $table->unsignedBigInteger('user_id')->nullable();
        });
    }

    protected function tearDown(): void
    {
        Schema::dropIfExists('sessions');
        Schema::dropIfExists('survey_sessions');
        Schema::dropIfExists('answers');
        Schema::dropIfExists('user_profiles');
        Schema::dropIfExists('users');

        parent::tearDown();
    }

    public function test_admin_can_delete_account_with_profile_answers_and_survey_data(): void
    {
        $admin = $this->createUser(1, 'admin');
        $this->createUser(2, 'respondent');

        DB::table('user_profiles')->insert(['user_id' => 2]);
        DB::table('answers')->insert([
            ['user_id' => 2, 'answer' => 'A'],
            ['user_id' => 2, 'answer' => 'B'],
        ]);
        DB::table('survey_sessions')->insert(['user_id' => 2]);
        DB::table('sessions')->insert(['id' => 'target-session', 'user_id' => 2]);

        $response = $this
            ->withoutMiddleware()
            ->actingAs($admin)
            ->from('/datauser')
            ->delete(route('admin.datauser.destroy', 2));

        $response->assertRedirect('/datauser');
        $response->assertSessionHas('successdelete');
        $this->assertDatabaseMissing('users', ['id' => 2]);
        $this->assertDatabaseMissing('user_profiles', ['user_id' => 2]);
        $this->assertDatabaseMissing('answers', ['user_id' => 2]);
        $this->assertDatabaseMissing('survey_sessions', ['user_id' => 2]);
        $this->assertDatabaseMissing('sessions', ['user_id' => 2]);
    }

    public function test_admin_can_delete_account_without_answers(): void
    {
        $admin = $this->createUser(1, 'admin');
        $this->createUser(2, 'empty-user');
        DB::table('user_profiles')->insert(['user_id' => 2]);

        $response = $this
            ->withoutMiddleware()
            ->actingAs($admin)
            ->from('/datauser')
            ->delete(route('admin.datauser.destroy', 2));

        $response->assertRedirect('/datauser');
        $this->assertDatabaseMissing('users', ['id' => 2]);
        $this->assertDatabaseMissing('user_profiles', ['user_id' => 2]);
    }

    public function test_logged_in_account_cannot_delete_itself(): void
    {
        $admin = $this->createUser(1, 'admin');

        $response = $this
            ->withoutMiddleware()
            ->actingAs($admin)
            ->delete(route('admin.datauser.destroy', 1));

        $response->assertStatus(422);
        $this->assertDatabaseHas('users', ['id' => 1]);
    }

    private function createUser(int $id, string $username): User
    {
        DB::table('users')->insert([
            'id' => $id,
            'username' => $username,
            'password' => 'secret',
            'role_id' => null,
        ]);

        return User::query()->findOrFail($id);
    }
}
