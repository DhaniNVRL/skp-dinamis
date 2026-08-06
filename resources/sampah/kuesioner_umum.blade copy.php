@foreach ($questions->sortBy('no') as $question)
    <div class="mb-6 p-4 border rounded-lg shadow-sm bg-white">

        <!-- HEADER QUESTION -->
        <div class="flex justify-between items-start gap-4">

            <label class="font-semibold text-gray-800">
                {{ $question->no }}. {{ $question->name }}
            </label>

            <!-- ACTION -->
            <div class="flex items-center gap-3">

                <!-- EDIT -->
                <a href="{{ route('question.edit', $question->id) }}"
                   class="text-yellow-500 hover:text-yellow-600 transition"
                   title="Edit">
                    <i class="fa fa-edit"></i>
                </a>

                <!-- DELETE -->
                <form action="{{ route('question.destroy', $question->id) }}"
                      method="POST"
                      onsubmit="return confirm('Hapus pertanyaan ini?')">
                    @csrf
                    @method('DELETE')

                    <button type="submit"
                            class="text-red-500 hover:text-red-600 transition"
                            title="Delete">
                        <i class="fa fa-trash"></i>
                    </button>
                </form>

            </div>
        </div>

        <!-- BODY QUESTION -->
        <div class="mt-4">

            @if ($question->id_questiontypes == 1)

                <input type="text"
                       name="answers[{{ $question->id }}]"
                       class="border rounded-lg p-2 w-full focus:ring focus:ring-blue-200 validate-required">

            @elseif ($question->id_questiontypes == 2)

                <textarea name="answers[{{ $question->id }}]"
                          class="border rounded-lg p-2 w-full focus:ring focus:ring-blue-200 validate-required"></textarea>

            @elseif ($question->id_questiontypes == 3)

                <div class="question-block space-y-2">

                    @foreach ($question->options as $opsion)
                        <div class="flex items-center justify-between p-3 border rounded-lg hover:bg-blue-50 hover:border-blue-400 transition">

                            <label class="flex items-center gap-3 cursor-pointer w-full">
                                <input
                                    type="radio"
                                    name="answer_{{ $question->id }}"
                                    value="{{ $opsion->id }}"
                                    data-has-child="{{ $opsion->has_child }}"
                                    class="w-4 h-4 text-blue-600 option-radio">

                                <span class="text-gray-700">
                                    {{ $opsion->answer_text }}
                                </span>
                            </label>

                            <!-- Action Buttons -->
                            <div class="flex gap-2 ml-4">

                                <!-- Edit Button -->
                                <a href="{{ route('options.edit', $opsion->id) }}" class="text-yellow-500 hover:text-yellow-600 transition" title="Edit">
                                   <i class="fa fa-edit"></i>
                                </a>

                                <!-- Delete Button -->
                                <form action="{{ route('options.destroy', $opsion->id) }}" method="POST"
                                    onsubmit="return confirm('Yakin ingin menghapus?')">
                                    @csrf
                                    @method('DELETE')

                                    <button type="submit" class="text-red-500 hover:text-red-600 transition">
                                        <i class="fa fa-trash"></i>
                                    </button>
                                </form>

                            </div>
                        </div>
                    @endforeach

                    <!-- CHILD TEXTAREA -->
                    <div class="child-textarea mt-2 hidden">
                        <textarea
                            name="child_answer_{{ $question->id }}"
                            class="border rounded-lg p-2 w-full focus:ring focus:ring-blue-200"
                            placeholder="Tulis jawaban tambahan..."></textarea>
                    </div>
                </div>

                <!-- ADD OPTION BUTTON -->
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

            @elseif ($question->id_questiontypes == 4)

                <div class="question-block space-y-2">

                    @foreach ($question->options as $opsion)
                        <div class="p-3 border rounded-lg hover:bg-blue-50 hover:border-blue-400 transition">

                            <div class="flex items-center justify-between">
                                <label class="flex items-center gap-3 cursor-pointer w-full">
                                    <input
                                        type="checkbox"
                                        name="answer_{{ $question->id }}[]"
                                        value="{{ $opsion->id }}"
                                        data-has-child="{{ $opsion->has_child }}"
                                        class="w-4 h-4 text-blue-600 option-checkbox">

                                    <span class="text-gray-700">
                                        {{ $opsion->answer_text }}
                                    </span>
                                </label>

                                <!-- Action Buttons -->
                                <div class="flex gap-2 ml-4">
                                    <a href="{{ route('options.edit', $opsion->id) }}" class="text-yellow-500 hover:text-yellow-600 transition">
                                        <i class="fa fa-edit"></i>
                                    </a>

                                    <form action="{{ route('options.destroy', $opsion->id) }}" method="POST"
                                        onsubmit="return confirm('Yakin ingin menghapus?')">
                                        @csrf
                                        @method('DELETE')

                                        <button type="submit" class="text-red-500 hover:text-red-600 transition">
                                            <i class="fa fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </div>

                            <!-- CHILD TEXTAREA (per option) -->
                            <div class="child-textarea mt-2 hidden">
                                <textarea
                                    name="child_answer_{{ $question->id }}[{{ $opsion->id }}]"
                                    class="border rounded-lg p-2 w-full focus:ring focus:ring-blue-200"
                                    placeholder="Tulis jawaban tambahan..."></textarea>
                            </div>

                        </div>
                    @endforeach

                </div>

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

            @elseif ($question->id_questiontypes == 6)

                <input type="text"
                       name="answers[{{ $question->id }}]"
                       class="border rounded-lg p-2 w-full focus:ring focus:ring-blue-200 validate-number" >

            @elseif ($question->id_questiontypes == 7)
                <input type="date"
                       name="answers[{{ $question->id }}]"
                       class="border rounded-lg p-2 w-full focus:ring focus:ring-blue-200" >
            @elseif ($question->id_questiontypes == 8)
            
                <input type="email"
                    name="answers[{{ $question->id }}]"
                    class="border rounded-lg p-2 w-full focus:ring focus:ring-blue-200 validate-email">
            
            @elseif ($question->id_questiontypes == 9)

                <input type="text"
                       name="answers[{{ $question->id }}]"
                       class="border rounded-lg p-2 w-full focus:ring focus:ring-blue-200 validate-number" >
            @else
                <p class="text-red-500 text-sm">
                    Type belum dibuat. ({{ $question->id_questiontypes }})
                </p>
            @endif

            
        </div>
    </div>
@endforeach
    
<!-- ADD QUESTION -->
<div class="mt-6">
    <button
        type="button"
        class="bg-green-600 text-white px-4 py-2 rounded mt-2"
        @click="
            const template = document.getElementById('question-form');
            if (!template) {
                alert('Template question-form tidak ditemukan!');
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

// Radio
document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('.question-block').forEach(function (block) {

        const radios = block.querySelectorAll('.option-radio');
        const textarea = block.querySelector('.child-textarea');

        radios.forEach(function (radio) {
            radio.addEventListener('change', function () {

                if (this.dataset.hasChild == "1") {
                    textarea.classList.remove('hidden');
                } else {
                    textarea.classList.add('hidden');
                }

            });
        });

    });
});

// Check Box
document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('.option-checkbox').forEach(function (checkbox) {

        checkbox.addEventListener('change', function () {
            const wrapper = this.closest('div.p-3');
            const textarea = wrapper.querySelector('.child-textarea');

            if (this.dataset.hasChild == "1" && this.checked) {
                textarea.classList.remove('hidden');
            } else {
                textarea.classList.add('hidden');
            }
        });

    });
});
</script>
