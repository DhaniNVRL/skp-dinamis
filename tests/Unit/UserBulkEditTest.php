<?php

namespace Tests\Unit;

use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class UserBulkEditTest extends TestCase
{
    #[Test]
    public function user_page_exposes_bulk_edit_controls(): void
    {
        $actions = file_get_contents(resource_path('views/admin/users/partials/bulk-action.blade.php'));
        $index = file_get_contents(resource_path('views/admin/users/index.blade.php'));
        $modal = file_get_contents(resource_path('views/admin/users/modals/bulk-edit.blade.php'));
        $script = file_get_contents(resource_path('js/admin/users.js'));

        $this->assertStringContainsString('id="btnBulkEditUser"', $actions);
        $this->assertStringContainsString("admin.users.modals.bulk-edit", $index);
        $this->assertStringContainsString("route('admin.datauser.bulk-update')", $modal);
        $this->assertStringContainsString('data-bulk-apply=', $modal);
        $this->assertStringContainsString('function initializeBulkUserEdit()', $script);
        $this->assertStringContainsString('input.name = "ids[]"', $script);
    }

    #[Test]
    public function backend_bulk_update_only_changes_enabled_fields(): void
    {
        $routes = file_get_contents(base_path('routes/web.php'));
        $controller = file_get_contents(app_path('Http/Controllers/DataUserController.php'));

        $this->assertStringContainsString("Route::put('/datauser/bulk-update', 'bulkUpdate')", $routes);
        $this->assertStringContainsString('public function bulkUpdate(Request $request)', $controller);
        $this->assertStringContainsString("boolean('apply_role')", $controller);
        $this->assertStringContainsString("boolean('apply_activity')", $controller);
        $this->assertStringContainsString("boolean('apply_group')", $controller);
        $this->assertStringContainsString("boolean('apply_unit')", $controller);
        $this->assertStringContainsString('Role akun yang sedang digunakan tidak dapat diubah.', $controller);
    }
}
