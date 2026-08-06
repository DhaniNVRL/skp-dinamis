@foreach ($questions->groupBy('no_header') as $noHeader => $group)

    {{-- HEADER TYPE --}}
    @foreach ($group->where('id_questiontypes', 1)->sortBy('no') as $question)
        <div class="mb-6 p-4 border rounded-lg shadow-sm bg-blue-100">
            <div class="flex items-center justify-between">

                <div class="flex-1 text-center font-bold text-lg text-gray-800">
                    {{ $question->name }}
                </div>

                <div class="flex gap-3">
                    <a href="{{ route('question.edit', $question->id) }}"
                       class="w-8 h-8 flex items-center justify-center rounded-full bg-yellow-100 text-yellow-600">
                        <i class="fa fa-edit"></i>
                    </a>
                </div>

            </div>
        </div>
    @endforeach


    {{-- TABLE TYPE --}}
    @php
        $tableQuestions = $group->where('id_questiontypes', 2)->sortBy('no');
    @endphp

    @if ($tableQuestions->count())
        <table class="table table-bordered w-full mb-6 border-collapse">
            <thead>
                <tr class="bg-gray-100">

                    <th class="text-left p-2 w-1/3">
                        Pertanyaan
                    </th>

                    @foreach ($competitors as $competitor)
                        <th class="text-center p-2">
                            <label class="font-semibold text-gray-700">
                                {{ $competitor->name }}
                            </label>
                        </th>
                    @endforeach

                </tr>
            </thead>

            <tbody>

                @foreach ($tableQuestions as $question)
                    <tr class="border-t">

                        {{-- QUESTION --}}
                        <td class="p-2 align-top">
                            <div class="flex justify-between items-start gap-2">
                                <label class="font-semibold text-gray-700">
                                    {{ $question->no_header }}{{ $question->no }}. {{ $question->name }}
                                </label>
                            </div>
                        </td>

                        {{-- ANSWERS --}}
                        @foreach ($competitors as $competitor)
                            <td class="text-center p-2 align-middle">
                                <div class="flex justify-center gap-1 flex-wrap">

                                    @for ($i = 1; $i <= 7; $i++)
                                        <label class="cursor-pointer">

                                            <input type="radio"
                                                name="answer[{{ $question->id }}][{{ $competitor->id }}]"
                                                value="{{ $i }}"
                                                class="peer hidden">

                                            <div class="w-8 h-8 flex items-center justify-center rounded-full
                                                        border border-gray-300 text-xs
                                                        peer-checked:bg-blue-600 peer-checked:text-white
                                                        peer-checked:border-blue-600
                                                        hover:bg-blue-100 transition">
                                                {{ $i }}
                                            </div>

                                        </label>
                                    @endfor

                                </div>
                            </td>
                        @endforeach

                    </tr>
                @endforeach

            </tbody>
        </table>
    @endif

@endforeach