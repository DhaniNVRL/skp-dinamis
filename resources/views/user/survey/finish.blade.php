@extends('layouts.app-role-modern')

@section('title', 'Selesaikan Survei')

@section('content')
<div class="mx-auto max-w-xl rounded-xl border border-gray-200 bg-white p-8 text-center shadow-sm">
    <span class="mx-auto inline-flex h-16 w-16 items-center justify-center rounded-full bg-green-100 text-2xl text-green-600"><i class="fa-solid fa-flag-checkered"></i></span>
    <h1 class="mt-5 text-2xl font-bold text-gray-900">Selesaikan survei?</h1>
    <p class="mt-2 text-gray-500">Pastikan seluruh jawaban sudah benar. Survei yang telah selesai tidak dapat diedit kembali.</p>
    <div class="mt-7 flex justify-center gap-3">
        <a href="{{ $lastForm ? route('survey.show', $lastForm) : route('survey.index') }}" class="rounded-lg border border-gray-300 px-5 py-2.5 font-medium text-gray-700 hover:bg-gray-50">Periksa Kembali</a>
        <form action="{{ route('survey.finish') }}" method="POST">@csrf
            <button type="submit" class="rounded-lg bg-green-600 px-5 py-2.5 font-semibold text-white hover:bg-green-700">Ya, Akhiri Survei</button>
        </form>
    </div>
</div>
@endsection

