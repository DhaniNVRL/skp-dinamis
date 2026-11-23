<div
    id="hideShowPage"
    data-toggle-url="{{ route('subunit-question.toggle') }}"
    data-show-all-url="{{ route('subunit-question.show-all') }}"
    data-form-toggle-url="{{ route('unit-form-visibility.toggle') }}"
    data-show-all-unit-url="{{ route('unit-form-visibility.toggle-all') }}"
    class="space-y-6"
>
    <input
        id="hideShowCsrfToken"
        type="hidden"
        value="{{ csrf_token() }}"
    >

    {{-- HEADER --}}
    <div class="rounded-xl border border-blue-200 bg-blue-50 p-5">
        <div class="flex items-start gap-4">
            <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-full bg-blue-100 text-blue-600">
                <i class="fa-solid fa-eye"></i>
            </div>

            <div>
                <h2 class="text-lg font-semibold text-gray-800">
                    Hide and Show Form dan Pertanyaan
                </h2>

                <p class="mt-1 text-sm text-gray-600">
                    Atur form untuk Unit ini dan pertanyaan yang ditampilkan pada setiap Sub Unit.
                    Form yang disembunyikan otomatis dilewati oleh responden.
                    Seluruh Sub Unit langsung ditampilkan tanpa perlu memilih
                    Sub Unit terlebih dahulu.
                </p>
            </div>
        </div>
    </div>

    {{-- RINGKASAN HIDE AND SHOW --}}
    <div
        data-hide-show-summary
        class="rounded-xl border border-yellow-300 bg-yellow-50 px-4 py-3 text-sm text-amber-800 shadow-sm"
    >
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div class="space-y-1 leading-relaxed">
                <p>
                    Jumlah Pertanyaan Keseluruhan:
                    <span data-hide-show-total class="font-bold">{{ $questionVisibilitySummary['total'] ?? 0 }}</span>
                </p>
                <p>
                    Jumlah Show:
                    <span data-hide-show-shown class="font-bold text-green-700">{{ $questionVisibilitySummary['shown'] ?? 0 }}</span>
                </p>
                <p>
                    Jumlah Hide:
                    <span data-hide-show-hidden class="font-bold text-red-600">{{ $questionVisibilitySummary['hidden'] ?? 0 }}</span>
                </p>
            </div>

            <div class="flex items-center justify-between gap-3 rounded-lg border border-yellow-300 bg-white px-3 py-2 text-gray-700">
                <div>
                    <div class="text-xs font-semibold uppercase tracking-wide text-gray-500">
                        Tampilkan Semua
                    </div>
                    <div
                        data-show-all-unit-status
                        class="text-xs font-medium {{ ($allFormsAndQuestionsVisible ?? false) ? 'text-green-600' : 'text-gray-500' }}"
                    >
                        {{ ($allFormsAndQuestionsVisible ?? false) ? 'Semua form dan pertanyaan ditampilkan' : 'Aktifkan semua form dan pertanyaan' }}
                    </div>
                </div>

                <button
                    type="button"
                    data-show-all-unit
                    data-unit-id="{{ $units->id }}"
                    data-active="{{ ($allFormsAndQuestionsVisible ?? false) ? '1' : '0' }}"
                    aria-pressed="{{ ($allFormsAndQuestionsVisible ?? false) ? 'true' : 'false' }}"
                    @disabled($forms->isEmpty() || $allSubunits->isEmpty())
                    class="relative inline-flex h-7 w-12 shrink-0 items-center rounded-full transition {{ ($allFormsAndQuestionsVisible ?? false) ? 'bg-green-500' : 'bg-gray-300' }} disabled:cursor-not-allowed disabled:opacity-50"
                >
                    <span
                        data-show-all-unit-knob
                        class="inline-block h-5 w-5 rounded-full bg-white shadow transition-transform {{ ($allFormsAndQuestionsVisible ?? false) ? 'translate-x-6' : 'translate-x-1' }}"
                    ></span>
                    <span class="sr-only">Tampilkan seluruh form dan pertanyaan</span>
                </button>
            </div>
        </div>
    </div>

    {{-- EMPTY SUB UNIT --}}
    @if ($allSubunits->isEmpty())
        <div class="rounded-xl border border-dashed border-gray-300 bg-gray-50 px-6 py-14 text-center">
            <i class="fa-solid fa-building-circle-xmark text-4xl text-gray-300"></i>

            <h3 class="mt-4 font-semibold text-gray-700">
                Belum ada Sub Unit
            </h3>

            <p class="mt-1 text-sm text-gray-500">
                Tambahkan Sub Unit terlebih dahulu sebelum mengatur pertanyaan.
            </p>
        </div>
    @elseif ($forms->isEmpty())
        <div class="rounded-xl border border-dashed border-gray-300 bg-gray-50 px-6 py-14 text-center">
            <i class="fa-solid fa-clipboard-question text-4xl text-gray-300"></i>

            <h3 class="mt-4 font-semibold text-gray-700">
                Form belum tersedia
            </h3>

            <p class="mt-1 text-sm text-gray-500">
                Tidak ada form pada group milik unit ini.
            </p>
        </div>
    @else
        @foreach ($forms as $form)
            @include(
                'admin.subunit.hide-and-show.partials.form-card',
                [
                    'form' => $form,
                    'allSubunits' => $allSubunits,
                    'activeMapSubUnit' => $activeMapSubUnit,
                    'unitName' => $units->name,
                    'unitId' => $units->id,
                    'isFormVisible' => $unitFormVisibilityMap[(int) $form->id] ?? false,
                ]
            )
        @endforeach
    @endif

    {{-- NOTIFICATION --}}
    <div
        id="hideShowNotification"
        role="status"
        aria-live="polite"
        class="fixed bottom-16 right-4 z-[80] hidden w-[calc(100vw-2rem)] max-w-sm rounded-xl border p-4 shadow-2xl sm:right-6"
    >
        <div class="flex items-start gap-3">
            <div id="hideShowNotificationIcon" class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full">
                <i class="fa-solid fa-circle-check"></i>
            </div>
            <div class="min-w-0 flex-1">
                <p id="hideShowNotificationTitle" class="font-semibold"></p>
                <p id="hideShowNotificationMessage" class="mt-1 break-words text-sm"></p>
            </div>
            <button type="button" data-hide-show-notification-close class="shrink-0 opacity-70 transition hover:opacity-100" aria-label="Tutup notifikasi">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>
    </div>
</div>
