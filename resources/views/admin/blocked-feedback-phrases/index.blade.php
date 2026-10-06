@extends('admin.layouts.app-modern')

@section('title', 'Kata Terlarang')

@section('content')
<div class="space-y-6">
    <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
        <h1 class="text-2xl font-bold text-slate-900">Kata/Kalimat Tidak bisa di input</h1>
        <p class="mt-1 text-sm text-slate-500">Kelola kata atau kalimat yang tidak boleh dimasukkan pada jawaban Keluhan dan Saran.</p>
    </div>

    <div class="grid gap-6 xl:grid-cols-[380px_minmax(0,1fr)]">
        <form method="POST" action="{{ route('admin.blocked-feedback-phrases.store') }}" class="h-fit rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
            @csrf
            <h2 class="text-lg font-bold text-slate-900">Tambah Data</h2>
            <label for="phrase" class="mt-5 block text-sm font-semibold text-slate-700">Kata atau kalimat</label>
            <textarea id="phrase" name="phrase" rows="4" required maxlength="500" placeholder="Contoh: tidak ada keluhan" class="mt-2 w-full rounded-xl border border-slate-300 px-4 py-3 text-sm focus:border-blue-500 focus:ring-blue-500">{{ old('phrase') }}</textarea>
            @error('phrase')<p class="mt-2 text-sm text-red-600">{{ $message }}</p>@enderror

            <label class="mt-4 flex items-center gap-3 text-sm font-semibold text-slate-700">
                <input type="hidden" name="is_active" value="0">
                <input type="checkbox" name="is_active" value="1" @checked(old('is_active', true)) class="rounded border-slate-300 text-blue-600 focus:ring-blue-500">
                Aktifkan larangan
            </label>

            <button type="submit" class="mt-5 inline-flex w-full items-center justify-center gap-2 rounded-xl bg-blue-600 px-4 py-3 text-sm font-bold text-white hover:bg-blue-700">
                <i class="fa-solid fa-plus"></i> Tambah
            </button>
        </form>

        <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
            <div class="border-b border-slate-200 px-6 py-5">
                <h2 class="text-lg font-bold text-slate-900">Daftar Kata/Kalimat</h2>
                <p class="mt-1 text-sm text-slate-500">{{ $phrases->total() }} data tersedia.</p>
            </div>

            <div class="divide-y divide-slate-200">
                @forelse ($phrases as $item)
                    <div class="p-5" x-data="{ editing: false }">
                        <div x-show="!editing" class="flex items-start gap-4">
                            <div class="min-w-0 flex-1">
                                <p class="break-words font-semibold text-slate-800">{{ $item->phrase }}</p>
                                <span class="mt-2 inline-flex rounded-full px-2.5 py-1 text-xs font-bold {{ $item->is_active ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-100 text-slate-500' }}">
                                    {{ $item->is_active ? 'Aktif' : 'Nonaktif' }}
                                </span>
                            </div>
                            <button type="button" @click="editing = true" class="flex h-9 w-9 items-center justify-center rounded-lg bg-amber-50 text-amber-600 hover:bg-amber-100" title="Edit"><i class="fa-solid fa-pen"></i></button>
                            <form method="POST" action="{{ route('admin.blocked-feedback-phrases.destroy', $item) }}" onsubmit="return confirm('Hapus kata atau kalimat ini?')">
                                @csrf
                                @method('DELETE')
                                <button class="flex h-9 w-9 items-center justify-center rounded-lg bg-red-50 text-red-600 hover:bg-red-100" title="Hapus"><i class="fa-solid fa-trash"></i></button>
                            </form>
                        </div>

                        <form x-cloak x-show="editing" method="POST" action="{{ route('admin.blocked-feedback-phrases.update', $item) }}" class="space-y-3">
                            @csrf
                            @method('PUT')
                            <textarea name="phrase" rows="3" required maxlength="500" class="w-full rounded-xl border border-slate-300 px-4 py-3 text-sm focus:border-blue-500 focus:ring-blue-500">{{ $item->phrase }}</textarea>
                            <label class="flex items-center gap-3 text-sm font-semibold text-slate-700">
                                <input type="hidden" name="is_active" value="0">
                                <input type="checkbox" name="is_active" value="1" @checked($item->is_active) class="rounded border-slate-300 text-blue-600 focus:ring-blue-500">
                                Aktifkan larangan
                            </label>
                            <div class="flex justify-end gap-2">
                                <button type="button" @click="editing = false" class="rounded-lg border border-slate-300 px-4 py-2 text-sm font-semibold text-slate-600">Batal</button>
                                <button type="submit" class="rounded-lg bg-blue-600 px-4 py-2 text-sm font-bold text-white hover:bg-blue-700">Simpan</button>
                            </div>
                        </form>
                    </div>
                @empty
                    <div class="px-6 py-14 text-center text-sm text-slate-500">Belum ada kata atau kalimat terlarang.</div>
                @endforelse
            </div>

            @if ($phrases->hasPages())
                <div class="border-t border-slate-200 px-6 py-4">{{ $phrases->links() }}</div>
            @endif
        </div>
    </div>
</div>
@endsection
