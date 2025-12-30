@extends('admin.layouts.app')

@section('title', 'Edit Unit')

@section('content')
<div class="max-w-xl mx-auto mt-10 bg-white shadow-md rounded px-8 pt-6 pb-8">
  <h2 class="text-2xl font-semibold mb-6 text-center">Edit Unit</h2>

  <form action="{{ route('units.update', $unit->id) }}" method="POST">
    @csrf
    @method('PUT')

    <div class="mb-4">
      <label for="name" class="block text-gray-700 font-bold mb-2">Nama Unit:</label>
      <input type="text" name="name" id="name" value="{{ old('name', $unit->name) }}"
             class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" required>
      @error('name')
        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
      @enderror
    </div>

    <div class="flex justify-between items-center">
      <a href="{{ route('admin.units', $group->id) }}">
          Batal
      </a>
      <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded">
        Simpan Perubahan
      </button>
    </div>
  </form>
</div>
@endsection
