<div
    id="globalModal"
    class="hidden fixed inset-0 z-50 bg-black bg-opacity-50 items-center justify-center"
>
    <div class="bg-white w-full max-w-xl rounded shadow-lg">

        <!-- HEADER -->
        <div class="flex justify-between items-center px-4 py-3 border-b">
            <h2 id="modalTitle" class="text-lg font-semibold"></h2>
            <button data-close class="text-gray-600 hover:text-black text-xl">&times;</button>
        </div>

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
                <form id="manualForm" method="POST">
                    @csrf
                    <input type="hidden" id="groupId" name="id_groups">

                    <div id="rows">
                        <div class="row flex gap-2 mb-2">
                            <input type="text" name="name[]" class="border p-2 w-full" required>
                            <button type="button" class="remove text-red-600 font-bold">X</button>
                        </div>
                    </div>

                    <button type="button" id="addRow" class="text-blue-600 mb-3">
                        + Add Row
                    </button>

                    <div class="text-right">
                        <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded">
                            Save
                        </button>
                    </div>
                </form>
            </div>

            <!-- EXCEL -->
            <div data-content="excel" class="hidden">
                <form id="excelForm" method="POST" enctype="multipart/form-data">
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
    </div>
</div>
