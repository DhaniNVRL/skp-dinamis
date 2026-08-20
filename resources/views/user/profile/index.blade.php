@extends('layouts.app-role-modern')

@section('title', 'Profil Responden')

@section('content')
<div
    id="respondentProfilePage"
    class="mx-auto max-w-6xl space-y-6"
>
    {{-- SUCCESS --}}
    @if (session('success'))
        <div
            data-alert
            class="flex items-start justify-between rounded-xl border border-green-200 bg-green-50 px-5 py-4 text-sm text-green-700"
        >
            <div class="flex items-center gap-3">
                <i class="fa-solid fa-circle-check"></i>
                <span>{{ session('success') }}</span>
            </div>

            <button
                type="button"
                data-alert-close
                class="text-green-500 hover:text-green-700"
            >
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>
    @endif

    {{-- ERROR --}}
    @if (session('error'))
        <div
            data-alert
            class="flex items-start justify-between rounded-xl border border-red-200 bg-red-50 px-5 py-4 text-sm text-red-700"
        >
            <div class="flex items-center gap-3">
                <i class="fa-solid fa-circle-exclamation"></i>
                <span>{{ session('error') }}</span>
            </div>

            <button
                type="button"
                data-alert-close
                class="text-red-500 hover:text-red-700"
            >
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>
    @endif

    {{-- PAGE HEADER --}}
    <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm">
        <div class="flex flex-col justify-between gap-5 md:flex-row md:items-center">
            <div class="flex items-center gap-4">
                <div class="flex h-16 w-16 items-center justify-center rounded-full bg-blue-100 text-2xl text-blue-600">
                    <i class="fa-solid fa-user"></i>
                </div>

                <div>
                    <h1 class="text-2xl font-bold text-gray-800">
                        Profil Responden
                    </h1>

                    <p class="mt-1 text-sm text-gray-500">
                        Informasi pekerjaan dan status survei Anda.
                    </p>
                </div>
            </div>
        </div>
    </div>

    {{-- PROFILE CONTENT --}}
    <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
        <div class="lg:col-span-2">
            @include(
                'user.profile.partials.information'
            )
        </div>

        <div>
            @include(
                'user.profile.partials.survey-status'
            )
        </div>
    </div>
</div>
@endsection
