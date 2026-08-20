@extends('admin.layouts.app-modern')

@section('title', 'Pengelolaan Akun')

@section('content')
<div id="userPage" class="mx-auto max-w-[1600px] space-y-6">
    @include('admin.users.partials.page-header')

    @if (session('success'))
        <div data-alert class="flex items-start justify-between rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-700">
            <div><i class="fa-solid fa-circle-check mr-2"></i>{{ session('success') }}</div>
            <button type="button" data-alert-close class="ml-4 text-green-500 hover:text-green-700"><i class="fa-solid fa-xmark"></i></button>
        </div>
    @endif

    @if (session('successdelete'))
        <div data-alert class="flex items-start justify-between rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
            <div><i class="fa-solid fa-trash mr-2"></i>{{ session('successdelete') }}</div>
            <button type="button" data-alert-close class="ml-4 text-red-500 hover:text-red-700"><i class="fa-solid fa-xmark"></i></button>
        </div>
    @endif

    @if (session('error'))
        <div data-alert class="flex items-start justify-between rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
            <div><i class="fa-solid fa-circle-exclamation mr-2"></i>{{ session('error') }}</div>
            <button type="button" data-alert-close class="ml-4 text-red-500 hover:text-red-700"><i class="fa-solid fa-xmark"></i></button>
        </div>
    @endif

    @if ($errors->any())
        <div data-alert class="flex items-start justify-between rounded-lg border border-red-200 bg-red-50 p-4">
            <div>
                <p class="font-semibold text-red-700">Data belum dapat diproses:</p>
                <ul class="mt-2 list-disc space-y-1 pl-5 text-sm text-red-600">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            <button type="button" data-alert-close class="ml-4 text-red-500 hover:text-red-700"><i class="fa-solid fa-xmark"></i></button>
        </div>
    @endif

    <div class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm">
        <div class="border-b border-gray-200 bg-gray-50">
            <div class="inline-flex items-center gap-2 border-b-2 border-blue-600 bg-white px-6 py-4 text-sm font-semibold text-blue-600">
                <i class="fa-solid fa-users"></i>
                Data User
            </div>
        </div>

        <div class="space-y-4 p-5">
            @include('admin.users.partials.toolbar')
            @include('admin.users.partials.filter')
            @include('admin.users.partials.bulk-action')
            @include('admin.users.partials.table')
            @include('admin.users.partials.pagination')
        </div>
    </div>
</div>

@include('admin.users.modals.bulk-deleted')
@include('admin.users.modals.create')
@include('admin.users.modals.delete')
@include('admin.users.modals.edit')
@include('admin.users.modals.reset-password')
@include('admin.users.modals.reset-profile')
@include('admin.users.modals.delete-answers')
@include('admin.users.modals.reopen-survey')
@endsection

@push('templates')
    @include('admin.users.templates.create-row')
@endpush

