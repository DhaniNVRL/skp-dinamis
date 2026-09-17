
@php
    $questionId = $question->id;
    $maximumScale = 7;
@endphp

<div
    x-data="{
        importance: '',
        performance: '',
        reason: '',

        get showReason() {
            return this.importance !== ''
                && this.performance !== ''
                && Number(this.importance) > 0
                && Number(this.performance) > 0
                && Number(this.performance) < Number(this.importance);
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
            for="performance-below-reason-{{ $questionId }}"
            class="block text-sm font-semibold text-gray-700"
        >
            Alasan Kinerja di Bawah Kepentingan
        </label>

        <textarea
            id="performance-below-reason-{{ $questionId }}"
            name="answers[{{ $questionId }}][reason]"
            x-model="reason"
            :disabled="!showReason"
            rows="4"
            placeholder="Jelaskan mengapa Kinerja lebih rendah daripada Kepentingan..."
            class="w-full rounded-lg border border-gray-300 px-3 py-2"
        ></textarea>
    </div>
</div>
