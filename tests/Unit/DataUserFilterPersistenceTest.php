<?php

namespace Tests\Unit;

use Tests\TestCase;

class DataUserFilterPersistenceTest extends TestCase
{
    public function test_user_filters_are_saved_and_only_reset_explicitly(): void
    {
        $controller = file_get_contents(app_path('Http/Controllers/DataUserController.php'));
        $filter = file_get_contents(resource_path('views/admin/users/partials/filter.blade.php'));

        $this->assertStringContainsString("private const FILTER_SESSION_KEY = 'admin.datauser.filters';", $controller);
        $this->assertStringContainsString("'sort_direction'", $controller);
        $this->assertStringContainsString("'page'", $controller);
        $this->assertStringContainsString('session()->put(self::FILTER_SESSION_KEY', $controller);
        $this->assertStringContainsString('session()->get(self::FILTER_SESSION_KEY', $controller);
        $this->assertStringContainsString("['reset_filters' => 1]", $filter);
    }
}
