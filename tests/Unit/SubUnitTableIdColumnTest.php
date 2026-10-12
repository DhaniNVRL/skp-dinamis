<?php

namespace Tests\Unit;

use Tests\TestCase;

class SubUnitTableIdColumnTest extends TestCase
{
    public function test_sub_unit_table_displays_id_after_sequence_number(): void
    {
        $table = file_get_contents(resource_path('views/admin/subunit/subunit/partials/table.blade.php'));

        $this->assertMatchesRegularExpression('/No\s*<\/th>\s*<th[\s\S]*?>\s*ID\s*<\/th>/', $table);
        $this->assertMatchesRegularExpression(
            '/\{\{ \$subunits->firstItem\(\) \+ \$loop->index \}\}[\s\S]*?\{\{ \$subunit->id \}\}/',
            $table
        );
        $this->assertStringContainsString('colspan="5"', $table);
    }
}
