<div
    id="hideShowPage"
    data-toggle-url="{{ route('subunit-question.toggle') }}"
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
                    Hide and Show Pertanyaan
                </h2>

                <p class="mt-1 text-sm text-gray-600">
                    Atur pertanyaan yang ditampilkan pada setiap Sub Unit.
                    Seluruh Sub Unit langsung ditampilkan tanpa perlu memilih
                    Sub Unit terlebih dahulu.
                </p>
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
