<template id="keluhan-saran">

    <div class="flex border-b">
        <button type="button" class="tab-btn px-4 py-2 border-b-2 border-blue-600 text-blue-600" data-tab="manual">
            Manual
        </button>

        <button type="button" class="tab-btn px-4 py-2" data-tab="excel">
            Excel
        </button>
    </div>

    <div class="p-4">

        <!-- MANUAL -->
        <div data-content="manual">

            <form method="POST" action="{{ route('question.store') }}">
            @csrf

                <input type="hidden" name="group_id" value="{{ $groups->id }}">
                <input type="hidden" name="form_id" value="{{ $groups->form_id }}">

                <div data-section="manual-form">

                    <div class="manual-rows">

                        <!-- ROW TEMPLATE -->
                        <div class="row-item flex gap-2 mb-2">
                            <input type="text" name="no_header[]" placeholder="Header Number" class="border p-2 w-1/4">
                            <input type="text" name="no[]" placeholder="No" class="border p-2 w-1/4">
                            <textarea name="name[]" placeholder="Pertanyaan" class="border p-2 w-1/4"></textarea>

                            <select name="formtype[]" class="border p-2 w-1/4">
                               <option value="2">Question</option>
                                <option value="1">Title</option>
                            </select>
                            <button type="button" class="remove-row text-red-600 px-2">
                                ✕
                            </button>
                        </div>

                    </div>

                    <button type="button" class="add-row-btn bg-gray-500 text-white px-3 py-1 mt-2">
                        + Add Row
                    </button>

                </div>

                <button type="submit" class="bg-blue-600 text-white px-4 py-2 mt-3">
                    Save
                </button>
            </form>

        </div>

        <!-- EXCEL -->
        <div data-content="excel" class="hidden">

            <form method="POST"
                  action="{{ route('question.import') }}"
                  enctype="multipart/form-data">
                    @csrf
              

                <input type="file" name="file" class="border p-2 w-full mb-3" required>

                <input type="hidden" name="group_id" value="{{ $groups->id }}">
                <input type="hidden" name="form_id" id="form_id_input">
                
                <a href="{{ route('question.export') }}" class="bg-blue-600 text-white px-4 py-2 rounded">
                        Download Excel Question Template 
                </a>
                <button type="submit" class="bg-green-600 text-white px-4 py-2">
                    Import
                </button>

            </form>

        </div>

    </div>

</template>