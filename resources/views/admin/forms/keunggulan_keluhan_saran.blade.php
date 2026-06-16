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

        <div class="grid grid-cols-1 md:grid-cols-3 gap-5">

        <!-- Keunggulan -->
            <div class="bg-green-50 border border-green-100 rounded-lg p-4">
                <label class="block text-sm font-semibold text-green-700 mb-2">
                    Keunggulan
                </label>
                <textarea
                    rows="6"
                    class="w-full rounded-lg border border-gray-300 focus:border-green-500 focus:ring-2 focus:ring-green-200 resize-none p-3 text-sm"
                    placeholder="Tuliskan keunggulan, pencapaian, atau aspek positif yang sudah berjalan dengan baik dan perlu dipertahankan..."></textarea>
            </div>
        
            <!-- Keluhan -->
            <div class="bg-red-50 border border-red-100 rounded-lg p-4">
                <label class="block text-sm font-semibold text-red-700 mb-2">
                    Keluhan
                </label>
                <textarea
                    rows="6"
                    class="w-full rounded-lg border border-gray-300 focus:border-red-500 focus:ring-2 focus:ring-red-200 resize-none p-3 text-sm"
                    placeholder="Tuliskan kendala, hambatan, atau hal yang perlu mendapat perhatian..."></textarea>
            </div>

            <!-- Saran -->
            <div class="bg-blue-50 border border-blue-100 rounded-lg p-4">
                <label class="block text-sm font-semibold text-blue-700 mb-2">
                    Saran
                </label>
                <textarea
                    rows="6"
                    class="w-full rounded-lg border border-gray-300 focus:border-blue-500 focus:ring-2 focus:ring-blue-200 resize-none p-3 text-sm"
                    placeholder="Berikan ide, masukan, atau rekomendasi untuk peningkatan kualitas layanan..."></textarea>
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