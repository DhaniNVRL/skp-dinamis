<section class="space-y-5">
    <div class="rounded-xl border border-blue-200 bg-blue-50 p-5">
        <h3 class="font-semibold text-blue-900">Kompetitor yang Dinilai per Unit</h3>
        <p class="mt-1 text-sm text-blue-700">
            Pertanyaan pembanding tetap bersifat global. Pengaturan ini hanya menentukan kompetitor yang tampil dan wajib dinilai oleh responden pada unit
            <strong>{{ $unit->name }}</strong>.
        </p>
    </div>

    <form method="POST" action="{{ route('subunits.competitor-visibility.update', $unit->id) }}" class="overflow-hidden rounded-xl border border-gray-200 bg-white">
        @csrf

        <div class="flex flex-col gap-4 border-b border-gray-200 p-5 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h4 class="font-semibold text-gray-900">Daftar Kompetitor</h4>
                <p class="mt-1 text-sm text-gray-500">
                    @if ($competitorVisibilityConfigured)
                        <span class="font-medium text-blue-700">Konfigurasi khusus aktif untuk unit ini.</span>
                    @else
                        <span class="font-medium text-emerald-700">Default: seluruh kompetitor ditampilkan.</span>
                    @endif
                </p>
            </div>

            @if ($allCompetitors->isNotEmpty())
                <div class="flex flex-wrap gap-2">
                    <button type="button" data-competitor-select-all class="rounded-lg border border-blue-200 bg-white px-3 py-2 text-sm font-medium text-blue-700 hover:bg-blue-50">
                        Pilih Semua
                    </button>
                    <button type="button" data-competitor-clear class="rounded-lg border border-gray-200 bg-white px-3 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">
                        Hapus Pilihan
                    </button>
                </div>
            @endif
        </div>

        @if ($allCompetitors->isEmpty())
            <div class="p-8 text-center text-sm text-gray-500">Belum ada kompetitor pada group unit ini.</div>
        @else
            <div data-competitor-list class="grid gap-3 p-5 md:grid-cols-2 xl:grid-cols-3">
                @foreach ($allCompetitors as $competitor)
                    <label class="flex cursor-pointer items-center gap-3 rounded-xl border border-gray-200 p-4 transition hover:border-blue-300 hover:bg-blue-50/50">
                        <input
                            type="checkbox"
                            name="competitor_ids[]"
                            value="{{ $competitor->id }}"
                            @checked(in_array((int) $competitor->id, $selectedCompetitorIds, true))
                            class="h-4 w-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500"
                        >
                        <span class="font-medium text-gray-800">{{ $competitor->name }}</span>
                    </label>
                @endforeach
            </div>

            @error('competitor_ids')
                <p class="px-5 pb-3 text-sm text-red-600">{{ $message }}</p>
            @enderror

            <div class="flex flex-col gap-3 border-t border-gray-200 bg-gray-50 p-5 sm:flex-row sm:items-center sm:justify-between">
                <p class="text-sm text-gray-500">Jika semua pilihan dikosongkan, unit ini tidak akan menilai kompetitor pada form pembanding.</p>
                <button type="submit" class="inline-flex items-center justify-center gap-2 rounded-lg bg-blue-600 px-5 py-2.5 font-semibold text-white hover:bg-blue-700">
                    <i class="fa-solid fa-floppy-disk"></i>
                    Simpan Pengaturan
                </button>
            </div>
        @endif
    </form>
</section>

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const list = document.querySelector('[data-competitor-list]');
            if (!list) return;

            const inputs = () => Array.from(list.querySelectorAll('input[type="checkbox"]'));

            document.querySelector('[data-competitor-select-all]')?.addEventListener('click', () => {
                inputs().forEach((input) => { input.checked = true; });
            });

            document.querySelector('[data-competitor-clear]')?.addEventListener('click', () => {
                inputs().forEach((input) => { input.checked = false; });
            });
        });
    </script>
@endpush
