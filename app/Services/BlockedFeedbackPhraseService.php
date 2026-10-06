<?php

namespace App\Services;

use App\Models\BlockedFeedbackPhrase;
use Illuminate\Support\Facades\Schema;

class BlockedFeedbackPhraseService
{
    private ?array $activePhrases = null;

    public function normalize(mixed $value): string
    {
        if (! is_scalar($value)) {
            return '';
        }

        $normalized = mb_strtolower(trim((string) $value), 'UTF-8');
        $normalized = preg_replace('/[^\p{L}\p{N}]+/u', ' ', $normalized);
        $normalized = preg_replace('/\s+/u', ' ', (string) $normalized);

        return trim((string) $normalized);
    }

    public function active(): array
    {
        if ($this->activePhrases !== null) {
            return $this->activePhrases;
        }

        if (! Schema::hasTable('blocked_feedback_phrases')) {
            return $this->activePhrases = [];
        }

        return $this->activePhrases = BlockedFeedbackPhrase::query()
            ->where('is_active', true)
            ->orderBy('normalized_phrase')
            ->get(['phrase', 'normalized_phrase'])
            ->map(fn (BlockedFeedbackPhrase $item): array => [
                'phrase' => $item->phrase,
                'normalized' => $item->normalized_phrase,
            ])
            ->all();
    }

    public function findMatch(mixed $value): ?string
    {
        $answer = $this->normalize($value);

        if ($answer === '') {
            return null;
        }

        foreach ($this->active() as $item) {
            $blocked = $item['normalized'];
            $matches = str_contains($blocked, ' ')
                ? str_contains(' '.$answer.' ', ' '.$blocked.' ')
                : preg_match('/(?:^|\s)'.preg_quote($blocked, '/').'(?:$|\s)/u', $answer) === 1;

            if ($matches) {
                return $item['phrase'];
            }
        }

        return null;
    }
}
