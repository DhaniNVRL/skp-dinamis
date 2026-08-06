@extends('layouts.app')

@section('title', 'Group & Complete Profile')

@section('content')
<div class="mx-auto max-w-[1600px] space-y-6">
    @include('admin.groups.groups.partials.page-header')

    @if (session('success'))
        <div data-alert class="flex items-center justify-between rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-700"><span><i class="fa-solid fa-circle-check mr-2"></i>{{ session('success') }}</span><button type="button" data-alert-close><i class="fa-solid fa-xmark"></i></button></div>
    @endif
    @if (session('successdelete'))
        <div data-alert class="flex items-center justify-between rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700"><span><i class="fa-solid fa-trash mr-2"></i>{{ session('successdelete') }}</span><button type="button" data-alert-close><i class="fa-solid fa-xmark"></i></button></div>
    @endif
    @if (session('error'))
        <div data-alert class="flex items-center justify-between rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700"><span>{{ session('error') }}</span><button type="button" data-alert-close><i class="fa-solid fa-xmark"></i></button></div>
    @endif
    @if ($errors->any())
        <div class="rounded-lg border border-red-200 bg-red-50 p-4 text-red-700"><p class="font-semibold">Data belum dapat diproses:</p><ul class="mt-2 list-disc pl-5 text-sm">@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>
    @endif

    <div class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm">
        <div class="flex overflow-x-auto border-b border-gray-200 bg-gray-50">
            <button type="button" data-tab="group" class="tab-button whitespace-nowrap border-b-2 border-blue-600 bg-white px-6 py-4 text-sm font-semibold text-blue-600 transition"><i class="fa-solid fa-layer-group mr-2"></i>Data Group</button>
            <button type="button" data-tab="profile" class="tab-button whitespace-nowrap border-b-2 border-transparent px-6 py-4 text-sm font-semibold text-gray-500 transition hover:text-gray-700"><i class="fa-solid fa-clipboard-list mr-2"></i>Complete Profile</button>
        </div>
        <div class="p-5">
            <div id="tab-group">@include('admin.groups.pages.group-data')</div>
            <div id="tab-profile" class="hidden">@include('admin.groups.pages.complete-profile')</div>
        </div>
    </div>
</div>

@include('admin.groups.groups.modals.create')
@include('admin.groups.groups.modals.edit')
@include('admin.groups.groups.modals.delete')
@include('admin.groups.groups.modals.bulk-deleted')
@include('admin.groups.complete-profile.modals.create')
@include('admin.groups.complete-profile.modals.edit')
@include('admin.groups.complete-profile.modals.delete')
@endsection

@push('templates')
    @include('admin.groups.groups.templates.create-row')
    @include('admin.groups.complete-profile.templates.create-row')
@endpush
