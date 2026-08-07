@extends('admin.layouts.app')

@section('title', 'Monitoring Survey')

@section('content')
<div id="monitoringDashboard" class="mx-auto max-w-[1600px] space-y-6">
    <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm">
        <div class="flex flex-col gap-5 lg:flex-row lg:items-center lg:justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Monitoring Kegiatan Survey</h1>
                <p class="mt-1 text-sm text-gray-500">Pantau status pengisian survey seluruh responden.</p>
            </div>

            <div class="flex items-center gap-3 rounded-xl bg-indigo-50 px-5 py-4">
                <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-indigo-100 text-indigo-600">
                    <i class="fa-solid fa-user-shield"></i>
                </div>
                <div>
                    <p class="text-xs font-medium uppercase tracking-wide text-indigo-500">Login Sebagai</p>
                    <p class="font-semibold text-indigo-700">{{ auth()->user()->role?->name ?? 'Administrator' }}</p>
                </div>
            </div>
        </div>
    </div>

    @include('admin.dashboard.partials.filters')
    @include('admin.dashboard.partials.cards')

    <div class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm">
        <div class="flex flex-col gap-2 border-b border-gray-200 bg-gray-50 px-5 py-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="font-semibold text-gray-900">Daftar Responden</h2>
                <p class="text-sm text-gray-500">Klik detail untuk melihat profil dan jawaban responden.</p>
            </div>
            <span class="text-sm text-gray-500">{{ $respondents->total() }} responden ditemukan</span>
        </div>

        @include('admin.dashboard.partials.respondent-table')

        <div class="border-t border-gray-200 px-5 py-4">
            {{ $respondents->links() }}
        </div>
    </div>
</div>

@endsection

@push('scripts')
    <script src="{{ asset('js/dashboard-monitoring.js') }}"></script>
@endpush
