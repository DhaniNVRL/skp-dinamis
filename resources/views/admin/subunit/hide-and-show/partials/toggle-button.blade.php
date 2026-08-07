<button
    type="button"
    data-hide-show-toggle
    data-form-id="{{ $formId }}"
    data-question-id="{{ $questionId }}"
    data-subunit-ids='@json(array_values($subunitIds))'
    data-scope-type="{{ $scopeType }}"
    data-target-names="{{ collect($targetNames)->filter()->implode(', ') }}"
    data-active="{{ $isActive ? '1' : '0' }}"
    aria-pressed="{{ $isActive ? 'true' : 'false' }}"
    class="relative inline-flex h-7 w-12 shrink-0 items-center rounded-full transition
        {{ $isActive ? 'bg-green-500' : 'bg-gray-300' }}"
>
    <span
        data-toggle-knob
        class="inline-block h-5 w-5 rounded-full bg-white shadow transition-transform
            {{ $isActive ? 'translate-x-6' : 'translate-x-1' }}"
    ></span>

    <span class="sr-only">
        Ubah status pertanyaan
    </span>
</button>
