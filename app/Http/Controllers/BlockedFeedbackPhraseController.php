<?php

namespace App\Http\Controllers;

use App\Models\BlockedFeedbackPhrase;
use App\Services\BlockedFeedbackPhraseService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class BlockedFeedbackPhraseController extends Controller
{
    public function index(): View
    {
        return view('admin.blocked-feedback-phrases.index', [
            'phrases' => BlockedFeedbackPhrase::query()
                ->orderByDesc('is_active')
                ->orderBy('phrase')
                ->paginate(20),
        ]);
    }

    public function store(Request $request, BlockedFeedbackPhraseService $service): RedirectResponse
    {
        $validated = $request->validate([
            'phrase' => ['required', 'string', 'min:2', 'max:500'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $normalized = $service->normalize($validated['phrase']);
        $this->ensureUnique($normalized);

        BlockedFeedbackPhrase::query()->create([
            'phrase' => trim($validated['phrase']),
            'normalized_phrase' => $normalized,
            'normalized_hash' => hash('sha256', $normalized),
            'is_active' => $request->boolean('is_active'),
        ]);

        return back()->with('success', 'Kata atau kalimat terlarang berhasil ditambahkan.');
    }

    public function update(
        Request $request,
        BlockedFeedbackPhrase $blockedFeedbackPhrase,
        BlockedFeedbackPhraseService $service
    ): RedirectResponse {
        $validated = $request->validate([
            'phrase' => ['required', 'string', 'min:2', 'max:500'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $normalized = $service->normalize($validated['phrase']);
        $this->ensureUnique($normalized, $blockedFeedbackPhrase->id);

        $blockedFeedbackPhrase->update([
            'phrase' => trim($validated['phrase']),
            'normalized_phrase' => $normalized,
            'normalized_hash' => hash('sha256', $normalized),
            'is_active' => $request->boolean('is_active'),
        ]);

        return back()->with('success', 'Kata atau kalimat terlarang berhasil diperbarui.');
    }

    public function destroy(BlockedFeedbackPhrase $blockedFeedbackPhrase): RedirectResponse
    {
        $blockedFeedbackPhrase->delete();

        return back()->with('success', 'Kata atau kalimat terlarang berhasil dihapus.');
    }

    private function ensureUnique(string $normalized, ?int $exceptId = null): void
    {
        $query = BlockedFeedbackPhrase::query()
            ->where('normalized_hash', hash('sha256', $normalized));

        if ($exceptId !== null) {
            $query->whereKeyNot($exceptId);
        }

        if ($normalized === '' || $query->exists()) {
            throw ValidationException::withMessages([
                'phrase' => $normalized === ''
                    ? 'Kata atau kalimat tidak valid.'
                    : 'Kata atau kalimat tersebut sudah tersedia.',
            ]);
        }
    }
}
