<?php

namespace Tests\Unit;

use Tests\TestCase;

class UserExcelOptionalAssignmentTest extends TestCase
{
    public function test_group_unit_fullname_and_email_are_optional_in_user_excel_import(): void
    {
        $controller = file_get_contents(app_path('Http/Controllers/DataUserController.php'));

        $this->assertStringContainsString("'group_id' => ['nullable', 'integer']", $controller);
        $this->assertStringContainsString("'unit_id' => ['nullable', 'integer']", $controller);
        $this->assertStringContainsString("'fullname' => ['nullable', 'string', 'max:255']", $controller);
        $this->assertStringContainsString("'email' => ['nullable', 'email', 'max:255']", $controller);
        $this->assertStringContainsString("setCellValue('E1','id_group (opsional)')", $controller);
        $this->assertStringContainsString("setCellValue('F1','id_unit (opsional)')", $controller);
        $this->assertStringContainsString("setCellValue('G1','fullname (opsional)')", $controller);
        $this->assertStringContainsString("setCellValue('H1','email (opsional)')", $controller);
        $this->assertStringContainsString('Group dan Unit tetap opsional.', $controller);
        $this->assertStringNotContainsString('Pastikan Activity, Group, dan Unit saling sesuai.', $controller);
    }
}
