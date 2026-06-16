@foreach ($questions->groupBy('no_header') as $noHeader => $group)

    @foreach ($group->where('id_questiontypes', 1)->sortBy('no') as $question)
         <div class="mb-6 p-4 border rounded-lg shadow-sm bg-blue-100">
            <div class="flex items-center justify-between">
                
                <!-- Judul -->
                <div class="flex-1">
                    <h4 class="font-bold text-lg text-center text-gray-800">
                        {{ $question->name }}
                    </h4>
                </div>

                <!-- Tombol Aksi -->
                <div class="flex items-center gap-3 ml-4">
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
        </div>
    @endforeach

    {{-- PERTANYAAN --}}
    @foreach ($group->where('id_questiontypes', 2)->sortBy('no') as $question)
        <div class="mb-4 p-4 bg-white border rounded-lg shadow-sm">
            <div class="border rounded-lg p-4">

                <!-- Header Pertanyaan -->
                <div class="flex items-center justify-between mb-4">

                    <label class="font-semibold text-gray-700">
                        {{ $question->no_header }}{{ $question->no }}. {{ $question->name }}
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

                <!-- Pilihan Jawaban -->
                <div class="flex flex-wrap justify-center gap-4">
                    @foreach ([1,2,3,4,5,6,7] as $value)
                        <label class="flex items-center gap-1">
                            <input
                                type="radio"
                                x-model="question1"
                                name="question1_{{ $question->id }}"
                                value="{{ $value }}"
                            >
                            <span>{{ $value }}</span>
                        </label>
                    @endforeach
                </div>

            </div>
        </div>
    @endforeach
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