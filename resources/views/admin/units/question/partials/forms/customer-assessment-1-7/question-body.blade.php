@switch((int) $question->questiontype_id)

    @case(1)
        @include(
            'admin.units.question.partials.forms.customer-assessment-1-7.options.title',
            compact('question')
        )
        @break

    {{-- Kepentingan dan Kinerja tanpa alasan --}}
    @case(2)
        @include(
            'admin.units.question.partials.forms.customer-assessment-1-7.options.importance-performance',
            compact('question')
        )
        @break

    {{-- Kepentingan dan Kinerja dengan textarea alasan --}}
    @case(3)
        @include(
            'admin.units.question.partials.forms.customer-assessment-1-7.options.importance-performance-reason',
            compact('question')
        )
        @break

    {{-- Kepentingan dan Kinerja dengan checkbox alasan --}}
    @case(4)
        @include(
            'admin.units.question.partials.forms.customer-assessment-1-7.options.importance-performance-options',
            compact('question')
        )
        @break

    {{-- Penilaian satu indikator --}}
    @case(5)
        @include(
            'admin.units.question.partials.forms.customer-assessment-1-7.options.single-indicator',
            compact('question')
        )
        @break

    {{-- Jawaban textarea --}}
    @case(6)
        @include(
            'admin.units.question.partials.forms.customer-assessment-1-7.options.textarea',
            compact('question')
        )
        @break

    @default

        <div
            class="rounded-xl border border-amber-200
                   bg-amber-50 px-4 py-3"
        >
            <div class="flex items-center gap-2 text-sm text-amber-700">

                <i class="fa-solid fa-triangle-exclamation"></i>

                <span>
                    Tampilan untuk tipe pertanyaan
                    <strong>#{{ $question->questiontype_id }}</strong>
                    belum tersedia.
                </span>

            </div>
        </div>

@endswitch