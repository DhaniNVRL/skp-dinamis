<template id="options-form">

    <!-- TABS -->
    <div class="flex border-b">
        <button type="button" class="tab-btn flex-1 py-2 border-b-2 border-blue-600 text-blue-600" data-tab="manual">
            Manual
        </button>
        <button type="button" class="tab-btn flex-1 py-2" data-tab="excel">
            Excel
        </button>
    </div>

    <!-- CONTENT -->
    <div class="p-4">

        <!-- MANUAL FORM -->
        <div data-content="manual">
            <form id="manualForm" method="POST" action="{{ route('options.store') }}">
                @csrf

                <input type="hidden" name="id_groups" value="{{ $groups->id ?? '' }}">
                <input type="hidden" name="question_id" value="">

                <div id="answerRows">

                    <div class="row question-wrapper flex flex-col gap-2 mb-2">
                        <div class="option-item flex items-center gap-2">
                            <input type="text" name="no[]" placeholder="Option Text" class="border p-2 w-full" validate-required>
                            <input type="text" name="answer_text[]" placeholder="Option Text" class="border p-2 w-full" required>

                            <!-- Pilihan apakah ada jawaban turunan -->
                            <select name="has_child[]" class="border p-2">
                                <option value="0">No Child</option>
                                <option value="1">Has Child</option>
                            </select>

                            <button type="button" class="remove text-red-600 font-bold">X</button>
                        </div>
                    </div>

                </div>

                <button type="button" id="addAnswerRow" class="text-blue-600 mb-3">
                    + Add Row
                </button>

                <div class="text-right">
                    <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded">
                        Save
                    </button>
                </div>
            </form>
        </div>

        <!-- EXCEL FORM -->
        <div data-content="excel" class="hidden">
            <form method="POST" enctype="multipart/form-data" action="{{ route('options.store') }}">
                @csrf

                <input type="file" name="file" class="border p-2 w-full mb-3" required>

                <div class="text-right">
                    <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded">
                        Import
                    </button>
                </div>
            </form>
        </div>

    </div>
</template>
