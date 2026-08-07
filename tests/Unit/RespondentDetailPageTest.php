<?php

namespace Tests\Unit;

use App\Models\Role;
use App\Models\User;
use App\Models\UserProfile;
use Tests\TestCase;

class RespondentDetailPageTest extends TestCase
{
    public function test_full_page_detail_contains_profile_survey_status_and_unlimited_answers_section(): void
    {
        $role = new Role(['name' => 'User']);
        $user = new User(['username' => 'responden-1']);
        $user->setRelation('role', $role);

        $profile = new UserProfile([
            'fullname' => 'Responden Satu',
            'email' => 'responden@example.test',
        ]);

        $html = view('admin.shared.respondent-answer-detail', [
            'pageTitle' => 'Detail Responden dan Jawaban',
            'pageDescription' => 'Detail lengkap.',
            'backUrl' => '/admin/dashboard',
            'backLabel' => 'Kembali ke Dashboard',
            'user' => $user,
            'profile' => $profile,
            'answers' => collect(),
            'survey' => [
                'status' => 'in_progress',
                'status_label' => 'Sedang Mengisi',
                'started_at' => '07-08-2026 10:00',
                'finished_at' => null,
                'answers_count' => 0,
            ],
        ])->render();

        $this->assertStringContainsString('Status Survey', $html);
        $this->assertStringContainsString('Sedang Mengisi', $html);
        $this->assertStringContainsString('Seluruh jawaban ditampilkan tanpa pembatasan halaman.', $html);
        $this->assertStringNotContainsString('data-modal', $html);
    }
}
