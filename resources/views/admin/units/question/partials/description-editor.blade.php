<div
    class="description-editor-wrapper"
    data-editor-wrapper
>
    {{-- Toolbar --}}
    <div
        class="flex flex-wrap items-center gap-2
               rounded-t-lg border border-gray-300 bg-gray-100 p-2"
    >
        <button
            type="button"
            data-description-command="bold"
            class="rounded px-3 py-1.5 font-bold hover:bg-gray-200"
        >
            B
        </button>

        <button
            type="button"
            data-description-command="italic"
            class="rounded px-3 py-1.5 italic hover:bg-gray-200"
        >
            I
        </button>

        <button
            type="button"
            data-description-command="underline"
            class="rounded px-3 py-1.5 underline hover:bg-gray-200"
        >
            U
        </button>

        <span class="mx-1 h-6 w-px bg-gray-300"></span>

        <select
            data-description-format
            class="rounded border border-gray-300 bg-white px-3 py-1.5 text-sm"
        >
            <option value="p">
                Paragraph
            </option>

            <option value="h2">
                Heading 2
            </option>

            <option value="h3">
                Heading 3
            </option>
        </select>

        <button
            type="button"
            data-description-command="insertUnorderedList"
            class="rounded px-3 py-1.5 hover:bg-gray-200"
        >
            <i class="fa-solid fa-list-ul mr-1"></i>
            List
        </button>

        <button
            type="button"
            data-description-command="insertOrderedList"
            class="rounded px-3 py-1.5 hover:bg-gray-200"
        >
            <i class="fa-solid fa-list-ol mr-1"></i>
            Number
        </button>

        <span class="mx-1 h-6 w-px bg-gray-300"></span>

        <button
            type="button"
            data-description-table
            class="inline-flex items-center gap-2 rounded
                   px-3 py-1.5 hover:bg-gray-200"
        >
            <i class="fa-solid fa-table"></i>
            Table
        </button>
    </div>

    {{-- Pengaturan table --}}
    <div
        data-table-settings
        class="hidden border-x border-gray-300 bg-indigo-50 p-4"
    >
        <div class="flex flex-wrap items-end gap-4">

            {{-- Row --}}
            <div>
                <label
                    for=""
                    class="mb-1 block text-xs font-semibold text-gray-600"
                >
                    Jumlah Row
                </label>

                <input
                    type="number"
                    data-table-rows
                    min="1"
                    max="20"
                    value="2"
                    class="w-24 rounded-lg border border-gray-300
                           bg-white px-3 py-2 text-sm"
                >
            </div>

            {{-- Column --}}
            <div>
                <label
                    class="mb-1 block text-xs font-semibold text-gray-600"
                >
                    Jumlah Column
                </label>

                <input
                    type="number"
                    data-table-columns
                    min="1"
                    max="10"
                    value="2"
                    class="w-24 rounded-lg border border-gray-300
                           bg-white px-3 py-2 text-sm"
                >
            </div>

            {{-- Border --}}
            <div>
                <label
                    class="mb-1 block text-xs font-semibold text-gray-600"
                >
                    Tebal Garis
                </label>

                <select
                    data-table-border
                    class="w-32 rounded-lg border border-gray-300
                           bg-white px-3 py-2 text-sm"
                >
                    <option value="0">
                        0 - Tanpa garis
                    </option>

                    <option value="1" selected>
                        1 - Tipis
                    </option>

                    <option value="2">
                        2 - Tebal
                    </option>
                </select>
            </div>

            {{-- Apply --}}
            <button
                type="button"
                data-table-apply
                class="inline-flex items-center gap-2 rounded-lg
                       bg-indigo-600 px-4 py-2 text-sm font-medium
                       text-white hover:bg-indigo-700"
            >
                <i class="fa-solid fa-check"></i>

                <span data-table-apply-label>
                    Buat Table
                </span>
            </button>

            {{-- Delete --}}
            <button
                type="button"
                data-table-delete
                class="hidden inline-flex items-center gap-2
                       rounded-lg bg-red-600 px-4 py-2
                       text-sm font-medium text-white hover:bg-red-700"
            >
                <i class="fa-solid fa-trash"></i>
                Hapus Table
            </button>

            {{-- Cancel --}}
            <button
                type="button"
                data-table-cancel
                class="rounded-lg border border-gray-300 bg-white
                       px-4 py-2 text-sm font-medium
                       text-gray-700 hover:bg-gray-50"
            >
                Batal
            </button>

        </div>

        <p class="mt-3 text-xs text-gray-500">
            Klik cell tabel lalu tekan Table untuk mengubah tabel yang sudah ada.
        </p>
    </div>

    {{-- Editor --}}
    <div
        id="{{ $editorId }}"
        data-description-editor
        contenteditable="true"
        spellcheck="true"
        class="description-editor min-h-[350px]
               overflow-x-auto rounded-b-lg
               border border-t-0 border-gray-300
               bg-white p-4 text-gray-700
               focus:outline-none"
    ></div>

    {{-- Hidden input --}}
    <textarea
        id="{{ $contentInputId }}"
        data-description-input
        name="content"
        class="hidden"
    ></textarea>
</div>

@once
    <style>
        .description-editor:empty::before {
            content: "Mulai mengetik di sini...";
            color: #9ca3af;
            pointer-events: none;
        }

        .description-editor table,
        .description-content table {
            width: 100%;
            margin: 16px 0;
            border-collapse: collapse;
            table-layout: auto;
        }

        .description-editor td,
        .description-editor th,
        .description-content td,
        .description-content th {
            min-width: 100px;
            padding: 10px;
            vertical-align: top;
        }

        .description-editor td,
        .description-editor th {
            cursor: text;
        }

        .description-editor table.description-table-selected {
            outline: 2px solid #6366f1;
            outline-offset: 3px;
        }

        .description-editor td:focus,
        .description-editor th:focus {
            background-color: #eef2ff;
            outline: 2px solid #818cf8;
            outline-offset: -2px;
        }

        .description-editor h2,
        .description-content h2 {
            margin: 16px 0 12px;
            font-size: 24px;
            line-height: 32px;
            font-weight: 700;
        }

        .description-editor h3,
        .description-content h3 {
            margin: 16px 0 8px;
            font-size: 20px;
            line-height: 28px;
            font-weight: 600;
        }

        .description-editor ul,
        .description-content ul {
            margin: 12px 0;
            padding-left: 24px;
            list-style: disc;
        }

        .description-editor ol,
        .description-content ol {
            margin: 12px 0;
            padding-left: 24px;
            list-style: decimal;
        }
    </style>
@endonce