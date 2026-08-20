@extends('admin.layouts.app-modern')

@section('title', 'Dashboard Modern')

@section('content')
<div class="mx-auto max-w-[1600px] space-y-6">
    <section class="flex flex-col justify-between gap-4 lg:flex-row lg:items-end">
        <div>
            <p class="text-sm font-bold uppercase tracking-[.2em] text-blue-600">Monitoring Survey</p>
            <h1 class="mt-2 text-3xl font-black tracking-tight text-slate-900 lg:text-4xl">Dashboard</h1>
            <p class="mt-2 text-slate-500">Ringkasan aktivitas responden dan progres pengisian survey.</p>
        </div>
    </section>

    @include('admin.dashboard.partials.cards')

    <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
        <div class="flex flex-col gap-4 border-b border-slate-200 p-5 lg:flex-row lg:items-center lg:justify-between">
            <div><h2 class="text-xl font-black"><i class="fa-solid fa-list-ul mr-2 text-blue-600"></i>Daftar Responden</h2><p class="mt-1 text-sm text-slate-500">Pantau status dan progres pengisian survey.</p></div>
            @if(Route::has('admin.raw-data.index'))<a href="{{ route('admin.raw-data.index') }}" class="inline-flex items-center justify-center gap-2 rounded-xl bg-blue-600 px-4 py-2.5 text-sm font-bold text-white shadow hover:bg-blue-700"><i class="fa-solid fa-download"></i>Export</a>@endif
        </div>
        <div class="border-b border-slate-200 bg-slate-50/70 p-5">@include('admin.dashboard.partials.filters')</div>
        <div class="overflow-x-auto">@include('admin.dashboard.partials.respondent-table')</div>
        @if(method_exists($respondents, 'links'))<div class="border-t border-slate-200 px-5 py-4">{{ $respondents->withQueryString()->links() }}</div>@endif
    </section>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded',()=>{const quick=document.getElementById('modernGlobalSearch');const target=document.querySelector('input[name="search"], input[name="username"]');if(quick&&target){quick.value=target.value||'';quick.addEventListener('keydown',e=>{if(e.key==='Enter'){e.preventDefault();target.value=quick.value;target.form?.submit();}});}});
</script>
@endpush

