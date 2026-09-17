
@php
    $questionId = $question->id;
    $maximumScale = 5;
@endphp

<div
    x-data="{
        importance: '',
        performance: '',
        reason: '',

        get showReason() {
            return this.performance !== ''
                && Number(this.performance) > 0;
        }
    }"
    class="space-y-5"
>
    <div class="space-y-2">
        <label
            for="importance-{{ $questionId }}"
            class="block text-sm font-semibold text-gray-700"
        >
            Kepentingan
        </label>

        <select
            id="importance-{{ $questionId }}"
            name="answers[{{ $questionId }}][importance]"
            x-model="importance"
            class="w-full rounded-lg border border-gray-300 px-3 py-2"
        >
            <option value="">Pilih nilai Kepentingan</option>

            @for ($value = 1; $value <= $maximumScale; $value++)
                <option value="{{ $value }}">
                    {{ $value }}
                </option>
            @endfor

            <option value="0">0 — Tidak dinilai</option>
        </select>
    </div>

    <div class="space-y-2">
        <label
            for="performance-{{ $questionId }}"
            class="block text-sm font-semibold text-gray-700"
        >
            Kinerja
        </label>

        <select
            id="performance-{{ $questionId }}"
            name="answers[{{ $questionId }}][performance]"
            x-model="performance"
            class="w-full rounded-lg border border-gray-300 px-3 py-2"
        >
            <option value="">Pilih nilai Kinerja</option>

            @for ($value = 1; $value <= $maximumScale; $value++)
                <option value="{{ $value }}">
                    {{ $value }}
                </option>
            @endfor

            <option value="0">0 — Tidak dinilai</option>
        </select>
    </div>

    <div
        x-show="showReason"
        x-cloak
        class="space-y-2"
    >
        <label
            for="performance-reason-{{ $questionId }}"
            class="block text-sm font-semibold text-gray-700"
        >
            Alasan Kinerja
        </label>

        <textarea
            id="performance-reason-{{ $questionId }}"
            name="answers[{{ $questionId }}][reason]"
            x-model="reason"
            :disabled="!showReason"
            rows="4"
            placeholder="Tuliskan alasan penilaian Kinerja..."
            class="w-full rounded-lg border border-gray-300 px-3 py-2"
        ></textarea>
    </div>
</div>
