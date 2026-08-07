@extends('layouts.app')

@section('title', $form->name ?? 'Survei')

@section('content')
<div id="surveyPage" class="mx-auto max-w-[1600px] space-y-6">
    @include('user.survey.partials.form-header')

    @if ($errors->any())
        <div class="rounded-xl border border-red-200 bg-red-50 p-4 text-sm text-red-700">
            <p class="font-semibold">Semua pertanyaan wajib diisi.</p>
            <ul class="mt-2 list-disc space-y-1 pl-5">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form
        id="surveyAnswerForm"
        action="{{ route('survey.save', $form) }}"
        method="POST"
        novalidate
        class="space-y-6"
    >
        @csrf

        <div class="space-y-6">

    {{-- FORM DESCRIPTION --}}
    @if (
        (int) $form->formtype_id !== 12 &&
        $form->description
    )
        @php
            $descriptionContent =
                $form->description->content
                ?? $form->description->description
                ?? $form->description->text
                ?? null;
        @endphp

        @if ($descriptionContent)
            <div
                class="overflow-hidden rounded-xl border
                       border-indigo-200 bg-white shadow-sm"
            >
                <div
                    class="flex items-center gap-3 border-b
                           border-indigo-200 bg-indigo-50 px-6 py-4"
                >
                    <span
                        class="inline-flex h-10 w-10 items-center
                               justify-center rounded-lg bg-indigo-100
                               text-indigo-600"
                    >
                        <i class="fa-solid fa-circle-info"></i>
                    </span>

                    <div>
                        <h2 class="font-semibold text-gray-900">
                            Petunjuk Pengisian
                        </h2>

                        <p class="text-sm text-gray-500">
                            Bacalah petunjuk sebelum mengisi pertanyaan.
                        </p>
                    </div>
                </div>

                <article
                    class="prose max-w-none px-6 py-5 text-gray-700"
                >
                    {!! $descriptionContent !!}
                </article>
            </div>
        @endif
    @endif

    {{-- FORM QUESTIONS --}}
    <div
        class="rounded-xl border border-gray-200
               bg-white p-5 shadow-sm md:p-7"
            >
                @switch((int) $form->formtype_id)

                    @case(1)
                        @include('user.survey.forms.general-questionnaire')
                        @break

                    @case(2)
                        @include('user.survey.forms.customer-assessment-1-5')
                        @break

                    @case(3)
                        @include('user.survey.forms.customer-assessment-1-7')
                        @break

                    @case(4)
                        @include('user.survey.forms.engagement-assessment-1-5')
                        @break

                    @case(5)
                        @include('user.survey.forms.engagement-assessment-1-7')
                        @break

                    @case(6)
                        @include('user.survey.forms.ranking-1-3')
                        @break

                    @case(7)
                        @include('user.survey.forms.ranking-1-5')
                        @break

                    @case(8)
                        @include('user.survey.forms.strength-complaint-suggestion')
                        @break

                    @case(9)
                        @include('user.survey.forms.complaint-suggestion')
                        @break

                    @case(10)
                        @include('user.survey.forms.suggestion')
                        @break

                    @case(11)
                        @include('user.survey.forms.competitor-assessment-1-5')
                        @break

                    @case(12)
                        @include('user.survey.forms.description')
                        @break

                    @case(13)
                        @include('user.survey.forms.competitor-assessment-1-7')
                        @break

                    @default
                        @include('user.survey.partials.empty', [
                            'message' => 'Jenis form belum didukung.',
                        ])

                @endswitch
            </div>
        </div>

        @include('user.survey.partials.navigation')
    </form>
</div>
@endsection

@push('scripts')
@endpush
