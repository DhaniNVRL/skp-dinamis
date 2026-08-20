@php
    $generalQuestions = $forms->where('formtype_id', 1)->flatMap(fn ($form) =>
        $form->questions->map(fn ($question) => ['question' => $question, 'form' => $form])
    );
    $allQuestions = $forms->flatMap(fn ($form) =>
        $form->questions->map(fn ($question) => ['question' => $question, 'form' => $form])
    );
    $branchOptionsByQuestion = $generalQuestions->mapWithKeys(fn ($item) => [
        (string) $item['question']->id => $item['question']->options->map(fn ($option) => [
            'id' => (int) $option->id,
            'text' => $option->answer_text,
        ])->values()->all(),
    ])->all();
@endphp

<details id="branchingConfiguration" class="mb-6 overflow-hidden rounded-xl border border-indigo-200 bg-white shadow-sm">
    <summary class="flex cursor-pointer list-none items-center justify-between gap-4 bg-indigo-50 px-5 py-4">
        <div class="flex items-center gap-3">
            <span class="inline-flex h-10 w-10 items-center justify-center rounded-lg bg-indigo-100 text-indigo-600"><i class="fa-solid fa-code-branch"></i></span>
            <div>
                <h3 class="font-semibold text-gray-900">Konfigurasi Percabangan Pertanyaan</h3>
                <p class="text-sm text-gray-600">Tambahkan beberapa rule untuk melewati pertanyaan dan form.</p>
            </div>
        </div>
        <i class="fa-solid fa-chevron-down text-indigo-600"></i>
    </summary>

    <div class="space-y-6 p-5">
        <form id="branchConfigurationForm" method="POST" action="{{ route('admin.branch-rules.store', $groups) }}" data-store-url="{{ route('admin.branch-rules.store', $groups) }}" class="space-y-5">
            @csrf
            <div class="grid gap-4 lg:grid-cols-2">
                <div>
                    <label class="mb-2 block text-sm font-semibold text-gray-700">Pertanyaan pemicu</label>
                    <select id="branchParentQuestion" name="parent_question_id" required class="w-full rounded-lg border border-blue-300 bg-white px-3 py-2.5">
                        <option value="">Pilih pertanyaan</option>
                        @foreach ($generalQuestions as $item)
                            <option value="{{ $item['question']->id }}" data-form-id="{{ $item['form']->id }}">{{ trim(($item['question']->no_header ?? '').($item['question']->no ?? '')) }} &mdash; {{ $item['question']->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="mb-2 block text-sm font-semibold text-gray-700">Opsi pemicu</label>
                    <select id="branchAffirmativeOption" name="affirmative_option_id" required class="w-full rounded-lg border border-blue-300 bg-white px-3 py-2.5 disabled:bg-gray-100">
                        <option value="">Pilih opsi pemicu</option>
                        @foreach ($generalQuestions as $item)
                            @php
                                $parentQuestion = $item['question'];
                            @endphp
                            @if ($parentQuestion->options->isNotEmpty())
                                <optgroup label="{{ trim(($parentQuestion->no_header ?? '').($parentQuestion->no ?? '')) }} — {{ $parentQuestion->name }}" data-question-id="{{ $parentQuestion->id }}">
                                    @foreach ($parentQuestion->options as $parentOption)
                                        <option value="{{ $parentOption->id }}" data-question-id="{{ $parentQuestion->id }}">
                                            {{ $parentOption->answer_text ?? $parentOption->name ?? ('Opsi #'.$parentOption->id) }}
                                        </option>
                                    @endforeach
                                </optgroup>
                            @endif
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="grid gap-4 lg:grid-cols-2">
                <div>
                    <label class="mb-2 block text-sm font-semibold text-gray-700">Lewati pertanyaan saat opsi dipilih</label>
                    <div class="mb-2 flex gap-2">
                        <button type="button" data-check-action="all" data-check-target="branchSkippedQuestions" class="rounded border border-blue-200 px-2 py-1 text-xs font-semibold text-blue-600 hover:bg-blue-50">Pilih Semua</button>
                        <button type="button" data-check-action="none" data-check-target="branchSkippedQuestions" class="rounded border border-gray-200 px-2 py-1 text-xs font-semibold text-gray-600 hover:bg-gray-50">Hapus Pilihan</button>
                    </div>
                    <div id="branchSkippedQuestions" class="max-h-52 overflow-y-auto rounded-lg border border-blue-300 bg-white p-2">
                        <p data-empty-message hidden style="display:none" class="px-2 py-3 text-center text-sm text-gray-400">Pilih pertanyaan pemicu dahulu.</p>
                        @foreach ($allQuestions as $item)
                            <label data-question-choice data-form-id="{{ $item['form']->id }}" data-question-id="{{ $item['question']->id }}" class="flex cursor-pointer items-start gap-2 rounded px-2 py-2 text-sm hover:bg-gray-50">
                                <input type="checkbox" name="skipped_question_ids[]" value="{{ $item['question']->id }}" class="mt-0.5 rounded border-gray-300 text-red-600 focus:ring-red-500">
                                <span>[{{ $item['form']->name }}] <strong>{{ trim(($item['question']->no_header ?? '').($item['question']->no ?? '')) }}</strong> &mdash; {{ $item['question']->name }}</span>
                            </label>
                        @endforeach
                    </div>
                </div>

                <div>
                    <label class="mb-2 block text-sm font-semibold text-gray-700">Lewati satu atau beberapa form saat opsi dipilih</label>
                    <div class="mb-2 flex gap-2">
                        <button type="button" data-check-action="all" data-check-target="branchSkippedForms" class="rounded border border-blue-200 px-2 py-1 text-xs font-semibold text-blue-600 hover:bg-blue-50">Pilih Semua</button>
                        <button type="button" data-check-action="none" data-check-target="branchSkippedForms" class="rounded border border-gray-200 px-2 py-1 text-xs font-semibold text-gray-600 hover:bg-gray-50">Hapus Pilihan</button>
                    </div>
                    <div id="branchSkippedForms" class="max-h-52 overflow-y-auto rounded-lg border border-blue-300 bg-white p-2">
                        @foreach ($forms as $form)
                            <label data-form-choice data-form-id="{{ $form->id }}" class="flex cursor-pointer items-start gap-2 rounded px-2 py-2 text-sm hover:bg-gray-50">
                                <input type="checkbox" name="skip_form_ids[]" value="{{ $form->id }}" class="mt-0.5 rounded border-gray-300 text-amber-600 focus:ring-amber-500">
                                <span><strong>{{ $form->no_urut }}</strong> &mdash; {{ $form->name }}</span>
                            </label>
                        @endforeach
                    </div>
                </div>
            </div>

            <div class="rounded-lg border border-blue-200 bg-blue-50 p-3 text-sm text-blue-700">Centang pertanyaan atau form yang akan dilewati. Tombol Pilih Semua hanya memilih data yang sedang ditampilkan.</div>
            <div class="flex justify-end gap-2">
                <button id="cancelBranchEdit" type="button" hidden class="inline-flex items-center gap-2 rounded-lg border border-gray-300 bg-white px-4 py-2.5 font-semibold text-gray-700 hover:bg-gray-50"><i class="fa-solid fa-xmark"></i> Batal Edit</button>
                <button id="branchSubmitButton" type="submit" class="inline-flex items-center gap-2 rounded-lg bg-indigo-600 px-4 py-2.5 font-semibold text-white hover:bg-indigo-700"><i class="fa-solid fa-floppy-disk"></i> <span>Simpan Konfigurasi</span></button>
            </div>
        </form>

        <div class="border-t border-gray-200 pt-5">
            <h4 class="mb-3 font-semibold text-gray-900">Konfigurasi Aktif</h4>
            <div class="space-y-3">
                @forelse ($branchRules as $rule)
                    @php
                        $ruleEditPayload = [
                            'parent_id' => (int) $rule->parent_question_id,
                            'option_id' => (int) $rule->affirmative_option_id,
                            'skipped' => $rule->skippedQuestions->pluck('id')->map(fn ($id) => (int) $id)->values()->all(),
                            'forms' => $rule->skippedForms->pluck('id')->map(fn ($id) => (int) $id)->values()->all(),
                        ];
                    @endphp
                    <div class="flex flex-col justify-between gap-3 rounded-lg border border-gray-200 p-4 lg:flex-row lg:items-center">
                        <div class="text-sm text-gray-700">
                            <p><span class="mr-2 rounded bg-indigo-100 px-2 py-1 text-xs font-semibold text-indigo-700">Rule #{{ $rule->id }}</span><span class="font-semibold">Pemicu:</span> {{ $rule->parentQuestion?->name }}</p>
                            <p><span class="font-semibold">Opsi:</span> {{ $rule->affirmativeOption?->answer_text }}</p>
                            <p><span class="font-semibold">Lewati pertanyaan:</span> {{ $rule->skippedQuestions->map(fn ($q) => trim(($q->no_header ?? '').($q->no ?? '')))->implode(', ') ?: 'Tidak ada' }}</p>
                            <p><span class="font-semibold">Lewati form:</span> {{ $rule->skippedForms->pluck('name')->implode(', ') ?: ($rule->skipForm?->name ?? 'Tidak ada') }}</p>
                        </div>
                        <div class="flex gap-2">
                            <button type="button" class="branch-edit-button inline-flex items-center gap-2 rounded-lg border border-amber-200 bg-amber-50 px-3 py-2 text-sm font-semibold text-amber-600 hover:bg-amber-100" data-update-url="{{ route('admin.branch-rules.update', [$groups, $rule]) }}" data-rule='@json($ruleEditPayload)'><i class="fa-solid fa-pen"></i> Edit</button>
                            <form method="POST" action="{{ route('admin.branch-rules.destroy', [$groups, $rule]) }}" onsubmit="return confirm('Hapus konfigurasi percabangan ini?')">
                            @csrf @method('DELETE')
                            <button class="inline-flex items-center gap-2 rounded-lg border border-red-200 bg-red-50 px-3 py-2 text-sm font-semibold text-red-600 hover:bg-red-100"><i class="fa-solid fa-trash"></i> Hapus</button>
                        </form>
                        </div>
                    </div>
                @empty
                    <p class="rounded-lg border border-dashed border-gray-300 p-4 text-center text-sm text-gray-500">Belum ada konfigurasi percabangan tersimpan.</p>
                @endforelse
            </div>
        </div>
    </div>
</details>

<script>
(function initializeBranchConfiguration() {
    const optionsByQuestion = @json($branchOptionsByQuestion);
    const parent = document.getElementById('branchParentQuestion');
    const option = document.getElementById('branchAffirmativeOption');
    const configurationForm = document.getElementById('branchConfigurationForm');
    const cancelEdit = document.getElementById('cancelBranchEdit');
    const submitButton = document.getElementById('branchSubmitButton');
    const questionContainers = ['branchSkippedQuestions']
        .map(id => document.getElementById(id));
    const formContainer = document.getElementById('branchSkippedForms');
    if (!parent || !option || !configurationForm || !cancelEdit || !submitButton || questionContainers.some(item => !item) || !formContainer) return;

    cancelEdit.style.display = 'none';

    const syncChoices = function () {
        const availableOptions = optionsByQuestion[String(parent.value)] || [];
        const previousOptionValue = option.value;
        option.replaceChildren();
        const placeholder = document.createElement('option');
        placeholder.value = '';
        placeholder.textContent = availableOptions.length > 0
            ? 'Pilih opsi pemicu'
            : (parent.value ? 'Pertanyaan belum mempunyai opsi' : 'Pilih pertanyaan terlebih dahulu');
        option.appendChild(placeholder);
        availableOptions.forEach(function (item) {
            const choice = document.createElement('option');
            choice.value = String(item.id);
            choice.textContent = item.text;
            option.appendChild(choice);
        });
        option.disabled = availableOptions.length === 0;
        if (availableOptions.some(item => String(item.id) === String(previousOptionValue))) {
            option.value = String(previousOptionValue);
        }

        const selectedParent = parent.options[parent.selectedIndex];
        const formId = selectedParent ? selectedParent.dataset.formId : '';
        questionContainers.forEach(function (container) {
            let visibleCount = 0;
            container.querySelectorAll('[data-question-choice]').forEach(function (row) {
                const visible = formId !== '' && row.dataset.formId === formId && row.dataset.questionId !== parent.value;
                row.hidden = !visible;
                row.classList.toggle('hidden', !visible);
                row.style.display = visible ? 'flex' : 'none';
                const checkbox = row.querySelector('input[type="checkbox"]');
                checkbox.disabled = !visible;
                if (!visible) checkbox.checked = false;
                if (visible) visibleCount++;
            });
            const emptyMessage = container.querySelector('[data-empty-message]');
            emptyMessage.hidden = visibleCount > 0;
            emptyMessage.classList.toggle('hidden', visibleCount > 0);
            emptyMessage.style.display = visibleCount > 0 ? 'none' : 'block';
        });

        formContainer.querySelectorAll('[data-form-choice]').forEach(function (row) {
            const isCurrentForm = formId !== '' && row.dataset.formId === formId;
            row.hidden = isCurrentForm;
            row.classList.toggle('hidden', isCurrentForm);
            row.style.display = isCurrentForm ? 'none' : 'flex';
            const checkbox = row.querySelector('input[type="checkbox"]');
            checkbox.disabled = isCurrentForm;
            if (isCurrentForm) checkbox.checked = false;
        });
    };

    const clearAllChecks = function () {
        configurationForm.querySelectorAll('input[type="checkbox"]').forEach(function (checkbox) {
            checkbox.checked = false;
        });
    };
    const checkValues = function (container, values) {
        const selected = (values || []).map(String);
        container.querySelectorAll('input[type="checkbox"]:not(:disabled)').forEach(function (checkbox) {
            checkbox.checked = selected.includes(String(checkbox.value));
        });
    };
    const leaveEditMode = function () {
        configurationForm.action = configurationForm.dataset.storeUrl;
        configurationForm.querySelector('input[name="_method"]')?.remove();
        configurationForm.reset();
        clearAllChecks();
        submitButton.querySelector('span').textContent = 'Simpan Konfigurasi';
        cancelEdit.hidden = true;
        cancelEdit.style.display = 'none';
        syncChoices();
    };

    document.querySelectorAll('.branch-edit-button').forEach(function (button) {
        button.addEventListener('click', function () {
            let rule;
            try {
                rule = JSON.parse(button.dataset.rule || '{}');
            } catch (error) {
                return;
            }

            clearAllChecks();
            parent.value = String(rule.parent_id || '');
            syncChoices();
            option.value = String(rule.option_id || '');
            checkValues(questionContainers[0], rule.skipped);
            checkValues(formContainer, rule.forms);

            configurationForm.action = button.dataset.updateUrl;
            let method = configurationForm.querySelector('input[name="_method"]');
            if (!method) {
                method = document.createElement('input');
                method.type = 'hidden';
                method.name = '_method';
                configurationForm.appendChild(method);
            }
            method.value = 'PUT';
            submitButton.querySelector('span').textContent = 'Simpan Perubahan';
            cancelEdit.hidden = false;
            cancelEdit.style.display = 'inline-flex';
            document.getElementById('branchingConfiguration').open = true;
            configurationForm.scrollIntoView({ behavior: 'smooth', block: 'start' });
        });
    });

    cancelEdit.addEventListener('click', leaveEditMode);
    document.querySelectorAll('[data-check-action]').forEach(function (button) {
        button.addEventListener('click', function () {
            const container = document.getElementById(button.dataset.checkTarget);
            if (!container) return;
            const checked = button.dataset.checkAction === 'all';
            container.querySelectorAll('input[type="checkbox"]:not(:disabled)').forEach(function (checkbox) {
                if (!checkbox.closest('[hidden]')) checkbox.checked = checked;
            });
        });
    });

    parent.addEventListener('change', syncChoices);
    parent.addEventListener('input', syncChoices);
    window.addEventListener('pageshow', syncChoices);
    syncChoices();
    window.setTimeout(syncChoices, 50);
    window.setTimeout(syncChoices, 300);
})();
</script>
