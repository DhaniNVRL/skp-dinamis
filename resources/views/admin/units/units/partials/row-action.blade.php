<tr
    class="unit-row transition hover:bg-gray-50"
    data-unit-name="{{ strtolower($unit->name) }}"
>
    <td class="px-4 py-3 text-center">
        <input
            type="checkbox"
            name="unit_ids[]"
            value="{{ $unit->id }}"
            class="unit-checkbox h-4 w-4 rounded
                border-gray-300 text-blue-600
                focus:ring-blue-500"
        >
    </td>

    <td class="unit-number px-4 py-3 text-sm text-gray-500">
        {{ $number }}
    </td>

    <td class="px-4 py-3">
        <span class="text-sm font-medium text-gray-800">
            {{ $unit->name }}
        </span>
    </td>

    <td class="px-4 py-3">
        <div class="flex items-center justify-center gap-3">

            <a
                href="{{ route('admin.subunit', [
                    'id' => $unit->id,
                    'tab' => 'subunit',
                ]) }}"
                class="inline-flex items-center justify-center
                    text-sky-600 transition hover:text-sky-800"
                title="Buka Sub Unit"
                aria-label="Buka Sub Unit"
            >
                <i class="fa-solid fa-arrow-right-long"></i>
            </a>
            {{-- EDIT --}}
            <button
                type="button"
                data-modal-open="editUnitModal"
                data-id="{{ $unit->id }}"
                data-name="{{ $unit->name }}"
                data-action="{{ route('units.update', [
                    'id' => $unit->id,
                ]) }}"
                title="Edit Unit"
                class="text-amber-500 transition hover:text-amber-700"
            >
                <i class="fa-solid fa-pen-to-square"></i>
            </button>

            {{-- DELETE --}}
            <button
                type="button"
                data-modal-open="deleteUnitModal"
                data-id="{{ $unit->id }}"
                data-name="{{ $unit->name }}"
                data-action="{{ route('units.destroy', [
                    'id' => $unit->id,
                ]) }}"
                title="Hapus Unit"
                class="text-red-500 transition hover:text-red-700"
            >
                <i class="fa-solid fa-trash"></i>
            </button>
        </div>
    </td>
</tr>