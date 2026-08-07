@extends('layouts.app')

@section('title', 'Review Jawaban User')

@section('content')
    @include('admin.shared.respondent-answer-detail', [
        'pageTitle' => 'Review Jawaban User',
        'pageDescription' => 'Profil, status survey, dan seluruh jawaban akun ini.',
        'backUrl' => route('admin.datauser'),
        'backLabel' => 'Kembali ke Data User',
        'profile' => $user->profile,
    ])
@endsection
