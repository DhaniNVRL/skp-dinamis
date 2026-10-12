<?php

namespace Tests\Unit;

use App\Imports\UserImportReadFilter;
use Tests\TestCase;

class UserImportCapacityTest extends TestCase
{
    public function test_excel_reader_only_loads_input_cells_for_one_thousand_users(): void
    {
        $filter = new UserImportReadFilter();

        $this->assertTrue($filter->readCell('A', 1, 'Template Import'));
        $this->assertTrue($filter->readCell('F', 1001, 'Template Import'));
        $this->assertTrue($filter->readCell('A', 1002, 'Template Import'));
        $this->assertFalse($filter->readCell('G', 2, 'Template Import'));
        $this->assertFalse($filter->readCell('A', 1003, 'Template Import'));
    }

    public function test_import_uses_read_only_workbook_and_batch_inserts(): void
    {
        $controller = file_get_contents(app_path('Http/Controllers/DataUserController.php'));

        $this->assertStringContainsString('setReadDataOnly(true)', $controller);
        $this->assertStringContainsString("setLoadSheetsOnly(['Template Import'])", $controller);
        $this->assertStringContainsString('new UserImportReadFilter()', $controller);
        $this->assertStringContainsString('Maksimal 1.000 user', $controller);
        $this->assertStringContainsString("['rounds' => 9]", $controller);
        $this->assertStringContainsString('chunk(200)', $controller);
    }
}
