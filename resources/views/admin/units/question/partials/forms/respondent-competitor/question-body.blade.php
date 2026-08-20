@php($type = (int) ($question->questiontype_id ?? 0))
@if ($type === 1)
    @include('admin.units.question.partials.forms.competitor-1-7.options.title', compact('question', 'form'))
@elseif ($type === 2)
    <div class="rounded-xl border border-gray-200 bg-white p-5">
        <div class="flex items-start gap-3">
            <span class="inline-flex min-w-12 justify-center rounded-lg bg-violet-100 px-2.5 py-1 text-sm font-semibold text-violet-700">{{ $question->no_header }}{{ $question->no }}</span>
            <div><h4 class="font-semibold text-gray-800">{{ $question->name }}</h4><p class="mt-1 text-xs text-gray-500">Setiap kompetitor yang dimasukkan responden dinilai dengan skala 1–7 dan 0.</p></div>
        </div>
        <div class="mt-4 flex flex-wrap items-center justify-center gap-2">
            @foreach ([1,2,3,4,5,6,7] as $value)<span class="inline-flex h-9 w-9 items-center justify-center rounded-full border border-violet-300 text-sm text-violet-700">{{ $value }}</span>@endforeach
            <span class="mx-2 h-9 border-l border-gray-300"></span><span class="inline-flex h-9 w-9 items-center justify-center rounded-full border border-amber-300 bg-amber-50 text-sm text-amber-700">0</span>
        </div>
    </div>
@else
    <div class="rounded-lg border border-red-200 bg-red-50 p-4 text-sm text-red-700">Gunakan tipe pertanyaan Judul atau penilaian kompetitor.</div>
@endif
