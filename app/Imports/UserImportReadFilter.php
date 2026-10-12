<?php

namespace App\Imports;

use PhpOffice\PhpSpreadsheet\Reader\IReadFilter;

class UserImportReadFilter implements IReadFilter
{
    public function readCell(string $columnAddress, int $row, string $worksheetName = ''): bool
    {
        return $row <= 1002 && in_array($columnAddress, ['A', 'B', 'C', 'D', 'E', 'F'], true);
    }
}
