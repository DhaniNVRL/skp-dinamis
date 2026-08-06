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
                ]
            )
        @endforeach
    @endif

    {{-- NOTIFICATION --}}
    <div
        id="hideShowNotification"
        class="pointer-events-none fixed bottom-6 right-6 z-[100] hidden max-w-sm rounded-lg px-4 py-3 text-sm font-medium text-white shadow-xl"
    ></div>
</div>