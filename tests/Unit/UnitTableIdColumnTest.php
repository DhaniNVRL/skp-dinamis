<?php

namespace Tests\Unit;

use Tests\TestCase;

class UnitTableIdColumnTest extends TestCase
{
    public function test_unit_table_displays_id_after_sequence_number(): void
    {
        $table = file_get_contents(resource_path('views/admin/units/units/partials/table.blade.php'));
        $row = file_get_contents(resource_path('views/admin/units/units/partials/row-action.blade.php'));

        $this->assertMatchesRegularExpression('/No\s*<\/th>\s*<th[\s\S]*?>\s*ID\s*<\/th>/', $table);
        $this->assertMatchesRegularExpression('/\{\{ \$number \}\}[\s\S]*?\{\{ \$unit->id \}\}/', $row);
        $this->assertSame(2, substr_count($table, 'colspan="5"'));
    }
}
