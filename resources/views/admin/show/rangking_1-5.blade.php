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

        <div class="grid grid-cols-1 md:grid-cols-5 gap-4 mb-4">
            <div>
                <label class="block text-sm font-medium text-gray-600 mb-1">
                    Rangking 1
                </label>

                <div class="question-block space-y-2">

                    <div class="flex items-center gap-3">
                        <select class="ranking-select w-full border rounded-lg p-2"
                            name="answer_{{ $question->id }}">

                            <option value="">-- Pilih Jawaban --</option>

                            @foreach ($question->options as $opsion)
                                <option
                                    value="{{ $opsion->id }}"
                                    data-has-child="{{ $opsion->has_child }}">
                                    {{ $opsion->answer_text }}
                                </option>
                            @endforeach

                        </select>
                    </div>

                    <!-- CHILD TEXTAREA -->
                    <div class="child-textarea mt-2 hidden">
                        <textarea
                            name="child_answer_{{ $question->id }}"
                            class="border rounded-lg p-2 w-full"
                            placeholder="Tulis jawaban tambahan..."></textarea>
                    </div>
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-600 mb-1">
                    Rangking 2
                </label>

                <div class="question-block space-y-2">

                    <div class="flex items-center gap-3">
                        <select class="ranking-select w-full border rounded-lg p-2"
                            name="answer_{{ $question->id }}">

                            <option value="">-- Pilih Jawaban --</option>

                            @foreach ($question->options as $opsion)
                                <option
                                    value="{{ $opsion->id }}"
                                    data-has-child="{{ $opsion->has_child }}">
                                    {{ $opsion->answer_text }}
                                </option>
                            @endforeach

                        </select>
                    </div>

                    <!-- CHILD TEXTAREA -->
                    <div class="child-textarea mt-2 hidden">
                        <textarea
                            name="child_answer_{{ $question->id }}"
                            class="border rounded-lg p-2 w-full"
                            placeholder="Tulis jawaban tambahan..."></textarea>
                    </div>
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-600 mb-1">
                    Rangking 3
                </label>

                <div class="question-block space-y-2">

                    <div class="flex items-center gap-3">
                        <select class="ranking-select w-full border rounded-lg p-2"
                            name="answer_{{ $question->id }}">

                            <option value="">-- Pilih Jawaban --</option>

                            @foreach ($question->options as $opsion)
                                <option
                                    value="{{ $opsion->id }}"
                                    data-has-child="{{ $opsion->has_child }}">
                                    {{ $opsion->answer_text }}
                                </option>
                            @endforeach

                        </select>
                    </div>

                    <!-- CHILD TEXTAREA -->
                    <div class="child-textarea mt-2 hidden">
                        <textarea
                            name="child_answer_{{ $question->id }}"
                            class="border rounded-lg p-2 w-full"
                            placeholder="Tulis jawaban tambahan..."></textarea>
                    </div>
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-600 mb-1">
                    Rangking 4
                </label>

                <div class="question-block space-y-2">

                    <div class="flex items-center gap-3">
                        <select class="ranking-select w-full border rounded-lg p-2"
                            name="answer_{{ $question->id }}">

                            <option value="">-- Pilih Jawaban --</option>

                            @foreach ($question->options as $opsion)
                                <option
                                    value="{{ $opsion->id }}"
                                    data-has-child="{{ $opsion->has_child }}">
                                    {{ $opsion->answer_text }}
                                </option>
                            @endforeach

                        </select>
                    </div>

                    <!-- CHILD TEXTAREA -->
                    <div class="child-textarea mt-2 hidden">
                        <textarea
                            name="child_answer_{{ $question->id }}"
                            class="border rounded-lg p-2 w-full"
                            placeholder="Tulis jawaban tambahan..."></textarea>
                    </div>
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-600 mb-1">
                    Rangking 5
                </label>

                <div class="question-block space-y-2">

                    <div class="flex items-center gap-3">
                        <select class="ranking-select w-full border rounded-lg p-2"
                            name="answer_{{ $question->id }}">

                            <option value="">-- Pilih Jawaban --</option>

                            @foreach ($question->options as $opsion)
                                <option
                                    value="{{ $opsion->id }}"
                                    data-has-child="{{ $opsion->has_child }}">
                                    {{ $opsion->answer_text }}
                                </option>
                            @endforeach

                        </select>
                    </div>

                    <!-- CHILD TEXTAREA -->
                    <div class="child-textarea mt-2 hidden">
                        <textarea
                            name="child_answer_{{ $question->id }}"
                            class="border rounded-lg p-2 w-full"
                            placeholder="Tulis jawaban tambahan..."></textarea>
                    </div>
                </div>
            </div>
        </div>

        <!-- List option untuk edit/delete -->
        <div class="space-y-2">
            @foreach ($question->options as $opsion)
                <div class="flex items-center justify-between p-3 border rounded-lg">

                    <span class="text-gray-700">
                        {{ $opsion->answer_text }}
                    </span>
                </div>
            @endforeach
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