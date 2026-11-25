@extends('admin.layouts.app-modern')

@section('title', 'Download Raw Data')

@section('content')
<div class="mx-auto max-w-5xl space-y-6">
    <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm">
        <div class="flex items-start justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Download Raw Data</h1>
                <p class="mt-1 text-sm text-gray-500">Export jawaban survey menjadi satu baris per responden dengan kolom pertanyaan dinamis.</p>
            </div>
            <div class="rounded-xl bg-blue-50 p-3 text-blue-600"><i class="fa-solid fa-file-excel text-2xl"></i></div>
        </div>
    </div>

    @if ($errors->any())
        <div data-alert class="flex items-start justify-between rounded-xl border border-red-200 bg-red-50 p-4 text-red-700">
            <div>
                <p class="font-semibold">Raw Data belum dapat diunduh:</p>
                <ul class="mt-2 list-disc space-y-1 pl-5 text-sm">
                    @foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach
                </ul>
            </div>
            <button type="button" data-alert-close class="ml-4 text-red-500 hover:text-red-700"><i class="fa-solid fa-xmark"></i></button>
        </div>
    @endif

    <form method="GET" action="{{ route('admin.raw-data.download') }}" class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm">
        <div class="border-b border-gray-200 bg-gray-50 px-6 py-4">
            <h2 class="font-semibold text-gray-900">Pilih Data Survey</h2>
            <p class="mt-1 text-sm text-gray-500">Pilih activity. Setiap group akan tersedia dalam sheet terpisah pada satu file Excel.</p>
        </div>

        <div class="grid gap-5 p-6 md:grid-cols-2">
            <div>
                <label for="rawActivity" class="mb-2 block text-sm font-semibold text-gray-700">Activity</label>
                <select id="rawActivity" name="activity_id" required class="w-full rounded-lg border border-blue-300 bg-white px-4 py-3 text-gray-800 outline-none transition hover:bg-gray-50 focus:border-blue-500 focus:ring-2 focus:ring-blue-100">
                    <option value="">Pilih Activity</option>
                    @foreach ($activities as $activity)
                        <option value="{{ $activity->id }}" @selected((string) old('activity_id', request('activity_id')) === (string) $activity->id)>{{ $activity->name }}</option>
                    @endforeach
                </select>
            </div>

        </div>

        <div class="border-t border-gray-200 bg-gray-50 px-6 py-4">
            <div class="mb-4 rounded-lg border border-blue-200 bg-blue-50 px-4 py-3 text-sm text-blue-700">
                <i class="fa-solid fa-circle-info mr-2"></i>
                Format mengikuti contoh: identitas responden, unit yang dinilai, lalu jawaban dipivot berdasarkan nomor pertanyaan, sub-unit, dan kompetitor.
            </div>
            <div class="flex justify-end">
                <button type="submit" class="inline-flex items-center gap-2 rounded-lg bg-emerald-600 px-5 py-3 font-semibold text-white transition hover:bg-emerald-700">
                    <i class="fa-solid fa-download"></i> Download Raw Data Excel
                </button>
            </div>
        </div>
    </form>
</div>
@endsection

