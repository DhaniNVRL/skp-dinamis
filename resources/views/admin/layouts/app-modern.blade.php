<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard')</title>
    <link rel="icon" type="image/png" href="{{ asset('images/logo-sucofindo.png') }}?v=20260820-sucofindo-2">
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
    <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.6/Sortable.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <style>
        [x-cloak]{display:none!important}.modern-scroll::-webkit-scrollbar{width:6px}.modern-scroll::-webkit-scrollbar-thumb{background:#94a3b8;border-radius:999px}
    </style>
    @stack('styles')
</head>
<body class="min-h-screen bg-slate-50 text-slate-900" x-data="{ sidebarOpen: false, profileOpen: false }">
<div class="min-h-screen lg:flex">
    <div x-cloak x-show="sidebarOpen" x-transition.opacity class="fixed inset-0 z-40 bg-slate-950/50 lg:hidden" @click="sidebarOpen=false"></div>
    <aside class="fixed inset-y-0 left-0 z-50 flex w-[270px] -translate-x-full flex-col bg-gradient-to-b from-blue-900 via-blue-800 to-blue-950 text-white transition-transform duration-200 lg:translate-x-0" :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'">
        <div class="flex h-20 items-center border-b border-white/10 px-6">
            <img src="{{ asset('images/logo4.png') }}" alt="" class="max-h-11 max-w-[205px] object-contain brightness-0 invert">
        </div>
        @php
            $sidebarSections = [
                'Dashboard' => [
                    ['label'=>'Beranda','icon'=>'fa-house','route'=>'admin.dashboard'],
                ],
                'Setting' => [
                    ['label'=>'Setting','icon'=>'fa-gear','href'=>'#'],
                    ['label'=>'Raw Data','icon'=>'fa-file-chart-column','route'=>'admin.raw-data.index'],
                    ['label'=>'General Setting','icon'=>'fa-gears','route'=>'admin.activity'],
                ],
                'Master Data' => [
                    ['label'=>'Users','icon'=>'fa-users','route'=>'admin.datauser'],
                    ['label'=>'Roles','icon'=>'fa-bars-staggered','route'=>'admin.roles'],
                    ['label'=>'Activity','icon'=>'fa-bars-staggered','route'=>'admin.masterdata.activity'],
                    ['label'=>'Group','icon'=>'fa-bars-staggered','route'=>'admin.masterdata.groups'],
                    ['label'=>'Unit','icon'=>'fa-bars-staggered','route'=>'admin.masterdata.unit'],
                    ['label'=>'Sub Unit','icon'=>'fa-bars-staggered','href'=>'#'],
                    ['label'=>'Form','icon'=>'fa-bars-staggered','route'=>'forms.masterdata'],
                    ['label'=>'Form Type','icon'=>'fa-bars-staggered','route'=>'admin.formtype'],
                    ['label'=>'Question','icon'=>'fa-bars-staggered','route'=>'question.masterdata'],
                    ['label'=>'Question Types','icon'=>'fa-bars-staggered','route'=>'admin.questtype'],
                ],
            ];
        @endphp
        <nav class="modern-scroll flex-1 overflow-y-auto px-4 py-6">
            @foreach($sidebarSections as $section => $items)
                <div class="mb-6">
                    <p class="mb-2 px-3 text-[11px] font-bold uppercase tracking-[.18em] text-blue-200">{{ $section }}</p>
                    <div class="space-y-1">
                        @foreach($items as $item)
                            @php
                                $hasRoute = isset($item['route']) && Route::has($item['route']);
                                $url = $hasRoute ? route($item['route']) : ($item['href'] ?? '#');
                                $active = $hasRoute && request()->routeIs($item['route']);
                            @endphp
                            <a href="{{ $url }}" class="flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-semibold transition {{ $active ? 'bg-white/15 text-white shadow-sm ring-1 ring-white/10' : 'text-blue-50 hover:bg-white/10' }}">
                                <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg {{ $active ? 'bg-white/15' : 'bg-white/10' }}"><i class="fa-solid {{ $item['icon'] }}"></i></span>
                                <span>{{ $item['label'] }}</span>
                            </a>
                        @endforeach
                    </div>
                </div>
            @endforeach
        </nav>
    </aside>

    <div class="min-w-0 flex-1 lg:pl-[270px]">
        <header class="sticky top-0 z-30 flex h-20 items-center gap-4 border-b border-slate-200 bg-white/95 px-5 shadow-sm backdrop-blur lg:px-8">
            <button type="button" class="text-xl text-slate-600 lg:hidden" @click="sidebarOpen=true"><i class="fa-solid fa-bars"></i></button>
            <div class="ml-auto flex items-center gap-3">
                <div class="hidden items-center gap-2 rounded-xl border border-slate-200 px-3 py-2 text-sm font-semibold text-slate-600 sm:flex"><i class="fa-regular fa-calendar text-blue-600"></i>{{ now()->translatedFormat('d M Y') }}</div>
                
                <div class="relative" @click.outside="profileOpen=false">
                    <button type="button" @click="profileOpen=!profileOpen" class="flex items-center gap-3 rounded-xl p-1.5 hover:bg-slate-50">
                        <span class="flex h-10 w-10 items-center justify-center rounded-full bg-blue-100 font-bold text-blue-700">{{ strtoupper(substr(auth()->user()->username ?? 'A',0,1)) }}</span>
                        <span class="hidden text-left md:block"><span class="block text-sm font-bold">{{ auth()->user()->username ?? 'Administrator' }}</span><span class="block text-xs text-slate-500">Administrator</span></span>
                        <i class="fa-solid fa-chevron-down text-xs text-slate-400"></i>
                    </button>
                    <div x-cloak x-show="profileOpen" x-transition class="absolute right-0 mt-2 w-52 rounded-xl border border-slate-200 bg-white p-2 shadow-xl">
                        <form method="POST" action="{{ route('logout') }}">@csrf<button class="flex w-full items-center gap-3 rounded-lg px-3 py-2 text-left text-sm text-red-600 hover:bg-red-50"><i class="fa-solid fa-right-from-bracket"></i>Keluar</button></form>
                    </div>
                </div>
            </div>
        </header>
        <main class="p-5 lg:p-8">
            @include('layouts.partials.global-alerts')
            <div id="pageContent">@yield('content')</div>
        </main>
    </div>
</div>
<script>
document.addEventListener('keydown', function(e){ if((e.ctrlKey||e.metaKey)&&e.key.toLowerCase()==='k'){e.preventDefault();document.getElementById('modernGlobalSearch')?.focus();}});
</script>
@stack('modals')
@stack('templates')
<script src="{{ asset('js/global-alerts.js') }}?v=20260806-4"></script>
<script src="{{ asset('js/active-tab-persistence.js') }}?v=20260806-1"></script>
<script src="{{ asset('js/dashboard-monitoring.js') }}"></script>
@stack('scripts')
</body>
</html>



