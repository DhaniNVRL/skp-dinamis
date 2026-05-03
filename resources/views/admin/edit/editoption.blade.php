@extends('admin.layouts.app')

@section('title', 'Edit Option')

@section('content')
<div class="max-w-xl mx-auto mt-10 bg-white shadow-md rounded px-8 pt-6 pb-8">
  <h2 class="text-2xl font-semibold mb-6 text-center">Edit Option</h2>

  <form action="{{ route('options.update', $option->id) }}" method="POST" class="space-y-4">
    @csrf
    @method('PUT')

    <!-- ID -->
    <div class="mb-4">
      <label class="block text-gray-700 font-bold mb-2">ID Option:</label>
      <input type="text" value="{{ $option->id }}"
             class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 bg-gray-100"
             readonly>
    </div>

    <!-- QUESTION -->
    <div class="mb-4">
      <label class="block text-gray-700 font-bold mb-2">Question ID:</label>
      <input type="text" value="{{ $option->question_id }}"
             class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 bg-gray-100"
             readonly>
    </div>

    <!-- ANSWER TEXT -->
    <div class="mb-4">
      <label for="answer_text" class="block text-gray-700 font-bold mb-2">Jawaban:</label>
      <input type="text" name="answer_text" id="answer_text"
             value="{{ old('answer_text', $option->answer_text) }}"
             class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 focus:outline-none focus:shadow-outline"
             required>

      @error('answer_text')
        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
      @enderror
    </div>

    <!-- HAS CHILD -->
    <div class="mb-4">
      <label class="block text-gray-700 font-bold mb-2">Input Tambahan</label>

      <select name="has_child"
              class="border rounded px-4 py-2 w-full">
        <option value="0" {{ $option->has_child == 0 ? 'selected' : '' }}>
            Tidak
        </option>
        <option value="1" {{ $option->has_child == 1 ? 'selected' : '' }}>
            Ya
        </option>
      </select>
    </div>

    <!-- ACTION -->
    <div class="flex justify-between items-center">
      <a href="{{ url()->previous() }}"
         class="bg-gray-300 hover:bg-gray-400 text-gray-800 px-4 py-2 rounded">
        Batal
      </a>

      <button type="submit"
              class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded">
        Simpan Perubahan
      </button>
    </div>

  </form>
</div>
@endsection
