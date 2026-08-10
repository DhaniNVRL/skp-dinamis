@extends('layouts.app')

@section('title', 'Lihat Jawaban Responden')

@section('content')
    @include('admin.shared.respondent-answer-detail', [
        'pageTitle' => 'Lihat Jawaban Responden',
        'pageDescription' => 'Tampilan read-only jawaban responden sesuai profil Surveyor.',
        'backUrl' => route('surveyor.dashboard'),
        'backLabel' => 'Kembali ke Dashboard Surveyor',
        'profile' => $profile,
    ])
@endsection