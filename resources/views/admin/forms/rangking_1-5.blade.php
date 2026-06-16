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

                {{-- Action --}}
                <div class="flex items-center gap-3 ml-4">

                    {{-- Edit --}}
                    <a href="{{ route('question.edit', $question->id) }}"
                       class="flex items-center justify-center w-8 h-8 rounded-full bg-yellow-100 text-yellow-600 hover:bg-yellow-200 transition"
                       title="Edit">
                        <i class="fa fa-edit"></i>
                    </a>

                    {{-- Delete --}}
                    <form action="{{ route('question.destroy', $question->id) }}"
                          method="POST"
                          onsubmit="return confirm('Hapus pertanyaan ini?')">
                        @csrf
                        @method('DELETE')

                        <button type="submit"
                                class="flex items-center justify-center w-8 h-8 rounded-full bg-red-100 text-red-600 hover:bg-red-200 transition"
                                title="Delete">
                            <i class="fa fa-trash"></i>
                        </button>
                    </form>

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

            <!-- Tombol Aksi -->
            <div class="flex items-center gap-3">
                <!-- Edit -->
                <a href="{{ route('question.edit', $question->id) }}"
                class="flex items-center justify-center w-8 h-8 rounded-full
                        bg-yellow-100 text-yellow-600 hover:bg-yellow-200 transition"
                title="Edit">
                    <i class="fa fa-edit"></i>
                </a>

                <!-- Delete -->
                <form action="{{ route('question.destroy', $question->id) }}"
                    method="POST"
                    onsubmit="return confirm('Hapus pertanyaan ini?')">
                    @csrf
                    @method('DELETE')

                    <button type="submit"
                            class="flex items-center justify-center w-8 h-8 rounded-full
                                bg-red-100 text-red-600 hover:bg-red-200 transition"
                            title="Delete">
                        <i class="fa fa-trash"></i>
                    </button>
                </form>
            </div>
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

                    <div class="flex gap-2">

                        <!-- Edit Button -->
                        <a href="{{ route('options.edit', $opsion->id) }}"
                        class="text-yellow-500 hover:text-yellow-600 transition"
                        title="Edit">
                            <i class="fa fa-edit"></i>
                        </a>

                        <!-- Delete Button -->
                        <form action="{{ route('options.destroy', $opsion->id) }}"
                            method="POST"
                            onsubmit="return confirm('Yakin ingin menghapus?')">
                            @csrf
                            @method('DELETE')

                            <button type="submit"
                                    class="text-red-500 hover:text-red-600 transition">
                                <i class="fa fa-trash"></i>
                            </button>
                        </form>

                    </div>
                </div>
            @endforeach
        </div>
    @endforeach

    <button
        type="button"
        class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg mt-3 transition"
        @click="
            const template = document.getElementById('options-form');
            if (!template) {
                alert('Template options-form tidak ditemukan!');
                return;
            }

            $dispatch('open-modal-tab', {
                title: 'Add Options',
                manual: '{{ route('options.store') }}',
                group: '{{ $groups->id }}',
                question: '{{ $question->id }}',
                content: template.innerHTML
            })
        ">
        Add Option
    </button>
@endforeach

    <div class="mt-6">
        <button
            type="button"
            class="bg-green-600 text-white px-4 py-2 rounded mt-2"
            @click="
                const template = document.getElementById('penilaian_1-5');
                if (!template) {
                    alert('Template penilaian_1-5 tidak ditemukan!');
                    return;
                }

                $dispatch('open-modal-tab', {
                    title: 'Add Question',
                    manual: '{{ route('question.store') }}',
                    group: '{{ $groups->id }}',
                    form: '{{ $form->id }}',
                    content: template.innerHTML
                    })
                ">
            Add Question
        </button>
    </div>


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