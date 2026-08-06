{{-- DESCRIPTION --}}
@if($form->description)
    <div class="bg-white rounded-xl shadow border border-gray-200 p-6 mb-6">
        {!! $form->description->content ?? '<div class="text-gray-400 italic">Belum ada description.</div>' !!}
    </div>
@endif


@foreach ($questions->groupBy('no_header') as $noHeader => $group)

    @php
        $subunitList = $subunitIds->toArray();

        $questionType1 = $group->where('id_questiontypes', 1)
            ->sortBy('no')
            ->filter(function ($question) use ($form, $activeMapSubUnit, $subunitList) {

                $key = $form->id.'-'.$question->id;

                return isset($activeMapSubUnit[$key]) &&
                    count(array_intersect($activeMapSubUnit[$key], $subunitList)) > 0;
            });
    @endphp

    @if($questionType1->isNotEmpty())
        <div class="mb-6 p-4 rounded-xl bg-blue-100 border shadow-sm">
            <h4 class="text-center font-bold text-lg text-gray-800">
                {{ $questionType1->first()->name }}
            </h4>
        </div>
    @endif


    {{-- ================= PERTANYAAN ================= --}}
    @foreach($group->where('id_questiontypes', 2)->sortBy('no') as $question)

        @php
            $key = $form->id . '-' . $question->id;
            $subQuestions = $activeMapSubUnit[$key] ?? [];
        @endphp

        {{-- Jika tidak memiliki subunit, lewati --}}
        @continue(empty($subQuestions))

        <div class="mb-6 bg-white border rounded-xl shadow-sm p-5">

            <div class="flex items-start justify-between gap-4 mb-4">
                <div class="font-semibold text-gray-800">
                    {{ $question->no_header }}{{ $question->no }}. {{ $question->name }}
                </div>
            </div>

            @foreach($subQuestions as $subunitId)

                @php
                    $subunit = $subunits->firstWhere('id', $subunitId);
                @endphp

                @continue(!$subunit)

                <div class="border rounded-lg p-4 mb-5" x-data="{ kinerja: '' }">

                    <div class="text-lg font-bold text-center text-blue-700 mb-4">
                        {{ $subunit->name }}
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                        {{-- Kepentingan --}}
                        <div class="rounded-lg p-4">
                            <div class="text-center font-semibold mb-3">
                                Kepentingan
                            </div>

                            <div class="flex flex-wrap justify-center gap-3">
                                @foreach([1,2,3,4,5,0] as $value)
                                    <label class="flex items-center gap-1 text-sm">
                                        <input
                                            type="radio"
                                            name="kepentingan[{{ $question->id }}][{{ $subunitId }}]"
                                            value="{{ $value }}"
                                            class="text-blue-600 focus:ring-blue-500">
                                        <span>{{ $value }}</span>
                                    </label>
                                @endforeach
                            </div>
                        </div>

                        {{-- Kinerja --}}
                        <div class="rounded-lg p-4">
                            <div class="text-center font-semibold mb-3">
                                Kinerja
                            </div>

                            <div class="flex flex-wrap justify-center gap-3">
                                @foreach([1,2,3,4,5,0] as $value)
                                    <label class="flex items-center gap-1 text-sm">
                                        <input
                                            type="radio"
                                            name="kinerja[{{ $question->id }}][{{ $subunitId }}]"
                                            value="{{ $value }}"
                                            x-model="kinerja"
                                            class="text-blue-600 focus:ring-blue-500">
                                        <span>{{ $value }}</span>
                                    </label>
                                @endforeach
                            </div>
                        </div>

                    </div>

                    <div
                        x-show="kinerja != '' && kinerja <= 4 && kinerja != 0"
                        x-transition
                        class="mt-5">

                        <label class="block font-semibold text-red-600 mb-2">
                            Alasan penilaian kinerja (≤ 4 wajib diisi)
                        </label>

                        <textarea
                            name="alasan[{{ $question->id }}][{{ $subunitId }}]"
                            rows="4"
                            class="w-full border rounded-lg p-3 focus:ring-2 focus:ring-blue-500"
                            placeholder="Tuliskan alasan Anda..."></textarea>

                    </div>

                </div>

            @endforeach

        </div>

    @endforeach


    {{-- ================= INDIKATOR (RADIO) ================= --}}
    @php
        $subunitList = $subunitIds->toArray();

        $type3Questions = $group->where('id_questiontypes', 3)
            ->sortBy('no')
            ->filter(function ($question) use ($form, $activeMapSubUnit, $subunitList) {

                $key = $form->id.'-'.$question->id;

                return isset($activeMapSubUnit[$key]) &&
                    count(array_intersect($activeMapSubUnit[$key], $subunitList)) > 0;
            });
    @endphp

    @if($type3Questions->isNotEmpty())
        <div class="mb-4 bg-white border rounded-xl shadow-sm p-5">

            @foreach ($type3Questions as $question)

                <div class="mb-3 font-semibold text-gray-700">
                    {{ $question->no_header }}. {{ $question->name }}
                </div>

                <div class="flex flex-wrap justify-center gap-3">

                    @foreach ([1,2,3,4,5,0] as $value)
                        <label class="flex items-center gap-1 text-sm">

                            <input type="radio"
                                name="indicator_{{ $question->id }}"
                                x-model="indicator_{{ $question->id }}"
                                value="{{ $value }}">

                            {{ $value }}

                        </label>
                    @endforeach

                </div>

            @endforeach

        </div>
    @endif


    {{-- ================= ESSAY ================= --}}
    @php
        $subunitList = $subunitIds->toArray();

        $type4Questions = $group->where('id_questiontypes', 4)
            ->sortBy('no')
            ->filter(function ($question) use ($form, $activeMapSubUnit, $subunitList) {

                $key = $form->id.'-'.$question->id;

                return isset($activeMapSubUnit[$key]) &&
                    count(array_intersect($activeMapSubUnit[$key], $subunitList)) > 0;
            });
    @endphp

    @if($type4Questions->isNotEmpty())
        <div class="mb-4 bg-white border rounded-xl shadow-sm p-5">

            @foreach ($type4Questions as $question)

                <div class="mb-3 font-semibold text-gray-700">
                    {{ $question->no_header }}. {{ $question->name }}
                </div>

                <textarea name="question_{{ $question->id }}"
                        rows="4"
                        class="w-full border rounded-lg p-3 focus:ring-2 focus:ring-blue-500"
                        placeholder="Tulis jawaban Anda di sini..."></textarea>

            @endforeach

        </div>
    @endif
@endforeach