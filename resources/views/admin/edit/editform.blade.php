@extends('admin.layouts.app')

@section('title', 'Edit Activity')

@section('content')
<div class="max-w-xl mx-auto mt-10 bg-white shadow-md rounded px-8 pt-6 pb-8">
  <h2 class="text-2xl font-semibold mb-6 text-center">Edit Kegiatan</h2>

  <form action="{{ route('forms.update', $form->id) }}" method="POST" class="space-y-4">
    @csrf
    @method('PUT')

    <div class="mb-4">
      <label for="id" class="block text-gray-700 font-bold mb-2">ID Form:</label>
      <input type="text" id="id" value="{{ $form->id }}"
             class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" readonly>
    </div>

    <div class="mb-4">
      <label for="name" class="block text-gray-700 font-bold mb-2">Nama Form:</label>
      <input type="text" name="name" id="name" value="{{ old('name', $form->name) }}"
             class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" required>
      @error('name')
        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
      @enderror
    </div>

    <div class="mb-4">
      <label for="id_formtype" class="block text-gray-700 font-bold mb-2">Jenis Form</label>
      <select name="id_formtype" required class="validate-required activity-dropdown border rounded px-4 py-2 w-full">
        <option value="">Choose Activity</option>
           @foreach($formtypes as $type)
                <option value="{{ $type->id }}"
                    {{ $type->id == $form->id_formtype ? 'selected' : '' }}>
                    {{ $type->name }} - {{ $type->description }}
                </option>
            @endforeach
      </select>
      @error('name')
        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
      @enderror
    </div>

    <div class="flex justify-between items-center">
       <a href="{{ route('admin.units', $form->id_groups) }}?tab=questions"  
            class="bg-gray-300 hover:bg-gray-400 text-gray-800 px-4 py-2 rounded">
            Batal
        </a>
      <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded">
        Simpan Perubahan
      </button>
    </div>
  </form>
</div>
@endsection
