<form action="{{ route('forms.update', $form->id) }}" method="POST" class="space-y-4">
    @csrf
    @method('PUT')

    <div>
        <label class="block font-semibold">Nama Form</label>
        <input type="text" name="name" value="{{ old('name', $form->name) }}"
               class="border rounded px-3 py-2 w-full">
    </div>

    <div>
        <label class="block font-semibold">Jenis Form</label>
        <select name="id_formtype" class="border rounded px-3 py-2 w-full">
            @foreach($formtypes as $type)
                <option value="{{ $type->id }}"
                    {{ $type->id == $form->id_formtype ? 'selected' : '' }}>
                    {{ $type->name }}
                </option>
            @endforeach
        </select>
    </div>

    <button type="submit"
            class="px-4 py-2 bg-green-500 text-white rounded hover:bg-green-600">
        Simpan Perubahan
    </button>
</form>