@extends('admin.layouts.app-modern')

@section('title', 'Detail Responden dan Jawaban')

@section('content')
    @include('admin.shared.respondent-answer-detail', [
        'pageTitle' => 'Detail Responden dan Jawaban',
        'pageDescription' => 'Profil, status survey, dan seluruh jawaban responden.',
        'backUrl' => $dashboardReturnUrl,
        'backLabel' => 'Kembali ke Dashboard',
    ])
@endsection

