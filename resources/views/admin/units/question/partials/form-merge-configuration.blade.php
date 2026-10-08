@php
    $orderedMergeForms = $forms->sortBy([
        ['no_urut', 'asc'],
        ['id', 'asc'],
    ])->values();
@endphp

<details id="formMergeConfiguration" class="mb-6 overflow-hidden rounded-xl border border-violet-200 bg-white shadow-sm">
    <summary class="flex cursor-pointer list-none items-center justify-between gap-4 bg-violet-50 px-5 py-4">
        <div class="flex items-center gap-3">
            <span class="inline-flex h-10 w-10 items-center justify-center rounded-lg bg-violet-100 text-violet-600">
                <i class="fa-solid fa-layer-group"></i>
            </span>
            <div>
                <h3 class="font-semibold text-gray-900">Konfigurasi Penggabungan Form</h3>
                <p class="text-sm text-gray-600">Tampilkan dan simpan beberapa form sebagai satu tahap pengisian responden.</p>
            </div>
        </div>
        <i class="fa-solid fa-chevron-down text-violet-600"></i>
    </summary>

    <form method="POST" action="{{ route('forms.merge-configuration.update') }}" class="space-y-5 p-5">
        @csrf
        @method('PUT')
        <input type="hidden" name="group_id" value="{{ $groups->id }}">

        <div class="rounded-lg border border-violet-200 bg-violet-50 p-3 text-sm text-violet-700">
            Beberapa form dapat digabung menjadi satu kelompok. Pilih salah satu form sebelumnya; jika form tersebut sudah tergabung, sistem otomatis memakai form utama kelompoknya. Semua anggota akan tampil dalam satu halaman dan disimpan bersamaan. Pertanyaan serta jawaban yang sudah ada tidak dihapus.
        </div>

        <div class="space-y-3">
            @forelse ($orderedMergeForms as $mergeForm)
                @php
                    $mergeCandidates = $orderedMergeForms->filter(
                        fn ($candidate) => (int) $candidate->no_urut < (int) $mergeForm->no_urut
                    );
                    $selectedParentId = old(
                        'merge_parent_ids.'.$mergeForm->id,
                        $mergeForm->merge_parent_id
                    );
                @endphp
                <div class="grid gap-3 rounded-lg border border-gray-200 p-4 lg:grid-cols-[minmax(0,1fr)_minmax(280px,420px)] lg:items-center">
                    <div class="min-w-0">
                        <div class="flex flex-wrap items-center gap-2">
                            <span class="rounded bg-gray-100 px-2 py-1 text-xs font-semibold text-gray-600">ID={{ $mergeForm->id }}</span>
                            <span class="rounded bg-violet-100 px-2 py-1 text-xs font-semibold text-violet-700">{{ $mergeForm->no_urut }}</span>
                            <span class="font-semibold text-gray-900">{{ $mergeForm->name }}</span>
                        </div>
                        <p class="mt-1 text-xs text-gray-500">{{ $mergeForm->formtype?->name }} — {{ $mergeForm->formtype?->description }}</p>
                    </div>

                    <div>
                        <label for="merge-parent-{{ $mergeForm->id }}" class="mb-1 block text-xs font-semibold uppercase tracking-wide text-gray-500">Status tampilan</label>
                        <select id="merge-parent-{{ $mergeForm->id }}" name="merge_parent_ids[{{ $mergeForm->id }}]" class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2.5 text-sm focus:border-violet-500 focus:ring-violet-500">
                            <option value="">Form utama / mandiri</option>
                            @foreach ($mergeCandidates as $candidate)
                                <option value="{{ $candidate->id }}" @selected((string) $selectedParentId === (string) $candidate->id)>
                                    Gabung dengan {{ $candidate->no_urut }} — {{ $candidate->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('merge_parent_ids.'.$mergeForm->id)
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            @empty
                <p class="rounded-lg border border-dashed border-gray-300 p-4 text-center text-sm text-gray-500">Belum ada form yang dapat dikonfigurasi.</p>
            @endforelse
        </div>

        @error('merge_parent_ids')
            <p class="text-sm text-red-600">{{ $message }}</p>
        @enderror

        <div class="flex justify-end">
            <button type="submit" class="inline-flex items-center gap-2 rounded-lg bg-violet-600 px-4 py-2.5 font-semibold text-white hover:bg-violet-700">
                <i class="fa-solid fa-floppy-disk"></i>
                Simpan Penggabungan
            </button>
        </div>
    </form>
</details>
