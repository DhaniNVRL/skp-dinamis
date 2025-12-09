@extends('admin.layouts.app')

@section('title', 'Dashboard')

@section('content')
   <div class="max-w-4xl mx-auto py-8 px-4 bg-white shadow rounded">
    <h1 class="text-3xl font-semibold mb-4">Dashboard Admin</h1>
    <p class="mb-2">Selamat datang, <strong>{{ Auth::user()->name }}</strong></p>
    <p class="mb-4">Role Anda:
        <span class="inline-block bg-yellow-400 text-black text-sm px-2 py-1 rounded">Admin</span>
    </p>

    <form method="POST" action="{{ route('logout') }}">
        @csrf
        <button class="bg-red-600 hover:bg-red-700 text-white font-bold py-2 px-4 rounded">
            Logout
        </button>
    </form>
</div>
@endsection
