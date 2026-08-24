<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', '')</title>
    <link rel="icon" type="image/png" href="{{ asset('images/logo-sucofindo.png') }}?v=20260820-sucofindo-2">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.6/Sortable.min.js"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.14.9/dist/cdn.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    @vite(['resources/css/app.css','resources/js/app.js'])
    <style>[x-cloak]{display:none!important}.role-scroll::-webkit-scrollbar{width:6px}.role-scroll::-webkit-scrollbar-thumb{background:#64748b;border-radius:999px}</style>
    @stack('styles')
</head>
@php
    $layoutUser = auth()->user();
    $isMonitoring = $layoutUser?->hasRole('monitoring');
    $isSurveyor = $layoutUser?->hasRole('surveyor');
    $layoutProfile = $layoutUser?->profile;
    $profileComplete = $layoutProfile && filled($layoutProfile->activity_id) && filled($layoutProfile->group_id) && filled($layoutProfile->unit_id);
    $surveyLocked = ($layoutUser?->hasRole('user') || $isSurveyor) && $layoutUser?->surveySession?->status === 'completed';
    $roleTitle = $isMonitoring ? 'Monitoring' : ($isSurveyor ? 'Surveyor · Akun Contoh' : 'Responden');
@endphp
<body class="min-h-screen bg-slate-50 text-slate-900" x-data="{ sidebarOpen:false, profileOpen:false }">
<div class="min-h-screen lg:flex">
    <div x-cloak x-show="sidebarOpen" class="fixed inset-0 z-40 bg-slate-950/50 lg:hidden" @click="sidebarOpen=false"></div>
    <aside class="fixed inset-y-0 left-0 z-50 flex w-[270px] -translate-x-full flex-col bg-gradient-to-b from-blue-900 via-blue-800 to-blue-950 text-white transition-transform lg:translate-x-0" :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'">
        <div class="flex h-20 items-center border-b border-white/10 px-6">
            <img src="{{ asset('images/logo4.png') }}" alt="" class="max-h-11 max-w-[205px] object-contain brightness-0 invert">
        </div>
        <div class="border-b border-white/10 px-5 py-5">
            <div class="flex items-center gap-3">
                <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-full bg-white/15 text-lg font-black ring-1 ring-white/20">{{ strtoupper(substr($layoutUser->username ?? 'U',0,1)) }}</span>
                <span class="min-w-0"><span class="block truncate text-sm font-bold">{{ $layoutUser->username ?? 'User' }}</span><span class="mt-1 block text-xs text-blue-200">{{ $roleTitle }}</span></span>
            </div>
        </div>
        <nav class="role-scroll flex-1 overflow-y-auto px-4 py-6">
            <div class="mb-6">
                <p class="mb-2 px-3 text-[11px] font-bold uppercase tracking-[.18em] text-blue-200">Dashboard</p>
                <div class="space-y-1">
                    @unless($isMonitoring)
                    <a href="{{ route('user.dashboard') }}" class="flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-semibold {{ request()->routeIs('user.dashboard') ? 'bg-white/15 ring-1 ring-white/10' : 'hover:bg-white/10' }}"><span class="flex h-8 w-8 items-center justify-center rounded-lg bg-white/10"><i class="fa-solid fa-house"></i></span>Beranda</a>
                    @endunless
                    @if($isMonitoring)
                    <a href="{{ route('monitoring.dashboard') }}" class="flex items-center gap-3 rounded-xl bg-white/15 px-3 py-2.5 text-sm font-semibold ring-1 ring-white/10"><span class="flex h-8 w-8 items-center justify-center rounded-lg bg-white/10"><i class="fa-solid fa-chart-line"></i></span>Dashboard Monitoring</a>
                    @elseif($isSurveyor && Route::has('surveyor.dashboard'))
                    <a href="{{ route('surveyor.dashboard') }}" class="flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-semibold {{ request()->routeIs('surveyor.dashboard') ? 'bg-white/15 ring-1 ring-white/10' : 'hover:bg-white/10' }}"><span class="flex h-8 w-8 items-center justify-center rounded-lg bg-white/10"><i class="fa-solid fa-chart-column"></i></span>Dashboard Monitoring</a>
                    @endif
                </div>
            </div>
            @if(!$isMonitoring && $profileComplete && !$surveyLocked)
            <div class="mb-6">
                <p class="mb-2 px-3 text-[11px] font-bold uppercase tracking-[.18em] text-blue-200">Survei</p>
                <a href="{{ route('survey.index') }}" class="flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-semibold {{ request()->routeIs('survey.*') ? 'bg-white/15 ring-1 ring-white/10' : 'hover:bg-white/10' }}"><span class="flex h-8 w-8 items-center justify-center rounded-lg bg-white/10"><i class="fa-solid fa-clipboard-list"></i></span>Isi Survei</a>
            </div>
            @endif
            @if($isMonitoring && $layoutProfile?->activity)
            <div class="rounded-2xl border border-white/15 bg-white/10 p-4"><p class="text-[10px] font-bold uppercase tracking-wider text-blue-200">Activity</p><p class="mt-2 text-sm font-semibold leading-5">{{ $layoutProfile->activity->name }}</p></div>
            @elseif($isSurveyor || !$profileComplete)
            <div class="rounded-2xl border border-white/15 bg-white/10 p-4"><p class="text-xs leading-5 text-blue-100">{{ $isSurveyor ? 'Mode simulasi pengisian untuk memberikan contoh kepada responden.' : 'Lengkapi profil untuk membuka akses pengisian survei.' }}</p></div>
            @endif
        </nav>
    </aside>

    <div class="flex min-h-screen min-w-0 flex-1 flex-col lg:pl-[270px]">
        <header class="sticky top-0 z-30 flex h-20 items-center gap-4 border-b border-slate-200 bg-white/95 px-5 shadow-sm backdrop-blur lg:px-8">
            <button type="button" class="text-xl text-slate-600 lg:hidden" @click="sidebarOpen=true"><i class="fa-solid fa-bars"></i></button>
            <div class="ml-auto flex items-center gap-3">
                <div class="hidden items-center gap-2 rounded-xl border border-slate-200 px-3 py-2 text-sm font-semibold text-slate-600 sm:flex"><i class="fa-regular fa-calendar text-blue-600"></i>{{ now()->translatedFormat('d M Y') }}</div>
                
                <div class="relative" @click.outside="profileOpen=false">
                    <button @click="profileOpen=!profileOpen" class="flex items-center gap-3 rounded-xl p-1.5 hover:bg-slate-50"><span class="flex h-10 w-10 items-center justify-center rounded-full bg-blue-100 font-bold text-blue-700">{{ strtoupper(substr($layoutUser->username ?? 'U',0,1)) }}</span><span class="hidden text-left md:block"><span class="block max-w-44 truncate text-sm font-bold">{{ $layoutUser->username ?? 'User' }}</span><span class="block text-xs text-slate-500">{{ $roleTitle }}</span></span><i class="fa-solid fa-chevron-down text-xs text-slate-400"></i></button>
                    <div x-cloak x-show="profileOpen" x-transition class="absolute right-0 mt-2 w-52 rounded-xl border border-slate-200 bg-white p-2 shadow-xl"><form method="POST" action="{{ route('logout') }}">@csrf<button class="flex w-full items-center gap-3 rounded-lg px-3 py-2 text-left text-sm text-red-600 hover:bg-red-50"><i class="fa-solid fa-right-from-bracket"></i>Keluar</button></form></div>
                </div>
            </div>
        </header>
        <main class="flex-1 p-4 sm:p-6 lg:p-8">
            @include('layouts.partials.global-alerts')
            <div id="pageContent">@yield('content')</div>
        </main>
        <footer class="border-t border-slate-200 bg-white px-5 py-4 text-center text-xs text-slate-500">&copy; {{ date('Y') }} PT Sucofindo - SBU Sertifikasi dan Eco-Framework. All Rights Reserved.</footer>
    </div>
</div>
@stack('modals')
@stack('templates')
<script src="{{ asset('js/global-alerts.js') }}?v=20260806-4"></script>
<script src="{{ asset('js/active-tab-persistence.js') }}?v=20260806-1"></script>
@stack('scripts')
</body>
</html>
