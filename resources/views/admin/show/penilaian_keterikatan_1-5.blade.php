@if($form->description)
    <div class="relative bg-white rounded-xl shadow border border-gray-200 p-6">


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

    @php
        $questionType1 = $group->where('id_questiontypes', 1)->sortBy('no');
    @endphp

    @if($questionType1->isNotEmpty())
        <div class="mb-6 p-4 rounded-xl bg-blue-100 border shadow-sm">
            <h4 class="text-center font-bold text-lg text-gray-800">
                {{ $questionType1->first()->name }}
            </h4>
        </div>
    @endif

    {{-- PERTANYAAN --}}
    @php
        $subunitList = $subunitIds->toArray();

        $type3Questions = $group->where('id_questiontypes', 2)
            ->sortBy('no')
            ->filter(function ($question) use ($form, $activeMapSubUnit, $subunitList) {

                $key = $form->id.'-'.$question->id;

                return isset($activeMapSubUnit[$key]) &&
                    count(array_intersect($activeMapSubUnit[$key], $subunitList)) > 0;
            });
    @endphp

    @if($type3Questions->isNotEmpty())
        @foreach ($type3Questions as $question)
            <div class="mb-4 bg-white rounded-xl shadow-sm p-5">

                {{-- Card dalam (border untuk setiap pertanyaan) --}}
                <div class="border rounded-lg p-4">

                    <div class="mb-3 font-semibold text-gray-700">
                        {{ $question->no_header }}{{ $question->no }}. {{ $question->name }}
                    </div>

                    <div class="flex flex-wrap justify-center gap-3">
                        @foreach ([1,2,3,4,5] as $value)
                            <label class="flex items-center gap-1 text-sm">
                                <input
                                    type="radio"
                                    name="indicator_{{ $question->id }}"
                                    x-model="indicator_{{ $question->id }}"
                                    value="{{ $value }}"
                                >
                                {{ $value }}
                            </label>
                        @endforeach
                    </div>

                </div>
            </div>
        @endforeach
    @endif
@endforeach