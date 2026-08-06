<div class="space-y-5">

    @forelse ($questions as $question)

        @include(
            'admin.units.question.partials.forms.question-card',
            [
                'question' => $question
            ]
        )

    @empty

        <div class="rounded-xl border border-dashed border-gray-300 bg-white py-12 text-center">
            Belum ada pertanyaan
        </div>

    @endforelse

</div>