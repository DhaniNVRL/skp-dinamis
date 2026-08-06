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

@foreach ($questions->sortBy('no') as $question)
<div class="mb-6 p-4 border rounded-lg shadow-sm bg-white">

    {{-- HEADER --}}
    <div class="flex justify-between items-start gap-4">
        <label class="font-semibold text-gray-800">
            {{ $question->no_header }}{{ $question->no }}. {{ $question->name }}
        </label>

        <div class="flex items-center gap-3">
            <a href="{{ route('question.edit', $question->id) }}"
               class="text-yellow-500 hover:text-yellow-600">
                <i class="fa fa-edit"></i>
            </a>

            <form action="{{ route('question.destroy', $question->id) }}"
                  method="POST"
                  onsubmit="return confirm('Hapus pertanyaan ini?')">
                @csrf
                @method('DELETE')

                <button type="submit" class="text-red-500 hover:text-red-600">
                    <i class="fa fa-trash"></i>
                </button>
            </form>
        </div>
    </div>

    {{-- BODY --}}
    <div class="mt-4">

        {{-- TYPE 3 (RADIO) --}}
        @if ($question->id_questiontypes == 3)

        <div class="question-block space-y-2">

            @foreach ($question->options as $opsion)
                <div class="option-item flex flex-col p-3 border rounded-lg hover:bg-blue-50 transition">

                    {{-- TOP ROW (RADIO + ACTION) --}}
                    <div class="flex items-center justify-between">

                        <label class="flex items-center gap-3 cursor-pointer w-full">
                            <input type="radio"
                                name="answer_{{ $question->id }}"
                                value="{{ $opsion->id }}"
                                data-has-child="{{ $opsion->has_child }}"
                                class="option-radio">

                            <span class="text-gray-700">
                                {{ $opsion->answer_text }}
                            </span>
                        </label>

                        {{-- ACTION BUTTONS --}}
                        <div class="flex gap-2 ml-4">

                            <a href="{{ route('options.edit', $opsion->id) }}"
                            class="text-yellow-500 hover:text-yellow-600 transition"
                            title="Edit">
                                <i class="fa fa-edit"></i>
                            </a>

                            <form action="{{ route('options.destroy', $opsion->id) }}"
                                method="POST"
                                onsubmit="return confirm('Yakin ingin menghapus?')">
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

                    {{-- CHILD --}}
                    <div class="child-textarea mt-2 hidden w-full answer-text2-wrapper">

                        @if(!empty($opsion->answer_text2))
                            <span class="text-red-600 block mb-2">
                                {{ $opsion->answer_text2 }}
                            </span>
                        @endif

                        <textarea
                            name="child_answer_{{ $question->id }}[{{ $opsion->id }}]"
                            class="border rounded p-2 w-full"
                            placeholder="Tulis jawaban tambahan...">
                        </textarea>

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

        {{-- TYPE 4 (CHECKBOX) --}}
        @elseif ($question->id_questiontypes == 4)

        <div class="question-block space-y-2">

            @foreach ($question->options as $opsion)
                <div class="option-item flex flex-col p-3 border rounded-lg hover:bg-blue-50 transition">

                    {{-- TOP ROW (CHECKBOX + ACTION) --}}
                    <div class="flex items-center justify-between">

                        <label class="flex items-center gap-3 cursor-pointer w-full">
                            <input type="checkbox"
                                name="answer_{{ $question->id }}[]"
                                value="{{ $opsion->id }}"
                                data-has-child="{{ $opsion->has_child }}"
                                class="option-checkbox">

                            <span class="text-gray-700">
                                {{ $opsion->answer_text }}
                            </span>
                        </label>

                        {{-- ACTION BUTTONS --}}
                        <div class="flex gap-2 ml-4">

                            <a href="{{ route('options.edit', $opsion->id) }}"
                            class="text-yellow-500 hover:text-yellow-600 transition"
                            title="Edit">
                                <i class="fa fa-edit"></i>
                            </a>

                            <form action="{{ route('options.destroy', $opsion->id) }}"
                                method="POST"
                                onsubmit="return confirm('Yakin ingin menghapus?')">
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

                    {{-- CHILD --}}
                    <div class="child-textarea mt-2 hidden w-full answer-text2-wrapper">

                        @if(!empty($opsion->answer_text2))
                            <span class="text-red-600 block mb-2">
                                {{ $opsion->answer_text2 }}
                            </span>
                        @endif

                        <textarea
                            name="child_answer_{{ $question->id }}[{{ $opsion->id }}]"
                            class="border rounded p-2 w-full"
                            placeholder="Tulis jawaban tambahan...">
                        </textarea>

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

        {{-- TYPE LAIN (TETAP PUNYAMU) --}}
        @elseif ($question->id_questiontypes == 1)
            <input type="text" name="answers[{{ $question->id }}]"
                   class="border p-2 w-full rounded">

        @elseif ($question->id_questiontypes == 2)
            <textarea name="answers[{{ $question->id }}]"
                      class="border p-2 w-full rounded"></textarea>

        @elseif ($question->id_questiontypes == 6)
            <input type="text" class="border p-2 w-full rounded validate-number">

        @elseif ($question->id_questiontypes == 7)
            <input type="date" class="border p-2 w-full rounded">

        @elseif ($question->id_questiontypes == 8)
            <input type="email" class="border p-2 w-full rounded validate-email">

        @elseif ($question->id_questiontypes == 9)
            <input type="text" class="border p-2 w-full rounded validate-number">

        @else
            <p class="text-red-500">Type belum dibuat</p>
        @endif

    </div>
</div>
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