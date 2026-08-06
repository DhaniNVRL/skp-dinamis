@if($form->description)
    <div class="relative bg-white rounded-xl shadow border border-gray-200 p-6">
        <!-- content -->
        @if($form->description)
            {!! $form->description->content !!}
        @else
            <div class="text-gray-400 italic">
                Belum ada description.
            </div>
        @endif

    </div>
@endif

@foreach ($questions->groupBy('no_header') as $noHeader => $group)

    @foreach ($group->where('id_questiontypes', 1)->sortBy('no') as $question)
        <div class="mb-6 p-4 border rounded-lg shadow-sm bg-blue-100">
            <div class="flex items-center justify-between">

                {{-- Judul --}}
                <div class="flex-1">
                    <h5 class="font-semibold text-gray-800 mb-4">
                        {{ $question->name }}
                    </h5>
                </div>
            </div>
        </div>
    @endforeach

    {{-- PERTANYAAN --}}
    @foreach ($group->where('id_questiontypes', 2)->sortBy('no') as $question)

        <div class="flex items-center justify-between mb-4">
            <label class="font-semibold text-gray-700">
                {{ $question->no_header }}. {{ $question->name }}
            </label>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-1 gap-5">
            <!-- Saran -->
            <div class="bg-blue-100 border border-blue-100 rounded-lg p-4">
                <label class="block text-sm font-semibold text-blue-700 mb-2">
                    Saran
                </label>
                <textarea
                    rows="12"
                    class="w-full rounded-lg border border-gray-300 focus:border-blue-500 focus:ring-2 focus:ring-blue-200 resize-none p-3 text-sm"
                    placeholder="Berikan ide, masukan, atau rekomendasi untuk peningkatan kualitas layanan..."></textarea>
            </div>

        </div>
    @endforeach
@endforeach

<script>
    document.addEventListener('change', function (e) {

        // =======================
        // CHILD TEXTAREA LOGIC
        // =======================
        if (e.target.classList.contains('ranking-select')) {

            const selectedOption = e.target.options[e.target.selectedIndex];
            const hasChild = selectedOption?.dataset?.hasChild;

            const questionBlock = e.target.closest('.question-block');
            const childTextarea = questionBlock.querySelector('.child-textarea');

            if (!childTextarea) return;

            if (hasChild === '1') {
                childTextarea.classList.remove('hidden');
            } else {
                childTextarea.classList.add('hidden');
            }
        }

        // =======================
        // UNIQUE SELECTION LOGIC
        // =======================
        const selects = document.querySelectorAll('.ranking-select');

        const selectedValues = Array.from(selects)
            .map(s => s.value)
            .filter(v => v);

        selects.forEach(select => {

            const currentValue = select.value;

            Array.from(select.options).forEach(option => {

                if (!option.value) return;

                option.hidden =
                    selectedValues.includes(option.value) &&
                    option.value !== currentValue;
            });
        });

    });
</script>