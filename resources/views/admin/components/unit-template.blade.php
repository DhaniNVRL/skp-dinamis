<template id="unit-template">

    <!-- TABS -->
    <div class="flex border-b">

        <button
            type="button"
            class="tab-btn flex-1 py-2 border-b-2 border-blue-600 text-blue-600"
            data-tab="manual"
        >
            Manual Input
        </button>

        <button
            type="button"
            class="tab-btn flex-1 py-2"
            data-tab="excel"
        >
            Import Excel
        </button>

    </div>

    <!-- CONTENT -->
    <div class="p-4">

        <!-- ===================================== -->
        <!-- MANUAL INPUT -->
        <!-- ===================================== -->
        <div data-content="manual">

            <form id="manualForm" method="POST">

                @csrf

                <input type="hidden" name="id_groups">

                <!-- ROWS -->
                <div id="rows">

                    <div class="row flex gap-2 mb-2">

                        <div class="w-full">

                            <input
                                type="text"
                                name="name[]"
                                placeholder="Enter unit name"
                                class="border p-2 w-full"
                                required
                            >

                        </div>

                        <button
                            type="button"
                            class="remove text-red-600 font-bold"
                        >
                            X
                        </button>

                    </div>

                </div>

                <!-- ADD ROW -->
                <button
                    type="button"
                    id="addRow"
                    class="text-blue-600 mb-3"
                >
                    + Add Another Unit
                </button>

                <!-- ACTION -->
                <div class="text-right">

                    <button
                        type="submit"
                        class="bg-blue-600 text-white px-4 py-2 rounded"
                    >
                        Save Units
                    </button>

                </div>

            </form>

        </div>

        <!-- ===================================== -->
        <!-- IMPORT EXCEL -->
        <!-- ===================================== -->
        <div data-content="excel" class="hidden">

            <form
                id="excelForm"
                method="POST"
                enctype="multipart/form-data"
            >

                @csrf

                <input type="hidden" name="id_groups">

                <input
                    type="file"
                    name="file"
                    class="border p-2 w-full mb-3"
                    required
                >

                <!-- ACTION -->
                <div class="text-right">

                    <a
                        href="{{ route('units.export') }}"
                        class="bg-blue-600 text-white px-4 py-2 me-5 rounded"
                    >
                        Download Template
                    </a>

                    <button
                        type="submit"
                        class="bg-green-600 text-white px-4 py-2 rounded"
                    >
                        Import Excel
                    </button>

                </div>

            </form>

        </div>

    </div>

</template>