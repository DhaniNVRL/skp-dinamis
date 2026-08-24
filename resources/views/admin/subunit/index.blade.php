@extends('admin.layouts.app-modern')

@section('title', 'Sub Unit')

@section('content')
<div
    id="subUnitPage"
    data-active-tab="{{ $activeTab ?? request('tab', 'subunit') }}"
    data-unit-id="{{ $units->id }}"
    class="space-y-6"
>
    {{-- PAGE HEADER --}}
    @include('admin.subunit.subunit.partials.page-header')

    {{-- SUCCESS --}}
    @if (session('success'))
        <div
            data-alert
            class="flex items-start justify-between rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-700"
        >
            <div>
                <i class="fa-solid fa-circle-check mr-2"></i>
                {{ session('success') }}
            </div>

            <button
                type="button"
                data-alert-close
                class="ml-4 text-green-500 hover:text-green-700"
            >
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>
    @endif

    {{-- SUCCESS DELETE --}}
    @if (session('successdelete'))
        <div
            data-alert
            class="flex items-start justify-between rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700"
        >
            <div>
                <i class="fa-solid fa-trash mr-2"></i>
                {{ session('successdelete') }}
            </div>

            <button
                type="button"
                data-alert-close
                class="ml-4 text-red-500 hover:text-red-700"
            >
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>
    @endif

    {{-- ERROR --}}
    @if (session('error'))
        <div
            data-alert
            class="flex items-start justify-between rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700"
        >
            <div>
                <i class="fa-solid fa-circle-exclamation mr-2"></i>
                {{ session('error') }}
            </div>

            <button
                type="button"
                data-alert-close
                class="ml-4 text-red-500 hover:text-red-700"
            >
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>
    @endif

    {{-- VALIDATION ERROR --}}
    @if ($errors->any())
        <div
            data-alert
            class="flex items-start justify-between rounded-lg border border-red-200 bg-red-50 p-4"
        >
            <div>
                <div class="font-semibold text-red-700">
                    Data belum dapat diproses:
                </div>

                <ul class="mt-2 list-disc space-y-1 pl-5 text-sm text-red-600">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>

            <button
                type="button"
                data-alert-close
                class="ml-4 text-red-500 hover:text-red-700"
            >
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>
    @endif

    {{-- TAB WRAPPER --}}
    <div
        id="subUnitTabContainer"
        class="overflow-hidden rounded-lg border border-gray-200 bg-white shadow-sm"
    >
        {{-- TAB NAVIGATION --}}
        <div
            id="subUnitTabNavigation"
            data-subunit-tabs
            class="flex overflow-x-auto border-b border-gray-200 bg-gray-50"
        >
            <button
                id="subUnitTabButton"
                type="button"
                data-subunit-tab="subunit"
                class="subunit-tab-button whitespace-nowrap border-b-2 border-transparent px-6 py-4 text-sm font-semibold text-gray-500 transition hover:text-gray-700"
            >
                <i class="fa-solid fa-building mr-2"></i>
                Sub Unit
            </button>

            <button
                id="hideShowTabButton"
                type="button"
                data-subunit-tab="hide-show"
                class="subunit-tab-button whitespace-nowrap border-b-2 border-transparent px-6 py-4 text-sm font-semibold text-gray-500 transition hover:text-gray-700"
            >
                <i class="fa-solid fa-eye mr-2"></i>
                Hide and Show
            </button>

            <button
                id="questionPreviewTabButton"
                type="button"
                data-subunit-tab="question-preview"
                class="subunit-tab-button whitespace-nowrap border-b-2 border-transparent px-6 py-4 text-sm font-semibold text-gray-500 transition hover:text-gray-700"
            >
                <i class="fa-solid fa-list-check mr-2"></i>
                Tampilan Pertanyaan
            </button>
        </div>

        {{-- TAB CONTENT --}}
        <div class="p-5">
            {{-- SUB UNIT --}}
            <div
                id="subUnitTabContent"
                data-subunit-content="subunit"
                class="subunit-tab-content"
            >
                @include('admin.subunit.pages.subunit')
            </div>

            {{-- HIDE AND SHOW --}}
            <div
                id="hideShowTabContent"
                data-subunit-content="hide-show"
                class="subunit-tab-content hidden"
            >
                @include('admin.subunit.pages.hide-show')
                <div class="mt-6">
                    @include('admin.subunit.pages.competitor-visibility')
                </div>
            </div>

            {{-- TAMPILAN PERTANYAAN --}}
            <div
                id="questionPreviewTabContent"
                data-subunit-content="question-preview"
                class="subunit-tab-content hidden"
            >
                @include('admin.subunit.pages.question-preview')
            </div>
        </div>
    </div>
</div>

{{-- MODALS --}}
@include('admin.subunit.subunit.modals.create-subunit')
@include('admin.subunit.subunit.modals.edit-subunit')
@include('admin.subunit.subunit.modals.delete-subunit')
@include('admin.subunit.subunit.modals.bulk-delete-subunit')

{{-- TEMPLATE --}}
@include('admin.subunit.subunit.templates.create-row-subunit')
@endsection
