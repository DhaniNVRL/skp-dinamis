<?php

namespace App\Services;

use App\Models\UnitCompetitorVisibility;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Schema;

class UnitCompetitorVisibilityService
{
    public function isConfigured(?int $unitId): bool
    {
        return $unitId !== null
            && Schema::hasTable('unit_competitor_visibilities')
            && UnitCompetitorVisibility::where('unit_id', $unitId)->exists();
    }

    public function filterForUnit(Collection $competitors, ?int $unitId): Collection
    {
        if (
            $unitId === null
            || ! Schema::hasTable('unit_competitor_visibilities')
        ) {
            return $competitors->values();
        }

        // Visibility bersifat opt-out: hanya konfigurasi eksplisit bernilai
        // false yang menyembunyikan kompetitor. Kompetitor tanpa konfigurasi
        // tetap ditampilkan agar data lama tetap kompatibel.
        $hiddenIds = UnitCompetitorVisibility::where('unit_id', $unitId)
            ->where('is_visible', false)
            ->pluck('competitor_id');

        return $competitors->whereNotIn('id', $hiddenIds)->values();
    }
}
