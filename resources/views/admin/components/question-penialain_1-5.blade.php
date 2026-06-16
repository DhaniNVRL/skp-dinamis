<template id="penilaian_1-5">

    <!-- TABS -->
    <div class="flex border-b">
        <button
            type="button"
            class="tab-btn flex-1 py-2 border-b-2 border-blue-600 text-blue-600"
            data-tab="manual">
            Manual
        </button>

        <button
            type="button"
            class="tab-btn flex-1 py-2"
            data-tab="excel">
            Excel
        </button>
    </div>

    <!-- CONTENT -->
    <div class="p-4">

        <!-- MANUAL -->
        <div data-content="manual">
            <form id="manualForm" method="POST" action="{{ route('question.store') }}">
                @csrf
                <input type="hidden" name="id_groups" id="id_groups_input" value="{{ $groups->id ?? '' }}">
                <input type="hidden" name="form_id" id="form_id_input" value="">

                <div id="rows"> <!-- ID diperbaiki -->
                    <div class="row flex gap-2 mb-2" id="answerRows">
                        <div class="w-full" style="width:30%;">
                            <input type="text" name="no_header[]" placeholder="Header Number" class="border p-2 w-full" validate-required>
                        </div>
                        <div class="w-full" style="width:30%;">
                            <input type="text" name="no[]" placeholder="No" class="border p-2 w-full" required>
                        </div>
                        <div class="w-full">
                            <textarea name="name[]" placeholder="Name Form" class="border p-2 w-full" required></textarea>
                        </div>
                        <div class="w-full">
                                <!-- Pilihan apakah ada jawaban turunan -->
                                <select name="formtype[]" class="border p-2">
                                    <option value="1">Title</option>
                                    <option value="2">Question</option>
                                    <option value="3">Question 1</option>
                                </select>
                            </div>
                        <button type="button" class="remove text-red-600 font-bold">X</button>
                    </div>
                </div>

                <button type="button" id="addRow" class="text-blue-600 mb-3">+ Add Row</button> <!-- ID diperbaiki -->

                <div class="text-right">
                    <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded">Save</button>
                </div>
            </form>
        </div>

        <!-- EXCEL -->
        <div data-content="excel" class="hidden">
            <form
                id="excelForm"
                method="POST"
                enctype="multipart/form-data"
                action="{{ route('question.store') }}"
            >
                @csrf

                <input
                    type="file"
                    name="file"
                    class="border p-2 w-full mb-3"
                    required
                >

                <input
                    type="hidden"
                    name="id_groups"
                    value="{{ $groups->id }}"
                >

                <div class="text-right">
                    <a
                        href="{{ route('units.export') }}"
                        class="bg-blue-600 text-white px-4 py-2 me-5 rounded">
                        Download Excel Template
                    </a>

                    <button
                        type="submit"
                        class="bg-green-600 text-white px-4 py-2 rounded">
                        Import
                    </button>
                </div>
            </form>
        </div>

    </div>
</template>