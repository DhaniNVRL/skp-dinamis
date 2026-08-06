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

                    <form action="{{ route('question.destroy', $question->id) }}"
                          method="POST"
                          onsubmit="return confirm('Hapus pertanyaan ini?')">
                        @csrf
                        @method('DELETE')

                        <button class="w-8 h-8 flex items-center justify-center rounded-full bg-red-100 text-red-600">
                            <i class="fa fa-trash"></i>
                        </button>
                    </form>
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
                            

                            <form action="{{ route('competitor.destroy', $competitor->id) }}"
                                    method="POST"
                                    onsubmit="return confirm('Hapus pertanyaan ini?')">
                                @csrf
                                @method('DELETE')

                                <button class="w-7 h-7 flex items-center justify-center rounded-full bg-red-100 text-red-600">
                                    <i class="fa fa-trash"></i>
                                </button>
                            </form>
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

                                <form action="{{ route('question.destroy', $question->id) }}"
                                      method="POST"
                                      onsubmit="return confirm('Hapus pertanyaan ini?')">
                                    @csrf
                                    @method('DELETE')

                                    <button class="w-7 h-7 flex items-center justify-center rounded-full bg-red-100 text-red-600">
                                        <i class="fa fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>

                        {{-- ANSWERS --}}
                        @foreach ($competitors as $competitor)
                            <td class="text-center p-2 align-middle">
                                <div class="flex justify-center gap-1 flex-wrap">

                                    @foreach ([1,2,3,4,5,6,7,0] as $value)
                                        <label class="cursor-pointer">

                                            <input type="radio"
                                                name="answer[{{ $question->id }}][{{ $competitor->id }}]"
                                                value="{{ $value }}"
                                                class="peer hidden">

                                            <div class="w-8 h-8 flex items-center justify-center rounded-full
                                                        border border-gray-300 text-xs
                                                        peer-checked:bg-blue-600 peer-checked:text-white
                                                        peer-checked:border-blue-600
                                                        hover:bg-blue-100 transition">
                                                {{ $value }}
                                            </div>
                                        </label>
                                    @endforeach

                                </div>
                            </td>
                        @endforeach

                    </tr>
                @endforeach

            </tbody>
        </table>
    @endif

@endforeach


{{-- ACTION BUTTONS --}}
<div class="mt-6 space-x-2">
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
    <button type="button"
            class="bg-green-600 text-white px-4 py-2 rounded mt-2"
            @click="
                const template = document.getElementById('competitor-template');
                if (!template) {
                    alert('Template competitor-template tidak ditemukan!');
                    return;
                }

                $dispatch('open-modal-tab', {
                    title: 'Add Question',
                    manual: '{{ route('competitor.store') }}',
                    group: '{{ $groups->id }}',
                    form: '{{ $form->id }}',
                    content: template.innerHTML
                })
            ">
        Add Kompetitor
    </button>
    <button type="button"
            class="bg-green-600 text-white px-4 py-2 rounded mt-2"
            @click="
                const template = document.getElementById('keterikatan_1-5');
                if (!template) {
                    alert('Template keterikatan_1-5 tidak ditemukan!');
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