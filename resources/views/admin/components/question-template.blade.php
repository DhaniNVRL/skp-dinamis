<template id="question-form">
    <!-- TABS -->
    <div class="flex border-b">
        <button class="tab-btn flex-1 py-2 border-b-2 border-blue-600 text-blue-600" data-tab="manual">
            Manual
        </button>
        <button class="tab-btn flex-1 py-2" data-tab="excel">
            Excel
        </button>
    </div>

    <!-- CONTENT -->
    <div class="p-4">

        <!-- MANUAL FORM -->
        <div data-content="manual">
            <form id="manualForm" method="POST" action="{{ route('question.store') }}">
                @csrf
                <input type="hidden" name="id_groups" id="id_groups_input" value="{{ $groups->id ?? '' }}">
                <input type="hidden" name="form_id" id="form_id_input" value="">

                <div id="rows">
                    <div class="row flex gap-2 mb-2">
                        <div class="w-full">
                            <input type="text" name="no[]" placeholder="No" class="border p-2 w-full" required>
                        </div>
                        <div class="w-full">
                            <input type="text" name="name[]" placeholder="Name Form" class="border p-2 w-full" required>
                        </div>
                        <div class="w-full">
                            <select name="formtype[]" required class="validate-required border rounded px-4 py-2 w-full">
                                <option value="">Choose Form Type</option>
                                @foreach($questionypes as $questionype)
                                    <option value="{{ $questionype->id }}">{{ $questionype->name }} — {{ $questionype->description }}</option>
                                @endforeach
                            </select>
                        </div>
                        <button type="button" class="remove text-red-600 font-bold">X</button>
                    </div>
                </div>

                <button type="button" id="addRow" class="text-blue-600 mb-3">+ Add Row</button>

                <div class="text-right">
                    <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded">Save</button>
                </div>
            </form>
        </div>

        <!-- EXCEL FORM -->
        <div data-content="excel" class="hidden">
            <form id="excelForm" method="POST" enctype="multipart/form-data" action="{{ route('question.store') }}">
                @csrf
                <input type="file" name="file" class="border p-2 w-full mb-3" required>
                <input type="hidden" name="id_groups" value="{{ $groups->id }}">
                <div class="text-right">
                    <a href="{{ route('units.export') }}" class="bg-blue-600 text-white px-4 py-2 me-5 rounded">Download Excel Template</a>
                    <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded">Import</button>
                </div>
            </form>
        </div>

    </div>
</template>