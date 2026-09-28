<?php

namespace App\Http\Controllers;

use App\Models\Form;
use App\Models\SurveyDraft;
use App\Models\SurveySession;
use App\Models\UserProfile;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SurveyDraftController extends Controller
{
    public function store(Request $request, Form $form): JsonResponse
    {
        $profile = UserProfile::query()->where('user_id', Auth::id())->firstOrFail();
        abort_unless((int) $form->group_id === (int) $profile->group_id, 403);

        $session = SurveySession::query()->where('user_id', Auth::id())->first();
        abort_unless($session, 409, 'Sesi survei belum dimulai.');
        abort_if($session?->status === 'completed', 403, 'Survei sudah selesai.');
        abort_unless((int) $session->current_form_id === (int) $form->id, 409, 'Form ini bukan form aktif.');

        $validated = $request->validate([
            'fields' => ['present', 'array', 'max:10000'],
            'fields.*.name' => ['required', 'string', 'max:1000'],
            'fields.*.value' => ['nullable', 'string', 'max:20000'],
        ]);

        $fields = collect($validated['fields'])
            ->filter(fn (array $field): bool => preg_match('/^(answers|respondent_competitors)\[/', $field['name']) === 1)
            ->map(fn (array $field): array => [
                'name' => $field['name'],
                'value' => $field['value'] ?? '',
            ])
            ->values()
            ->all();

        $draft = SurveyDraft::query()->updateOrCreate(
            ['user_id' => Auth::id(), 'form_id' => $form->id],
            ['payload' => $fields]
        );

        return response()->json([
            'saved' => true,
            'saved_at' => $draft->updated_at?->toIso8601String(),
        ]);
    }
}
