<?php

namespace Tests\Unit;

use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class UserBulkSurveyActionsTest extends TestCase
{
    #[Test]
    public function user_page_exposes_bulk_reset_and_unlock_controls(): void
    {
        $actions = file_get_contents(resource_path('views/admin/users/partials/bulk-action.blade.php'));
        $index = file_get_contents(resource_path('views/admin/users/index.blade.php'));
        $script = file_get_contents(resource_path('js/admin/users.js'));

        $this->assertStringContainsString('id="btnBulkResetAccount"', $actions);
        $this->assertStringContainsString('id="btnBulkUnlockSubmit"', $actions);
        $this->assertStringContainsString("admin.users.modals.bulk-reset-account", $index);
        $this->assertStringContainsString("admin.users.modals.bulk-unlock-submit", $index);
        $this->assertStringContainsString('function initializeBulkSurveyActions()', $script);
    }

    #[Test]
    public function backend_defines_bulk_reset_and_unlock_actions(): void
    {
        $routes = file_get_contents(base_path('routes/web.php'));
        $controller = file_get_contents(app_path('Http/Controllers/DataUserController.php'));

        $this->assertStringContainsString("Route::post('/datauser/bulk-reset-account', 'bulkResetAccount')", $routes);
        $this->assertStringContainsString("Route::post('/datauser/bulk-unlock-submit', 'bulkUnlockSubmit')", $routes);
        $this->assertStringContainsString('public function bulkResetAccount(Request $request)', $controller);
        $this->assertStringContainsString('public function bulkUnlockSubmit(Request $request)', $controller);
        $this->assertStringContainsString("SurveyDraft::query()->whereIn('user_id', \$ids)->delete()", $controller);
        $this->assertStringContainsString("'status' => 'in_progress'", $controller);
        $this->assertStringContainsString('Jawaban tetap dipertahankan.', $controller);
    }
}
