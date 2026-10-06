<?php

namespace Tests\Unit;

use App\Models\BlockedFeedbackPhrase;
use App\Services\BlockedFeedbackPhraseService;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class BlockedFeedbackPhraseServiceTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Schema::create('blocked_feedback_phrases', function (Blueprint $table): void {
            $table->id();
            $table->string('phrase');
            $table->string('normalized_phrase');
            $table->char('normalized_hash', 64)->unique();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    protected function tearDown(): void
    {
        Schema::dropIfExists('blocked_feedback_phrases');

        parent::tearDown();
    }

    public function test_active_word_is_matched_as_a_complete_word(): void
    {
        BlockedFeedbackPhrase::query()->create([
            'phrase' => 'buruk',
            'normalized_phrase' => 'buruk',
            'normalized_hash' => hash('sha256', 'buruk'),
            'is_active' => true,
        ]);

        $service = new BlockedFeedbackPhraseService();

        $this->assertSame('buruk', $service->findMatch('Pelayanannya sangat buruk.'));
        $this->assertNull($service->findMatch('Ini bukan kata keburukan.'));
    }

    public function test_active_sentence_is_matched_and_inactive_sentence_is_ignored(): void
    {
        BlockedFeedbackPhrase::query()->create([
            'phrase' => 'tidak ada keluhan',
            'normalized_phrase' => 'tidak ada keluhan',
            'normalized_hash' => hash('sha256', 'tidak ada keluhan'),
            'is_active' => true,
        ]);
        BlockedFeedbackPhrase::query()->create([
            'phrase' => 'semua baik',
            'normalized_phrase' => 'semua baik',
            'normalized_hash' => hash('sha256', 'semua baik'),
            'is_active' => false,
        ]);

        $service = new BlockedFeedbackPhraseService();

        $this->assertSame(
            'tidak ada keluhan',
            $service->findMatch('Menurut saya, tidak ada keluhan untuk layanan ini.')
        );
        $this->assertNull($service->findMatch('Semua baik untuk saat ini.'));
    }
}
