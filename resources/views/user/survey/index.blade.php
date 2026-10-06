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
        data-draft-url="{{ route('survey.draft.store', $form) }}"
        data-form-type="{{ (int) $form->formtype_id }}"
        data-has-validation-errors="{{ $errors->any() ? '1' : '0' }}"
        data-server-invalid-question-ids='@json($serverInvalidQuestionIds)'
        class="space-y-6 pb-4"
    >
        @csrf

        <div id="surveyDraftStatus" class="fixed bottom-5 right-5 z-40 hidden items-center gap-2 rounded-lg border bg-white px-4 py-2.5 text-sm font-medium shadow-lg" role="status" aria-live="polite"></div>
        <script id="surveyDraftPayload" type="application/json">{!! json_encode($surveyDraftPayload ?? [], JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) !!}</script>
        <script id="blockedFeedbackPhrases" type="application/json">{!! json_encode($blockedFeedbackPhrases ?? [], JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) !!}</script>

        <div class="space-y-6">
            @foreach ($surveyForms as $formContext)
                @include('user.survey.partials.merged-form-section', $formContext)
            @endforeach
        </div>

        @include('user.survey.partials.navigation')
    </form>
</div>
@endsection

@push('scripts')
@endpush


