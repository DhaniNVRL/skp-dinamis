<?php

namespace Tests\Unit;

use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class AdminFormCardIdBadgeTest extends TestCase
{
    #[Test]
    public function form_card_shows_database_id_before_sequence_number(): void
    {
        $view = file_get_contents(
            resource_path('views/admin/units/question/partials/form-card.blade.php')
        );

        $idPosition = strpos($view, 'ID={{ $form->id }}');
        $sequencePosition = strpos($view, '{{ $form->no_urut }}');

        $this->assertNotFalse($idPosition);
        $this->assertNotFalse($sequencePosition);
        $this->assertLessThan($sequencePosition, $idPosition);
    }
}
