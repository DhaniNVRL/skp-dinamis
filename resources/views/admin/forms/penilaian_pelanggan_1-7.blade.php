@foreach ($questions->groupBy('no_header') as $noHeader => $group)

    {{-- HEADER --}}
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
       <div class="mb-6 p-4 border rounded-lg shadow-sm bg-white">
            <div x-data="{ kepentingan: '', kinerja: '' }" class="border rounded-lg p-4">
                <div class="flex items-start justify-between mb-4">
                    <div class="font-semibold text-gray-800 pr-4">
                        {{ $question->no_header }}{{ $question->no }}. {{ $question->name }}
                    </div>

                <!-- Action -->
                    <div class="flex items-center gap-3 shrink-0">

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

                <div class="grid grid-cols-2 gap-8">

                    <!-- KEPENTINGAN -->
                    <div>
                        <div class="font-semibold mb-2 text-center">
                            Kepentingan
                        </div>

                        <div class="flex flex-wrap justify-center gap-4">
                            @foreach ([1,2,3,4,5,6,7,0] as $value)
                                <label class="flex items-center gap-1">
                                    <input
                                        type="radio"
                                        x-model="kepentingan"
                                        name="kepentingan_{{ $question->id }}"
                                        value="{{ $value }}"
                                    >
                                    <span>{{ $value }}</span>
                                </label>
                            @endforeach
                        </div>
                    </div>

                    <!-- KINERJA -->
                    <div>
                        <div class="font-semibold mb-2 text-center">
                            Kinerja
                        </div>

                        <div class="flex flex-wrap justify-center gap-4">
                            @foreach ([1,2,3,4,5,6,7,0] as $value)
                                <label class="flex items-center gap-1">
                                    <input
                                        type="radio"
                                        x-model="kinerja"
                                        name="kinerja_{{ $question->id }}"
                                        value="{{ $value }}"
                                    >
                                    <span>{{ $value }}</span>
                                </label>
                            @endforeach
                        </div>
                    </div>

                </div>

                <!-- TEXTAREA ALASAN -->
                <div x-show="kinerja != '' && kinerja <= 4 && kinerja != 0"
                    x-transition
                    class="mt-6">

                    <label class="block font-semibold mb-2 text-red-600">
                        Alasan penilaian kinerja
                        (jika menilai kinerja ≤ 3, mohon jelaskan alasannya)
                    </label>

                    <textarea
                        name="alasan_{{ $question->id }}"
                        rows="4"
                        class="w-full border rounded-lg p-3"
                        placeholder="Tuliskan alasan penilaian Anda..."
                    ></textarea>

                </div>
            </div>
        </div>
    @endforeach

    {{-- INDIKATOR --}}
    @foreach ($group->where('id_questiontypes', 3)->sortBy('no') as $question)
        <div class="mb-4 p-4 bg-white border rounded-lg shadow-sm">
            <div class="border rounded-lg p-4">

                <!-- Header Pertanyaan -->
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

                <!-- Pilihan Jawaban -->
                <div class="flex flex-wrap justify-center gap-4">
                    @foreach ([1,2,3,4,5,6,7,0] as $value)
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