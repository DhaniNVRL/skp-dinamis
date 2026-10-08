<?php

namespace Tests\Unit;

use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class CreateModalRowNumberTest extends TestCase
{
    #[Test]
    public function create_modals_start_numbering_after_existing_records(): void
    {
        $views = [
            'admin/activities/modals/create.blade.php' => '$activities->count() + 1',
            'admin/groups/groups/modals/create.blade.php' => '$groups->count() + 1',
            'admin/units/units/modals/create.blade.php' => '$units->count() + 1',
            'admin/subunit/subunit/modals/create-subunit.blade.php' => '$allSubunits->count() + 1',
            'admin/users/modals/create.blade.php' => '$userCreateStartNumber',
        ];

        foreach ($views as $path => $expected) {
            $view = file_get_contents(resource_path('views/'.$path));

            $this->assertStringContainsString('data-start-number=', $view, $path);
            $this->assertStringContainsString($expected, $view, $path);
        }
    }

    #[Test]
    public function row_refresh_uses_the_configured_start_number(): void
    {
        $dynamicTable = file_get_contents(resource_path('js/admin/global/dynamic-table.js'));
        $units = file_get_contents(resource_path('js/admin/units.js'));
        $subunits = file_get_contents(resource_path('js/admin/subunit.js'));

        foreach ([$dynamicTable, $units, $subunits] as $script) {
            $this->assertStringContainsString('dataset.startNumber', $script);
            $this->assertStringContainsString('startNumber + index', $script);
        }

        $this->assertStringContainsString('getElementById(', $units);
        $this->assertStringContainsString('"addUnitRow"', $units);
        $this->assertStringContainsString('"[data-create-unit-row]"', $units);
        $this->assertStringNotContainsString('".create-unit-row"', $units);

        $unitTemplate = file_get_contents(
            resource_path('views/admin/units/units/templates/create-row.blade.php')
        );
        $this->assertStringContainsString('$units->count() + 1', $unitTemplate);
    }

    #[Test]
    public function dynamic_add_buttons_target_the_table_that_owns_the_template(): void
    {
        $views = [
            'admin/activities/modals/create.blade.php' => 'activityCreateTable',
            'admin/groups/groups/modals/create.blade.php' => 'groupCreateTable',
            'admin/users/modals/create.blade.php' => 'userCreateTable',
        ];

        foreach ($views as $path => $tableId) {
            $view = file_get_contents(resource_path('views/'.$path));

            $this->assertStringContainsString('data-add-row="'.$tableId.'"', $view, $path);
            $this->assertStringContainsString('id="'.$tableId.'"', $view, $path);
        }
    }
}
