<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class SurveyorAccountAccessTest extends TestCase
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
        Schema::create('complete_profiles', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('activity_id');
            $table->string('group_question')->nullable();
            $table->string('unit_question')->nullable();
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
        Schema::create('answers', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('form_id')->nullable();
            $table->unsignedBigInteger('question_id')->nullable();
            $table->unsignedBigInteger('subunit_id')->nullable();
            $table->unsignedBigInteger('competitor_id')->nullable();
            $table->text('answer')->nullable();
            $table->timestamps();
        });
        Schema::create('forms', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('group_id');
            $table->integer('no_urut')->default(1);
            $table->string('name')->nullable();
            $table->timestamps();
        });

        DB::table('roles')->insert([
            ['id' => 1, 'name' => 'Admin'],
            ['id' => 2, 'name' => 'Surveyor'],
            ['id' => 3, 'name' => 'Monitoring'],
            ['id' => 4, 'name' => 'User'],
        ]);
        DB::table('activities')->insert([
            ['id' => 10, 'name' => 'Activity Surveyor'],
            ['id' => 20, 'name' => 'Activity Lain'],
        ]);
        DB::table('groups')->insert([
            ['id' => 100, 'activity_id' => 10, 'name' => 'Group Surveyor'],
            ['id' => 200, 'activity_id' => 20, 'name' => 'Group Lain'],
        ]);
        DB::table('units')->insert([
            ['id' => 1000, 'group_id' => 100, 'name' => 'Unit Surveyor'],
            ['id' => 2000, 'group_id' => 200, 'name' => 'Unit Lain'],
        ]);
        DB::table('users')->insert([
            'id' => 2,
            'username' => 'contoh-surveyor',
            'password' => Hash::make('Surveyor!123'),
            'role_id' => 2,
        ]);
        DB::table('user_profiles')->insert([
            'user_id' => 2,
            'activity_id' => 10,
            'group_id' => 100,
            'unit_id' => 1000,
            'fullname' => 'Surveyor Contoh',
        ]);
        DB::table('forms')->insert([
            'id' => 500,
            'group_id' => 100,
            'no_urut' => 1,
            'name' => 'Form Contoh',
        ]);
    }

    protected function tearDown(): void
    {
        foreach (['forms', 'answers', 'survey_sessions', 'complete_profiles', 'user_profiles', 'units', 'groups', 'activities', 'users', 'roles'] as $table) {
            Schema::dropIfExists($table);
        }

        parent::tearDown();
    }

    public function test_surveyor_gets_user_style_dashboard_and_cannot_access_admin_monitoring(): void
    {
        $surveyor = User::query()->findOrFail(2);

        $dashboard = $this->actingAs($surveyor)->get(route('user.dashboard'));
        $dashboard->assertOk();
        $dashboard->assertSee('Mode Contoh Surveyor');
        $dashboard->assertSee('Activity Surveyor');
        $dashboard->assertSee('Surveyor · Akun Contoh');
        $dashboard->assertSee(route('survey.index'), false);
        $dashboard->assertDontSee('href="'.route('profile.show').'"', false);
        $dashboard->assertSee(route('surveyor.dashboard'), false);

        DB::table('groups')->insert([
            'id' => 101,
            'activity_id' => 10,
            'name' => 'Group Di Luar Profil Surveyor',
        ]);
        DB::table('units')->insert([
            'id' => 1001,
            'group_id' => 101,
            'name' => 'Unit Di Luar Profil Surveyor',
        ]);
        DB::table('users')->insert([
            ['id' => 10, 'username' => 'responden-profil-surveyor', 'password' => Hash::make('User!12345'), 'role_id' => 4],
            ['id' => 11, 'username' => 'responden-di-luar-profil', 'password' => Hash::make('User!12345'), 'role_id' => 4],
        ]);
        DB::table('user_profiles')->insert([
            ['user_id' => 10, 'activity_id' => 10, 'group_id' => 100, 'unit_id' => 1000, 'fullname' => 'Dalam Profil'],
            ['user_id' => 11, 'activity_id' => 10, 'group_id' => 101, 'unit_id' => 1001, 'fullname' => 'Luar Profil'],
        ]);

        $monitoringDashboard = $this->actingAs($surveyor)
            ->get(route('surveyor.dashboard'));
        $monitoringDashboard->assertOk();
        $monitoringDashboard->assertSee('Dashboard Surveyor');
        $monitoringDashboard->assertSee('Monitoring Survey');
        $monitoringDashboard->assertSee('Activity Surveyor');
        $monitoringDashboard->assertSee('Group Surveyor');
        $monitoringDashboard->assertSee('Unit Surveyor');
        $monitoringDashboard->assertSee('responden-profil-surveyor');
        $monitoringDashboard->assertDontSee('Group Di Luar Profil Surveyor');
        $monitoringDashboard->assertDontSee('Unit Di Luar Profil Surveyor');
        $monitoringDashboard->assertDontSee('responden-di-luar-profil');
        $monitoringDashboard->assertSee('Lihat Jawaban');
        $monitoringDashboard->assertSee('Lihat Profil');
        $monitoringDashboard->assertSee(route('surveyor.respondent.profile', 10), false);
        $monitoringDashboard->assertSee(route('surveyor.respondent.answers', 10), false);
        $monitoringDashboard->assertDontSee('name="group_id"', false);
        $monitoringDashboard->assertDontSee('name="unit_id"', false);
        $monitoringDashboard->assertDontSee('Tambah Data');
        $monitoringDashboard->assertDontSee('Edit Data');
        $monitoringDashboard->assertDontSee('Hapus Data');

        $this->actingAs($surveyor)
            ->get(route('surveyor.respondent.profile', 10))
            ->assertOk()
            ->assertSee('responden-profil-surveyor');
        $this->actingAs($surveyor)
            ->get(route('surveyor.respondent.answers', 10))
            ->assertOk()
            ->assertSee('Lihat Jawaban Responden');
        $this->actingAs($surveyor)
            ->get(route('surveyor.respondent.profile', 11))
            ->assertNotFound();
        $this->actingAs($surveyor)
            ->get(route('surveyor.respondent.answers', 11))
            ->assertNotFound();

        $this->actingAs($surveyor)->get(route('admin.dashboard'))->assertForbidden();
        $this->actingAs($surveyor)->get(route('monitoring.dashboard'))->assertForbidden();
    }

    public function test_new_surveyor_login_opens_dashboard_before_profile_selection(): void
    {
        DB::table('users')->insert([
            'id' => 5,
            'username' => 'surveyor-baru',
            'password' => Hash::make('Surveyor!456'),
            'role_id' => 2,
        ]);
        DB::table('user_profiles')->insert([
            'user_id' => 5,
            'activity_id' => null,
            'group_id' => null,
            'unit_id' => null,
            'fullname' => null,
        ]);

        $response = $this->post('/login', [
            'username' => 'surveyor-baru',
            'password' => 'Surveyor!456',
        ]);

        $response->assertRedirect(route('user.dashboard'));
        $this->assertAuthenticatedAs(User::query()->findOrFail(5));

        $dashboard = $this->get(route('user.dashboard'));
        $dashboard->assertOk();
        $dashboard->assertSee('Mode Contoh Surveyor');
        $dashboard->assertSee('Lengkapi Profil');
        $dashboard->assertSee('Dashboard Monitoring');
        $dashboard->assertSee(route('surveyor.dashboard'), false);
        $dashboard->assertDontSee(route('survey.index'), false);
        $dashboard->assertDontSee('href="'.route('profile.show').'"', false);

        $this->get(route('surveyor.dashboard'))
            ->assertRedirect(route('profile.edit'))
            ->assertSessionHas('warning');
    }

    public function test_user_sidebar_uses_the_same_profile_and_survey_visibility_rules(): void
    {
        DB::table('users')->insert([
            ['id' => 6, 'username' => 'user-lengkap', 'password' => Hash::make('User!12345'), 'role_id' => 4],
            ['id' => 7, 'username' => 'user-belum-lengkap', 'password' => Hash::make('User!12345'), 'role_id' => 4],
        ]);
        DB::table('user_profiles')->insert([
            ['user_id' => 6, 'activity_id' => 10, 'group_id' => 100, 'unit_id' => 1000, 'fullname' => 'Lengkap'],
            ['user_id' => 7, 'activity_id' => 10, 'group_id' => null, 'unit_id' => null, 'fullname' => 'Belum'],
        ]);

        $complete = $this->actingAs(User::query()->findOrFail(6))->get(route('user.dashboard'));
        $complete->assertOk();
        $complete->assertSee(route('survey.index'), false);
        $complete->assertDontSee('href="'.route('profile.show').'"', false);

        $incomplete = $this->actingAs(User::query()->findOrFail(7))->get(route('user.dashboard'));
        $incomplete->assertOk();
        $incomplete->assertDontSee(route('survey.index'), false);
        $incomplete->assertDontSee('href="'.route('profile.show').'"', false);
    }

    public function test_user_activity_is_locked_while_group_and_unit_remain_cascading(): void
    {
        DB::table('users')->insert([
            'id' => 8,
            'username' => 'user-pilih-profile',
            'password' => Hash::make('User!12345'),
            'role_id' => 4,
        ]);
        DB::table('user_profiles')->insert([
            'user_id' => 8,
            'activity_id' => 10,
            'group_id' => null,
            'unit_id' => null,
            'fullname' => null,
        ]);

        $edit = $this->actingAs(User::query()->findOrFail(8))->get(route('profile.edit'));
        $edit->assertOk();
        $edit->assertDontSee('id="activity_id"', false);
        $edit->assertSee('Activity Surveyor');
        $edit->assertSee('Activity telah ditentukan untuk akun Anda dan tidak dapat diubah.');
        $edit->assertSee('id="group_id"', false);
        $edit->assertSeeInOrder(['id="unit_id"', 'disabled'], false);

        $save = $this->actingAs(User::query()->findOrFail(8))->put(route('profile.update'), [
            'activity_id' => 20,
            'group_id' => 100,
            'unit_id' => 1000,
        ]);

        $save->assertRedirect(route('profile.show'));
        $this->assertDatabaseHas('user_profiles', [
            'user_id' => 8,
            'activity_id' => 10,
            'group_id' => 100,
            'unit_id' => 1000,
        ]);
    }

    public function test_complete_profile_labels_follow_the_selected_activity(): void
    {
        DB::table('complete_profiles')->insert([
            [
                'activity_id' => 10,
                'group_question' => 'Bidang Kerja Activity Surveyor',
                'unit_question' => 'Jabatan Activity Surveyor',
            ],
            [
                'activity_id' => 20,
                'group_question' => 'Bidang Kerja Activity Lain',
                'unit_question' => 'Jabatan Activity Lain',
            ],
        ]);

        $surveyor = User::query()->findOrFail(2);

        $edit = $this->actingAs($surveyor)->get(route('profile.edit'));
        $edit->assertOk();
        $edit->assertSee('data-group-label="Bidang Kerja Activity Lain"', false);
        $edit->assertSee('data-unit-label="Jabatan Activity Lain"', false);

        $units = $this->actingAs($surveyor)->getJson(route('profile.units', 200));
        $units->assertOk()->assertJsonPath(
            'data.labels.group',
            'Bidang Kerja Activity Lain'
        )->assertJsonPath(
            'data.labels.unit',
            'Jabatan Activity Lain'
        );

        $dashboard = $this->actingAs($surveyor)->get(route('user.dashboard'));
        $dashboard->assertOk();
        $dashboard->assertSee('Bidang Kerja Activity Surveyor');
        $dashboard->assertSee('Jabatan Activity Surveyor');
    }
    public function test_surveyor_can_choose_another_activity_and_old_demo_data_is_cleared(): void
    {
        $surveyor = User::query()->findOrFail(2);
        DB::table('answers')->insert(['user_id' => 2, 'answer' => 'Contoh lama']);
        DB::table('survey_sessions')->insert([
            'user_id' => 2,
            'activity_id' => 10,
            'group_id' => 100,
            'unit_id' => 1000,
            'status' => 'in_progress',
        ]);

        $response = $this->actingAs($surveyor)
            ->from(route('profile.show'))
            ->put(route('profile.update'), [
                'activity_id' => 20,
                'group_id' => 200,
                'unit_id' => 2000,
            ]);

        $response->assertRedirect(route('profile.show'));
        $response->assertSessionHas('success');
        $this->assertDatabaseHas('user_profiles', [
            'user_id' => 2,
            'activity_id' => 20,
            'group_id' => 200,
            'unit_id' => 2000,
        ]);
        $this->assertDatabaseMissing('answers', ['user_id' => 2]);
        $this->assertDatabaseMissing('survey_sessions', ['user_id' => 2]);
    }

    public function test_surveyor_cannot_mix_group_from_another_activity(): void
    {
        $response = $this->actingAs(User::query()->findOrFail(2))
            ->from(route('profile.edit'))
            ->put(route('profile.update'), [
                'activity_id' => 20,
                'group_id' => 100,
                'unit_id' => 1000,
            ]);

        $response->assertRedirect(route('profile.edit'));
        $response->assertSessionHasErrors('group_id');
        $this->assertDatabaseHas('user_profiles', [
            'user_id' => 2,
            'activity_id' => 10,
        ]);
    }

    public function test_completed_surveyor_is_locked_and_can_reset_own_account(): void
    {
        DB::table('answers')->insert([
            'user_id' => 2,
            'answer' => 'Jawaban simulasi tetap tersimpan',
        ]);
        DB::table('survey_sessions')->insert([
            'user_id' => 2,
            'activity_id' => 10,
            'group_id' => 100,
            'unit_id' => 1000,
            'current_form_id' => null,
            'status' => 'completed',
            'started_at' => now()->subHour(),
            'finished_at' => now(),
        ]);

        $surveyor = User::query()->findOrFail(2);

        $this->actingAs($surveyor)
            ->get(route('survey.index'))
            ->assertRedirect(route('user.dashboard'))
            ->assertSessionHas('error');

        $dashboard = $this->get(route('user.dashboard'));
        $dashboard->assertOk();
        $dashboard->assertSee('Simulasi telah selesai dan dikunci sampai Admin melakukan Reset Account.');
        $dashboard->assertDontSee('Isi Survei');
        $dashboard->assertDontSee('Buka Kembali Simulasi');

        $this->get(route('profile.edit'))
            ->assertRedirect(route('user.dashboard'))
            ->assertSessionHas('error');

        $this->assertDatabaseHas('answers', [
            'user_id' => 2,
            'answer' => 'Jawaban simulasi tetap tersimpan',
        ]);
        $this->assertDatabaseHas('survey_sessions', [
            'user_id' => 2,
            'status' => 'completed',
        ]);
        $this->assertDatabaseHas('user_profiles', [
            'user_id' => 2,
            'activity_id' => 10,
            'group_id' => 100,
            'unit_id' => 1000,
        ]);
        $dashboard->assertSee('Reset Account');
        $dashboard->assertSee('surveyorResetAccountModal', false);

        $reset = $this->post(route('surveyor.reset-account'));
        $reset->assertRedirect(route('user.dashboard'));
        $reset->assertSessionHas('success');

        $this->assertDatabaseMissing('answers', ['user_id' => 2]);
        $this->assertDatabaseMissing('survey_sessions', ['user_id' => 2]);
        $this->assertDatabaseHas('user_profiles', [
            'user_id' => 2,
            'activity_id' => null,
            'group_id' => null,
            'unit_id' => null,
        ]);
        $this->assertDatabaseHas('users', ['id' => 2]);
        $this->assertAuthenticatedAs($surveyor);
    }

    public function test_completed_normal_user_has_no_survey_or_profile_actions_after_login(): void
    {
        DB::table('users')->insert([
            'id' => 9,
            'username' => 'user-selesai',
            'password' => Hash::make('User!12345'),
            'role_id' => 4,
        ]);
        DB::table('user_profiles')->insert([
            'user_id' => 9,
            'activity_id' => 10,
            'group_id' => 100,
            'unit_id' => 1000,
            'fullname' => 'User Selesai',
        ]);
        DB::table('survey_sessions')->insert([
            'user_id' => 9,
            'activity_id' => 10,
            'group_id' => 100,
            'unit_id' => 1000,
            'status' => 'completed',
            'started_at' => now()->subHour(),
            'finished_at' => now(),
        ]);

        $response = $this->actingAs(User::query()->findOrFail(9))
            ->get(route('user.dashboard'));

        $response->assertOk();
        $response->assertSee('Pengisian survei telah selesai dan dikunci.');
        $response->assertDontSee('Isi Survei');
        $response->assertDontSee('Mulai Survei');
        $response->assertDontSee('Lanjutkan Survei');
        $response->assertDontSee('Profil Lengkap');
        $response->assertDontSee(route('survey.index'), false);
        $response->assertDontSee('Reset Account');

        $this->post(route('surveyor.reset-account'))->assertForbidden();

        $this->get(route('survey.index'))
            ->assertRedirect(route('user.dashboard'));

        $this->post(route('logout'));
        $this->assertGuest();

        $login = $this->post('/login', [
            'username' => 'user-selesai',
            'password' => 'User!12345',
        ]);

        $login->assertRedirect(route('user.dashboard'));
        $this->assertAuthenticatedAs(User::query()->findOrFail(9));

        $dashboardAfterLogin = $this->get(route('user.dashboard'));
        $dashboardAfterLogin->assertOk();
        $dashboardAfterLogin->assertSee('Pengisian survei telah selesai dan dikunci.');
        $dashboardAfterLogin->assertDontSee(route('survey.index'), false);

        DB::table('users')->insert([
            'id' => 12,
            'username' => 'admin-pdf',
            'password' => Hash::make('Admin!12345'),
            'role_id' => 1,
        ]);

        $pdf = $this->actingAs(User::query()->findOrFail(12))
            ->get(route('admin.datauser.answers.pdf', 9));
        $pdf->assertOk();
        $pdf->assertHeader('content-type', 'application/pdf');
        $this->assertStringContainsString(
            'attachment; filename=review-jawaban-user-selesai.pdf',
            (string) $pdf->headers->get('content-disposition')
        );

        $this->actingAs(User::query()->findOrFail(12))
            ->get(route('admin.datauser.answers.pdf', 2))
            ->assertStatus(422);
    }
}
