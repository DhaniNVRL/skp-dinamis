@extends('admin.layouts.app-modern')

@section('title', 'Unit & Input Pertanyaan')

@section('content')

<div
    id="unitMainPage"
    class="mx-auto max-w-[1600px] space-y-6"
>
    {{-- ========================= --}}
    {{-- PAGE HEADER --}}
    {{-- ========================= --}}
    <div
        class="rounded-xl border border-gray-200
            bg-white p-6 shadow-sm"
    >
        <div
            class="flex flex-col gap-5
                lg:flex-row lg:items-center lg:justify-between"
        >
            <div>
                <a
                    href="{{ route('admin.groups', [
                        'id' => $groups->activity_id,
                    ]) }}"
                    class="mb-3 inline-flex items-center gap-2
                        text-sm font-medium text-blue-600
                        transition hover:text-blue-700"
                >
                    <i class="fa-solid fa-arrow-left"></i>
                    Kembali Ke Group
                </a>

                <h1 class="text-2xl font-bold text-gray-900">
                    Unit & Pertanyaan
                </h1>

                <p class="mt-1 text-sm text-gray-500">
                    Pengaturan Unit dan Input Pertanyaan
                    dari Group

                    <span class="font-semibold text-gray-800">
                        {{ $groups->name }}
                    </span>
                </p>
            </div>

            <div class="flex flex-wrap items-center gap-3">
                <div
                    class="min-w-[130px] rounded-xl
                        bg-indigo-50 px-5 py-4"
                >
                    <p
                        class="text-xs font-medium uppercase
                            tracking-wide text-indigo-500"
                    >
                        Group
                    </p>

                    <p class="mt-1 font-semibold text-indigo-700">
                        {{ $groups->name }}
                    </p>
                </div>

                <div
                    class="min-w-[130px] rounded-xl
                        bg-blue-50 px-5 py-4"
                >
                    <p
                        class="text-xs font-medium uppercase
                            tracking-wide text-blue-500"
                    >
                        Jumlah Unit
                    </p>

                    <p class="mt-1 text-xl font-bold text-blue-700">
                        {{ $units->count() }}
                    </p>
                </div>
            </div>
        </div>
    </div>

    {{-- ========================= --}}
    {{-- TAB CARD --}}
    {{-- ========================= --}}
    <div
        class="overflow-hidden rounded-xl
            border border-gray-200 bg-white shadow-sm"
    >
        {{-- TAB NAVIGATION --}}
        <div class="border-b border-gray-200 bg-gray-50">
            <nav
                id="unitTabs"
                class="flex flex-wrap items-center"
                data-tabs
            >
                <button
                    type="button"
                    data-unit-tab="unit"
                    class="unit-tab-button inline-flex items-center gap-2
                        border-b-2 border-blue-600 px-6 py-4
                        text-sm font-semibold text-blue-600 transition"
                >
                    <i class="fa-solid fa-building"></i>
                    Data Unit
                </button>

                <button
                    type="button"
                    data-unit-tab="question"
                    class="unit-tab-button inline-flex items-center gap-2
                        border-b-2 border-transparent px-6 py-4
                        text-sm font-semibold text-gray-600 transition
                        hover:border-gray-300 hover:text-gray-800"
                >
                    <i class="fa-solid fa-list-check"></i>
                    Pertanyaan
                </button>
            </nav>
        </div>

        {{-- TAB CONTENT --}}
        <div class="p-5">
            <div
                id="tab-unit"
                data-unit-tab-content="unit"
            >
                @include('admin.units.pages.units-data')
            </div>

            <div
                id="tab-question"
                data-unit-tab-content="question"
                class="hidden"
            >
                @include('admin.units.pages.question')
            </div>
        </div>
    </div>
</div>

{{-- ========================= --}}
{{-- MODAL UNIT --}}
{{-- ========================= --}}
@include('admin.units.units.modals.bulk-deleted')
@include('admin.units.units.modals.create')
@include('admin.units.units.modals.delete')
@include('admin.units.units.modals.edit')

{{-- ========================= --}}
{{-- MODAL FORM --}}
{{-- ========================= --}}
@include('admin.units.question.modals.create-form')
@include('admin.units.question.modals.edit-form')
@include('admin.units.question.modals.delete-form')

{{-- ========================= --}}
{{-- MODAL QUESTION --}}
{{-- ========================= --}}
@include('admin.units.question.modals.create-question')
@include('admin.units.question.modals.edit-question')
@include('admin.units.question.modals.delete-question')

{{-- ========================= --}}
{{-- MODAL OPTION --}}
{{-- ========================= --}}
@include('admin.units.question.modals.create-option')
@include('admin.units.question.modals.edit-option')
@include('admin.units.question.modals.delete-option')

{{-- ========================= --}}
{{-- MODAL DESCRIPTION --}}
{{-- ========================= --}}
@include('admin.units.question.modals.create-description')
@include('admin.units.question.modals.edit-description')
@include('admin.units.question.modals.delete-description')

{{-- ========================= --}}
{{-- MODAL COMPETITOR --}}
{{-- ========================= --}}
@include('admin.units.question.modals.create-competitor')
@include('admin.units.question.modals.edit-competitor')
@include('admin.units.question.modals.delete-competitor')

{{-- ========================= --}}
{{-- MODAL IMPORT QUESTION --}}
{{-- ========================= --}}
@include('admin.units.question.modals.import-question')

@endsection

@push('templates')

    {{-- UNIT --}}
    @include('admin.units.units.templates.create-row')

    {{-- COMPETITOR --}}
    @include('admin.units.question.templates.create-competitor-row')

    {{-- FORM --}}
    @include('admin.units.question.templates.create-form-row')

    {{-- QUESTION --}}
    @include('admin.units.question.templates.create-question-row')

    {{-- OPTION --}}
    @include('admin.units.question.templates.create-option-row')

    {{-- QUESTION TYPE PER FORM --}}
    @include('admin.units.question.templates.forms.general-questionnaire')
    @include('admin.units.question.templates.forms.customer-assessment-1-5')
    @include('admin.units.question.templates.forms.customer-assessment-1-7')
    @include('admin.units.question.templates.forms.engagement-assessment-1-5')
    @include('admin.units.question.templates.forms.engagement-assessment-1-7')
    @include('admin.units.question.templates.forms.ranking-1-3')
    @include('admin.units.question.templates.forms.ranking-1-5')
    @include('admin.units.question.templates.forms.strength-complaint-suggestion')
    @include('admin.units.question.templates.forms.complaint-suggestion')
    @include('admin.units.question.templates.forms.suggestion')
    @include('admin.units.question.templates.forms.competitor-1-5')
    @include('admin.units.question.templates.forms.competitor-1-7')
@include('admin.units.question.templates.forms.respondent-competitor')

@endpush

@push('scripts')
    <script src="{{ asset('js/units-page-fixes.js') }}?v=20260806-2"></script>
    <script src="{{ asset('js/unit-question-crud-fixes.js') }}?v=20260806-1"></script>
    <script src="{{ asset('js/question-bulk-selection.js') }}?v=20260806-1"></script>
@endpush

