@extends('layouts.app-role-modern')

@section('title', $form->name ?? 'Survei')
@section('suppressGlobalValidationErrors', true)

@section('content')
    @if (auth()->user()?->hasRole('surveyor'))
        <div class="mb-5 rounded-xl border border-blue-200 bg-blue-50 px-5 py-4 text-sm text-blue-800">
            <span class="font-semibold"><i class="fa-solid fa-person-chalkboard mr-2"></i>Mode Simulasi Surveyor</span>
            — jawaban pada halaman ini merupakan contoh tata cara pengisian dan tidak dihitung sebagai responden.
            <div class="mt-3 flex justify-end">
                <button id="surveyorAutofillButton" type="button"
                        title="Isi seluruh pertanyaan pada form ini dengan data dummy"
                        class="inline-flex items-center gap-2 rounded-lg bg-blue-600 px-4 py-2.5 font-semibold text-white shadow-sm transition hover:bg-blue-700">
                    <i class="fa-solid fa-wand-magic-sparkles"></i>
                    <span>Isi Otomatis</span>
                </button>
            </div>
        </div>
    @endif

<div id="surveyPage" class="mx-auto max-w-[1600px] space-y-6">
    @include('user.survey.partials.form-header')

    @php
        $serverInvalidQuestionIds = collect($errors->keys())
            ->map(function ($key) {
                return preg_match('/^answers\.(\d+)(?:\.|$)/', $key, $matches)
                    ? (int) $matches[1]
                    : null;
            })
            ->filter()
            ->unique()
            ->values();
    @endphp

    <form
        id="surveyAnswerForm"
        action="{{ route('survey.save', $form) }}"
        method="POST"
        novalidate
        data-server-invalid-question-ids='@json($serverInvalidQuestionIds)'
        class="space-y-6 pb-4"
    >
        @csrf

        <div class="space-y-6">

    {{-- Dipakai bersama oleh Responden dan Surveyor; Form Description (type 12) dirender terpisah. --}}
    @include('user.survey.forms.partials.form-description')
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

                    @case(14)
                        @include('user.survey.forms.respondent-competitor-assessment')
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


