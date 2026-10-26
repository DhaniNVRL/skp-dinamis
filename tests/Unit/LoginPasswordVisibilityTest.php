<?php

namespace Tests\Unit;

use Tests\TestCase;

class LoginPasswordVisibilityTest extends TestCase
{
    public function test_login_password_can_be_shown_and_hidden(): void
    {
        $view = file_get_contents(resource_path('views/auth/login.blade.php'));

        $this->assertStringContainsString('id="loginPassword"', $view);
        $this->assertStringContainsString('id="toggleLoginPassword"', $view);
        $this->assertStringContainsString("password.type = willShow ? 'text' : 'password';", $view);
        $this->assertStringContainsString("icon.classList.toggle('fa-eye-slash', willShow);", $view);
        $this->assertStringContainsString("toggle.setAttribute('aria-pressed', String(willShow));", $view);
    }
}
