@extends('admin.layouts.app')

@section('title', 'Detail Responden dan Jawaban')

@section('content')
    @php
        $dashboardRoute = match (strtolower((string) auth()->user()?->role?->name)) {
            'pm' => route('pm.dashboard'),
            'surveyor' => route('surveyor.dashboard'),
            default => route('admin.dashboard'),
        };
    @endphp

    @include('admin.shared.respondent-answer-detail', [
        'pageTitle' => 'Detail Responden dan Jawaban',
        'pageDescription' => 'Profil, status survey, dan seluruh jawaban responden.',
        'backUrl' => $dashboardRoute,
        'backLabel' => 'Kembali ke Dashboard',
    ])
@endsection
