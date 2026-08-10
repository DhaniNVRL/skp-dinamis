@extends('layouts.app')

@section('title', 'Dashboard Responden')

@section('content')
@php
    $profile = $profile ?? null;
    $surveySession = $surveySession ?? null;
    $user = $user ?? auth()->user();

    $profileComplete = $profile
        && filled($profile->activity_id)
        && filled($profile->group_id)
        && filled($profile->unit_id);

    $status = $surveySession?->status ?? 'not_started';
    $surveyLocked = $status === 'completed'
        && ($user?->hasRole('user') || $user?->hasRole('surveyor'));

    $statusLabel = match ($status) {
        'in_progress' => 'Sedang Berjalan',
        'completed' => 'Selesai',
        default => 'Belum Mulai',
    };

    $statusClass = match ($status) {
        'in_progress' => 'border-amber-200 bg-amber-50 text-amber-700',
        'completed' => 'border-green-200 bg-green-50 text-green-700',
        default => 'border-gray-200 bg-gray-100 text-gray-700',
    };
@endphp

<div class="mx-auto max-w-6xl space-y-6">

    @if ($user?->hasRole('surveyor'))
        <div class="rounded-xl border border-blue-200 bg-blue-50 px-5 py-4 text-sm text-blue-800">
            <div class="font-semibold"><i class="fa-solid fa-person-chalkboard mr-2"></i>Mode Contoh Surveyor</div>
            <p class="mt-1">Akun ini digunakan untuk memperagakan tata cara pengisian. Jawaban contoh tidak dihitung sebagai responden.</p>
        </div>
    @endif

    {{-- HEADER --}}
    <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm">
        <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">

            <div class="flex items-center gap-4">
                <div class="flex h-16 w-16 items-center justify-center rounded-full bg-blue-100 text-blue-600">
                    <i class="fa-solid fa-user text-2xl"></i>
                </div>

                <div>
                    <h1 class="text-2xl font-bold text-gray-900">
                        Profil Responden
                    </h1>

                    <p class="mt-1 text-sm text-gray-500">
                        Informasi pekerjaan dan status survei Anda.
                    </p>
                </div>
            </div>

            @if (!$profileComplete)
                <a
                    href="{{ route('profile.edit') }}"
                    class="inline-flex items-center justify-center gap-2 rounded-lg
                           bg-blue-600 px-5 py-2.5 text-sm font-semibold text-white
                           transition hover:bg-blue-700"
                >
                    <i class="fa-solid fa-pen-to-square"></i>
                    Lengkapi Profil
                </a>
            @elseif ($user?->hasRole('surveyor'))
                <button
                    type="button"
                    data-modal-open="surveyorResetAccountModal"
                    class="inline-flex items-center justify-center gap-2 rounded-lg
                           bg-red-600 px-5 py-2.5 text-sm font-semibold text-white
                           transition hover:bg-red-700"
                >
                    <i class="fa-solid fa-user-rotate"></i>
                    Reset Account
                </button>
            @elseif (!$surveyLocked)
                <button
                    type="button"
                    disabled
                    title="Profil sudah lengkap"
                    class="inline-flex cursor-not-allowed items-center justify-center gap-2
                           rounded-lg bg-gray-200 px-5 py-2.5 text-sm font-semibold
                           text-gray-400"
                >
                    <i class="fa-solid fa-circle-check"></i>
                    Profil Lengkap
                </button>
            @endif
        </div>
    </div>

    @if (session('success'))
        <div class="rounded-xl border border-green-200 bg-green-50 px-5 py-4 text-sm text-green-700">
            <i class="fa-solid fa-circle-check mr-2"></i>
            {{ session('success') }}
        </div>
    @endif

    @if (session('error'))
        <div class="rounded-xl border border-red-200 bg-red-50 px-5 py-4 text-sm text-red-700">
            <i class="fa-solid fa-circle-exclamation mr-2"></i>
            {{ session('error') }}
        </div>
    @endif

    <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">

        {{-- INFORMASI RESPONDEN --}}
        <div class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm lg:col-span-2">

            <div class="border-b border-gray-200 px-6 py-5">
                <h2 class="text-lg font-semibold text-gray-900">
                    Informasi Responden
                </h2>

                <p class="mt-1 text-sm text-gray-500">
                    Informasi aktivitas, bidang kerja, dan unit responden.
                </p>
            </div>

            <div class="divide-y divide-gray-200">

                {{-- ACTIVITY --}}
                <div class="flex items-center gap-4 px-6 py-5">
                    <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-lg bg-purple-100 text-purple-600">
                        <i class="fa-solid fa-briefcase"></i>
                    </div>

                    <div>
                        <div class="text-sm text-gray-500">
                            Aktivitas
                        </div>

                        <div class="mt-1 font-semibold text-gray-900">
                            {{ $profile?->activity?->name ?? '-' }}
                        </div>
                    </div>
                </div>

                {{-- GROUP --}}
                <div class="flex items-center gap-4 px-6 py-5">
                    <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-lg bg-indigo-100 text-indigo-600">
                        <i class="fa-solid fa-layer-group"></i>
                    </div>

                    <div>
                        <div class="text-sm text-gray-500">
                            Bidang Kerja / Group
                        </div>

                        <div class="mt-1 font-semibold text-gray-900">
                            {{ $profile?->group?->name ?? '-' }}
                        </div>
                    </div>
                </div>

                {{-- UNIT --}}
                <div class="flex items-center gap-4 px-6 py-5">
                    <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-lg bg-blue-100 text-blue-600">
                        <i class="fa-solid fa-building"></i>
                    </div>

                    <div>
                        <div class="text-sm text-gray-500">
                            Unit / Jabatan
                        </div>

                        <div class="mt-1 font-semibold text-gray-900">
                            {{ $profile?->unit?->name ?? '-' }}
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- STATUS SURVEI --}}
        <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm">

            <div class="flex items-start justify-between gap-3">
                <h2 class="text-lg font-semibold text-gray-900">
                    Status Survei
                </h2>

                <span class="inline-flex items-center gap-2 rounded-full border px-3 py-1 text-xs font-semibold {{ $statusClass }}">
                    @if ($status === 'completed')
                        <i class="fa-solid fa-circle-check"></i>
                    @elseif ($status === 'in_progress')
                        <i class="fa-solid fa-spinner"></i>
                    @else
                        <i class="fa-solid fa-clock"></i>
                    @endif

                    {{ $statusLabel }}
                </span>
            </div>

            <p class="mt-5 text-sm text-gray-500">
                @if ($status === 'completed')
                    Survei telah berhasil diselesaikan.
                @elseif ($status === 'in_progress')
                    Pengisian survei belum selesai.
                @else
                    Responden belum memulai pengisian survei.
                @endif
            </p>

            <div class="my-6 border-t border-gray-200"></div>

            <div class="space-y-5">
                <div>
                    <div class="text-xs font-semibold uppercase tracking-wide text-gray-400">
                        Waktu Mulai
                    </div>

                    <div class="mt-1 text-sm font-medium text-gray-700">
                        {{ $surveySession?->started_at?->format('d M Y H:i') ?? '-' }}
                    </div>
                </div>

                <div>
                    <div class="text-xs font-semibold uppercase tracking-wide text-gray-400">
                        Waktu Selesai
                    </div>

                    <div class="mt-1 text-sm font-medium text-gray-700">
                        {{ $surveySession?->finished_at?->format('d M Y H:i') ?? '-' }}
                    </div>
                </div>
            </div>

            <div class="mt-7">
                @if (!$profileComplete)
                    <button
                        type="button"
                        disabled
                        title="Lengkapi profil terlebih dahulu"
                        class="inline-flex w-full cursor-not-allowed items-center justify-center
                               gap-2 rounded-lg bg-gray-300 px-5 py-3 font-semibold
                               text-gray-500"
                    >
                        <i class="fa-solid fa-lock"></i>
                        Lengkapi Profil Dahulu
                    </button>

                    <p class="mt-2 text-center text-xs text-red-500">
                        Activity, group, dan unit wajib dilengkapi.
                    </p>                @elseif ($status === 'completed')
                    <p class="rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-center text-sm font-medium text-green-700">
                        {{ $user?->hasRole('surveyor')
                            ? 'Simulasi telah selesai dan dikunci sampai Admin melakukan Reset Account.'
                            : 'Pengisian survei telah selesai dan dikunci.' }}
                    </p>                @elseif ($status === 'in_progress')
                    <a
                        href="{{ route('survey.index') }}"
                        class="inline-flex w-full items-center justify-center gap-2
                               rounded-lg bg-amber-500 px-5 py-3 font-semibold
                               text-white transition hover:bg-amber-600"
                    >
                        <i class="fa-solid fa-arrow-right"></i>
                        Lanjutkan Survei
                    </a>
                @else
                    <a
                        href="{{ route('survey.index') }}"
                        class="inline-flex w-full items-center justify-center gap-2
                               rounded-lg bg-indigo-600 px-5 py-3 font-semibold
                               text-white transition hover:bg-indigo-700"
                    >
                        <i class="fa-solid fa-clipboard-list"></i>
                        Mulai Survei
                    </a>
                @endif
            </div>
        </div>
    </div>
</div>
@if ($user?->hasRole('surveyor') && $profileComplete)
    <div id="surveyorResetAccountModal" data-modal class="fixed inset-0 z-50 hidden items-center justify-center bg-black/50 p-4">
        <div class="w-full max-w-lg overflow-hidden rounded-xl bg-white shadow-xl">
            <form method="POST" action="{{ route('surveyor.reset-account') }}">
                @csrf
                <div class="flex items-center justify-between border-b border-gray-200 px-6 py-4">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900">Reset Account</h3>
                        <p class="mt-1 text-sm text-gray-500">Tindakan ini tidak dapat dibatalkan.</p>
                    </div>
                    <button type="button" data-modal-close="surveyorResetAccountModal" class="text-gray-400 transition hover:text-red-600" aria-label="Tutup"><i class="fa-solid fa-xmark text-xl"></i></button>
                </div>
                <div class="p-6">
                    <div class="rounded-xl border border-red-200 bg-red-50 p-4 text-sm text-red-700">
                        <div class="flex items-start gap-3">
                            <i class="fa-solid fa-triangle-exclamation mt-0.5"></i>
                            <div>
                                <p class="font-semibold">Apakah Anda yakin?</p>
                                <p class="mt-2">Karena seluruh jawaban, progres survei, Activity, Group, dan Unit akan terhapus untuk <strong class="text-red-900">{{ $user->username }}</strong>.</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="flex justify-end gap-3 border-t border-gray-200 bg-gray-50 px-6 py-4">
                    <button type="button" data-modal-close="surveyorResetAccountModal" class="rounded-lg border border-gray-300 px-4 py-2 text-sm text-gray-700 transition hover:bg-gray-100">Batal</button>
                    <button type="submit" class="rounded-lg bg-red-600 px-4 py-2 text-sm font-semibold text-white transition hover:bg-red-700"><i class="fa-solid fa-user-rotate mr-2"></i>Ya, Reset Account</button>
                </div>
            </form>
        </div>
    </div>
@endif

@endsection