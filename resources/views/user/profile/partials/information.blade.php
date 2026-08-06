<div class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm">
    <div class="border-b border-gray-200 px-6 py-5">
        <h2 class="text-lg font-semibold text-gray-800">
            Informasi Responden
        </h2>

        <p class="mt-1 text-sm text-gray-500">
            Informasi aktivitas, bidang kerja, dan unit responden.
        </p>
    </div>

    <div class="divide-y divide-gray-100">
        {{-- ACTIVITY --}}
        <div class="flex items-start gap-4 px-6 py-5">
            <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-lg bg-purple-100 text-purple-600">
                <i class="fa-solid fa-briefcase"></i>
            </div>

            <div>
                <div class="text-sm text-gray-500">
                    Aktivitas
                </div>

                <div class="mt-1 font-semibold text-gray-800">
                    {{ $profile->activity?->name ?? '-' }}
                </div>
            </div>
        </div>

        {{-- GROUP --}}
        <div class="flex items-start gap-4 px-6 py-5">
            <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-lg bg-indigo-100 text-indigo-600">
                <i class="fa-solid fa-layer-group"></i>
            </div>

            <div>
                <div class="text-sm text-gray-500">
                    {{ $completeProfile?->group_question
                        ?? 'Bidang Kerja / Group' }}
                </div>

                <div class="mt-1 font-semibold text-gray-800">
                    {{ $profile->group?->name ?? '-' }}
                </div>
            </div>
        </div>

        {{-- UNIT --}}
        <div class="flex items-start gap-4 px-6 py-5">
            <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-lg bg-blue-100 text-blue-600">
                <i class="fa-solid fa-building"></i>
            </div>

            <div>
                <div class="text-sm text-gray-500">
                    {{ $completeProfile?->unit_question
                        ?? 'Unit / Jabatan' }}
                </div>

                <div class="mt-1 font-semibold text-gray-800">
                    {{ $profile->unit?->name ?? '-' }}
                </div>
            </div>
        </div>
    </div>
</div>