<div
    id="questionPreviewPage"
    class="w-full"
>
    {{-- Penanda bahwa file berhasil dipanggil --}}
    <div class="mb-5 rounded-xl border border-indigo-200 bg-indigo-50 p-5">
        <div class="flex items-start gap-4">
            <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-full bg-indigo-100 text-indigo-600">
                <i class="fa-solid fa-list-check"></i>
            </div>

            <div>
                <h2 class="text-lg font-semibold text-gray-800">
                    Tampilan Pertanyaan
                </h2>

                <p class="mt-1 text-sm text-gray-600">
                    Preview pertanyaan berdasarkan pengaturan Hide and Show.
                </p>
            </div>
        </div>
    </div>

    @include('admin.subunit.show-question.index')
</div>