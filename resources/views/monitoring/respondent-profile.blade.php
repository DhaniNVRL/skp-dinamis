@extends('layouts.app')

@section('title', 'Profil Responden')

@section('content')
<div class="mx-auto max-w-5xl space-y-6">
    <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm">
        <a href="{{ route('surveyor.dashboard') }}" class="inline-flex items-center gap-2 text-sm font-medium text-blue-600 hover:text-blue-800">
            <i class="fa-solid fa-arrow-left"></i>Kembali ke Dashboard Surveyor
        </a>
        <div class="mt-4 flex items-center gap-4">
            <div class="flex h-14 w-14 items-center justify-center rounded-full bg-blue-100 text-xl text-blue-600"><i class="fa-solid fa-user"></i></div>
            <div><h1 class="text-2xl font-bold text-gray-900">Profil Responden</h1><p class="mt-1 text-sm text-gray-500">Informasi profil dalam akses Surveyor.</p></div>
        </div>
    </div>

    <div class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm">
        <dl class="grid gap-0 sm:grid-cols-2">
            @foreach ([
                'Username' => $profile->user?->username,
                'Nama Lengkap' => $profile->fullname,
                'Email' => $profile->email,
                'No. Handphone' => $profile->no_handphone,
                'Role' => $profile->user?->role?->name,
                'Activity' => $profile->activity?->name,
                'Group' => $profile->group?->name,
                'Unit' => $profile->unit?->name,
                'Status Survey' => match ($profile->user?->surveySession?->status) {
                    'completed' => 'Sudah Mengisi',
                    'in_progress' => 'Sedang Mengisi',
                    default => 'Belum Mengisi',
                },
            ] as $label => $value)
                <div class="border-b border-gray-100 px-6 py-5 odd:sm:border-r">
                    <dt class="text-xs font-semibold uppercase tracking-wide text-gray-400">{{ $label }}</dt>
                    <dd class="mt-1 break-words text-sm font-semibold text-gray-800">{{ filled($value) ? $value : '-' }}</dd>
                </div>
            @endforeach
        </dl>
    </div>
</div>
@endsection