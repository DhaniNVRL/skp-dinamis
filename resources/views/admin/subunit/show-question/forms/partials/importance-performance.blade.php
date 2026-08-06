<div class="grid grid-cols-1 gap-5 xl:grid-cols-2">
    {{-- KEPENTINGAN --}}
    <div class="rounded-xl border border-blue-200 bg-blue-50 p-5">
        <div class="mb-4 text-center">
            <h4 class="font-semibold text-blue-700">
                Kepentingan
            </h4>
        </div>

        <div class="flex justify-center">
            @include(
                'admin.subunit.show-question.forms.partials.scale',
                [
                    'question' => $question,
                    'maximum' => $scaleMaximum,
                    'includeZero' => true,
                    'name' => "importance_{$question->id}_{$scopeId}",
                    'leftLabel' => null,
                    'rightLabel' => null,
                    'zeroLabel' => null,
                ]
            )
        </div>
    </div>

    {{-- KINERJA --}}
    <div class="rounded-xl border border-emerald-200 bg-emerald-50 p-5">
        <div class="mb-4 text-center">
            <h4 class="font-semibold text-emerald-700">
                Kinerja
            </h4>
        </div>

        <div class="flex justify-center">
            @include(
                'admin.subunit.show-question.forms.partials.scale',
                [
                    'question' => $question,
                    'maximum' => $scaleMaximum,
                    'includeZero' => true,
                    'name' => "performance_{$question->id}_{$scopeId}",
                    'leftLabel' => null,
                    'rightLabel' => null,
                    'zeroLabel' => null,
                ]
            )
        </div>
    </div>
</div>