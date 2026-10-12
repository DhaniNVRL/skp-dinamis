<?php

namespace Tests\Unit;

use Tests\TestCase;

class UserPasswordValidationTest extends TestCase
{
    public function test_password_complexity_only_applies_to_admin_and_monitoring_registration(): void
    {
        $controller = file_get_contents(app_path('Http/Controllers/DataUserController.php'));

        $this->assertStringContainsString('in_array($roleId, [1, 3], true)', $controller);
        $this->assertStringContainsString('Password::min(10)->mixedCase()->numbers()->symbols()', $controller);
        $this->assertStringContainsString('Password akun Admin dan Monitoring minimal 10 karakter', $controller);
        $this->assertStringContainsString("'password' => ['required', 'string', 'max:255']", $controller);
        $this->assertStringContainsString("'password.*' => ['required', 'string', 'max:255']", $controller);
    }
}
