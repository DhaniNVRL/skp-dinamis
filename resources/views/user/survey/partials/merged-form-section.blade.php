<section class="space-y-4" data-merged-form-id="{{ $form->id }}" data-form-type="{{ (int) $form->formtype_id }}">
    @if (($surveyForms ?? collect())->count() > 1)
        <div class="rounded-xl border border-indigo-200 bg-indigo-50 px-5 py-4">
            <div class="text-xs font-semibold uppercase tracking-wide text-indigo-500">Form {{ $form->no_urut }}</div>
            <h2 class="mt-1 text-lg font-bold text-indigo-900">{{ $form->name }}</h2>
        </div>
    @endif

    @include('user.survey.forms.partials.form-description')
    <div class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm md:p-7">
        @switch((int) $form->formtype_id)
            @case(1) @include('user.survey.forms.general-questionnaire') @break
            @case(2) @include('user.survey.forms.customer-assessment-1-5') @break
            @case(3) @include('user.survey.forms.customer-assessment-1-7') @break
            @case(15) @include('user.survey.forms.customer-assessment-1-5-without-zero') @break
            @case(16) @include('user.survey.forms.customer-assessment-1-7-without-zero') @break
            @case(17) @include('user.survey.forms.customer-assessment-1-10') @break
            @case(19) @include('user.survey.forms.customer-assessment-1-10-without-zero') @break
            @case(4) @include('user.survey.forms.engagement-assessment-1-5') @break
            @case(5) @include('user.survey.forms.engagement-assessment-1-7') @break
            @case(18) @include('user.survey.forms.engagement-assessment-1-10') @break
            @case(6) @include('user.survey.forms.ranking-1-3') @break
            @case(7) @include('user.survey.forms.ranking-1-5') @break
            @case(8) @include('user.survey.forms.strength-complaint-suggestion') @break
            @case(9) @include('user.survey.forms.complaint-suggestion') @break
            @case(10) @include('user.survey.forms.suggestion') @break
            @case(11) @include('user.survey.forms.competitor-assessment-1-5') @break
            @case(12) @include('user.survey.forms.description') @break
            @case(13) @include('user.survey.forms.competitor-assessment-1-7') @break
            @case(14) @include('user.survey.forms.respondent-competitor-assessment') @break
            @default @include('user.survey.partials.empty', ['message' => 'Jenis form belum didukung.'])
        @endswitch
    </div>
</section>
