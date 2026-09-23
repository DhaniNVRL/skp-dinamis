<?php

namespace App\Services;

use App\Models\Form;
use App\Models\UnitFormVisibility;
use App\Models\UserProfile;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Schema;

class UnitFormVisibilityService
{
    private array $visibilityCache = [];

    private array $userUnitCache = [];

    public function isVisibleForUser(Form $form, int $userId): bool
    {
        if (!Schema::hasTable('user_profiles')) {
            return true;
        }

        if (!array_key_exists($userId, $this->userUnitCache)) {
            $this->userUnitCache[$userId] = UserProfile::query()
                ->where('user_id', $userId)
                ->value('unit_id');
        }

        $unitId = $this->userUnitCache[$userId];

        return !$unitId || $this->isVisibleForUnit((int) $form->id, (int) $unitId);
    }

    public function isVisibleForUnit(int $formId, int $unitId): bool
    {
        if (!Schema::hasTable('unit_form_visibilities')) {
            return true;
        }

        $key = $unitId.'-'.$formId;

        if (!array_key_exists($key, $this->visibilityCache)) {
            $value = UnitFormVisibility::query()
                ->where('unit_id', $unitId)
                ->where('form_id', $formId)
                ->value('is_visible');

            $this->visibilityCache[$key] = $value === null || (bool) $value;
        }

        return $this->visibilityCache[$key];
    }

    public function visibilityMap(Collection $forms, int $unitId): array
    {
        if (!Schema::hasTable('unit_form_visibilities')) {
            return $forms->mapWithKeys(fn (Form $form) => [(int) $form->id => true])->all();
        }

        $configured = UnitFormVisibility::query()
            ->where('unit_id', $unitId)
            ->whereIn('form_id', $forms->pluck('id'))
            ->pluck('is_visible', 'form_id');

        return $forms->mapWithKeys(function (Form $form) use ($configured, $unitId): array {
            $visible = !$configured->has($form->id) || (bool) $configured->get($form->id);
            $this->visibilityCache[$unitId.'-'.$form->id] = $visible;

            return [(int) $form->id => $visible];
        })->all();
    }
}
