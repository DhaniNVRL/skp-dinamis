<?php

namespace App\Services;

use App\Models\Form;
use Illuminate\Support\Collection;

class FormMergeService
{
    public function root(Form $form): Form
    {
        return $form->merge_parent_id
            ? Form::query()->findOrFail($form->merge_parent_id)
            : $form;
    }

    public function members(Form $form): Collection
    {
        $root = $this->root($form);

        return Form::query()
            ->where('id', $root->id)
            ->orWhere('merge_parent_id', $root->id)
            ->orderBy('no_urut')
            ->orderBy('id')
            ->get();
    }

    public function pages(Collection $forms): Collection
    {
        return $forms->whereNull('merge_parent_id')->values();
    }
}
