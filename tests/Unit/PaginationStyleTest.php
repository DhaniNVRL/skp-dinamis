<?php

namespace Tests\Unit;

use Illuminate\Pagination\LengthAwarePaginator;
use Tests\TestCase;

class PaginationStyleTest extends TestCase
{
    public function test_global_pagination_uses_blue_border_white_background_and_five_percent_gray_hover(): void
    {
        $paginator = new LengthAwarePaginator(
            collect(range(1, 10)),
            80,
            10,
            2,
            ['path' => '/testing']
        );

        $html = $paginator->links()->render();

        $this->assertStringContainsString('border-blue-500', $html);
        $this->assertStringContainsString('bg-white', $html);
        $this->assertStringContainsString('hover:bg-gray-500/5', $html);
        $this->assertStringNotContainsString('dark:bg-gray-800', $html);
    }
}