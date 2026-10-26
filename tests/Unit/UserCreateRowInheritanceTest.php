<?php

namespace Tests\Unit;

use Tests\TestCase;

class UserCreateRowInheritanceTest extends TestCase
{
    public function test_new_user_row_inherits_the_last_rows_assignment_fields(): void
    {
        $dynamicTable = file_get_contents(resource_path('js/admin/global/dynamic-table.js'));
        $users = file_get_contents(resource_path('js/admin/users.js'));

        $this->assertStringContainsString('dynamic-table:row-added', $dynamicTable);
        $this->assertStringContainsString('previousRow', $dynamicTable);
        $this->assertStringContainsString("table?.id !== 'userCreateTable'", $users);
        $this->assertStringContainsString("role.value = previousRole?.value || '';", $users);
        $this->assertStringContainsString("activity.value = previousActivity?.value || '';", $users);
        $this->assertStringContainsString("group.value = previousGroup?.value || '';", $users);
        $this->assertStringContainsString("unit.value = previousUnit?.value || '';", $users);
        $this->assertStringNotContainsString('username[]', $users);
        $this->assertStringNotContainsString('password[]', $users);
    }
}
