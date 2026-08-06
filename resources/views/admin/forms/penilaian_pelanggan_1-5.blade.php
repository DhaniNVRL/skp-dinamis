@if($form->description)
    <div class="relative bg-white rounded-xl shadow border border-gray-200 p-6">

        <!-- tombol delete (kiri atas) -->
        <form action="{{ route('description.destroy', $form->description->id) }}"
              method="POST"
              onsubmit="return confirm('Hapus description ini?')"
              class="absolute top-3 right-3">
            @csrf
            @method('DELETE')

            <button
                type="submit"
                class="flex items-center justify-center w-8 h-8 rounded-full
                       bg-red-100 text-red-600 hover:bg-red-200 transition">
                <i class="fa fa-trash"></i>
            </button>
        </form>

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
                            @foreach ([1,2,3,4,5,0] as $value)
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
                            @foreach ([1,2,3,4,5,0] as $value)
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
                    @foreach ([1,2,3,4,5,0] as $value)
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

        @foreach ($group->where('id_questiontypes', 4)->sortBy('no') as $question)
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

                <!-- Text Area Jawaban -->
                <div>
                    <textarea
                        name="question_{{ $question->id }}"
                        rows="4"
                        class="w-full border rounded-lg p-3 text-gray-700
                            focus:ring-2 focus:ring-blue-500 focus:outline-none"
                        placeholder="Tulis jawaban Anda di sini..."
                    ></textarea>
                </div>

            </div>
        </div>
    @endforeach

@endforeach

<div class="mt-6">
    <button
        type="button"
        class="inline-flex items-center gap-2 bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg transition"
        @click="
            const template = document.getElementById('description');

            if (!template) {
                alert('Template description tidak ditemukan!');
                return;
            }

            $dispatch('open-modal-tab', {
                title: 'Add Description',
                manual: '{{ route('description.store') }}',
                group: '{{ $groups->id }}',
                form: '{{ $form->id }}',
                content: template.innerHTML
            });
        "
    >
        <i class="fa fa-plus"></i>
        Add Description
    </button>
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
document.addEventListener('DOMContentLoaded', function () {

    // =========================
    // GUARD (ANTI DOUBLE INIT)
    // =========================
    if (window.__question_ui_initialized) return;
    window.__question_ui_initialized = true;

    // =========================
    // TAB SWITCH
    // =========================
    document.addEventListener('click', function (e) {

        const tabBtn = e.target.closest('[data-tab]');
        if (!tabBtn) return;

        const tab = tabBtn.dataset.tab;

        document.querySelectorAll('[data-content]').forEach(el => {
            el.classList.add('hidden');
        });

        const target = document.querySelector(`[data-content="${tab}"]`);
        if (target) target.classList.remove('hidden');
    });

    // =========================
    // ADD ROW (SAFE)
    // =========================
    document.addEventListener('click', function (e) {

        const btn = e.target.closest('.add-row-btn');
        if (!btn) return;

        const section = btn.closest('[data-section="manual-form"]');
        if (!section) return;

        const container = section.querySelector('.manual-rows');
        const firstRow = container?.querySelector('.row-item');

        if (!firstRow) return;

        const clone = firstRow.cloneNode(true);

        // reset value
        clone.querySelectorAll('input, textarea, select').forEach(el => {
            el.value = '';
        });

        container.appendChild(clone);
    });

    // =========================
    // REMOVE ROW (FIX INI YANG KAMU BUTUH)
    // =========================
    document.addEventListener('click', function (e) {

        const btn = e.target.closest('.remove-row');
        if (!btn) return;

        const section = btn.closest('[data-section="manual-form"]');
        if (!section) return;

        const container = section.querySelector('.manual-rows');
        const rows = container.querySelectorAll('.row-item');

        // minimal 1 row
        if (rows.length <= 1) {
            alert('Minimal harus ada 1 row');
            return;
        }

        const row = btn.closest('.row-item');
        if (row) row.remove();

        document.addEventListener('click', function (e) {

            if (e.target.id === 'addRow') {

                const container = document.querySelector('#rows .question-wrapper');
                if (!container) return;

                const clone = container.cloneNode(true);

                clone.querySelectorAll('input').forEach(el => el.value = '');

                document.querySelector('#rows').appendChild(clone);
            }

        });
    });

    // =========================
    // RADIO + CHECKBOX CHILD
    // =========================
    document.addEventListener('change', function (e) {

        const block = e.target.closest('.question-block');
        if (!block) return;

        // =========================
        // RADIO
        // =========================
        if (e.target.classList.contains('option-radio')) {

            const all = block.querySelectorAll('.option-radio');

            all.forEach(r => {
                const item = r.closest('.option-item');
                item?.querySelector('.child-textarea')?.classList.add('hidden');
            });

            const current = e.target.closest('.option-item');
            const child = current?.querySelector('.child-textarea');

            if (e.target.checked && e.target.dataset.hasChild === "1") {
                child?.classList.remove('hidden');
            }
        }

        if (e.target.classList.contains('option-checkbox')) {

            const item = e.target.closest('.option-item');
            const child = item?.querySelector('.child-textarea');

            if (!child) return;

            if (e.target.checked && e.target.dataset.hasChild === "1") {
                child.classList.remove('hidden');
            } else {
                child.classList.add('hidden');
                const ta = child.querySelector('textarea');
                if (ta) ta.value = '';
            }
        }

         if (e.target.classList.contains('has-child-select')) {

            const item = e.target.closest('.question-item');
            if (!item) return;

            const container = item.querySelector('.child-input-container');
            if (!container) return;

            if (e.target.value === "1") {
                container.classList.remove('hidden');
            } else {
                container.classList.add('hidden');

                const input = container.querySelector('input');
                if (input) input.value = '';
            }
        }

    });
    
    function initHasChildSelect(context = document) {
        context.querySelectorAll('.has-child-select').forEach(select => {
            const wrapper = select.closest('.question-item');
            if (!wrapper) return;

            const container = wrapper.querySelector('.child-input-container');
            if (!container) return;

            if (select.value === "1") {
                container.classList.remove('hidden');
            } else {
                container.classList.add('hidden');
            }
        });
    }

    // =========================
    // MODAL EVENT
    // =========================
    document.addEventListener('open-modal-tab', function (e) {

        setTimeout(() => {
            const input = document.querySelector('#form_id_input');
            if (input && e.detail.form) {
                input.value = e.detail.form;
            }
        }, 100);

    });

    function toggleChild(select) {
        const wrapper = select.closest('.question-item');
        const container = wrapper.querySelector('.child-input-container');

        if (select.value === "1") {
            container.classList.remove('hidden');
        } else {
            container.classList.add('hidden');
        }
    }
});
</script>